<?php

namespace App\Services\Messaging;

use App\Repositories\ConversationRepository;
use App\Repositories\MessageRepository;
use App\Services\Service;
use App\Util\Constants;
use App\Util\MessagingConstants;
use Illuminate\Support\Facades\DB;
use App\Models\Message;
use App\Services\Shared\ErrorResponseFormatter;
use Illuminate\Validation\ValidationException;
use App\Services\Shared\ValidatorService;

class MessagingService extends Service
{
    private $conversationRepository;
    private $messageRepository;
    private $validatorService;
    private $errorResponseFormatter;

    public function __construct(
        ConversationRepository $conversationRepository,
        MessageRepository $messageRepository,
        ValidatorService $validatorService,
        ErrorResponseFormatter $errorResponseFormatter
    ) {
        $this->conversationRepository = $conversationRepository;
        $this->messageRepository = $messageRepository;
        $this->validatorService = $validatorService;
        $this->errorResponseFormatter = $errorResponseFormatter;
    }

    /**
     * Crea una nueva conversación y el mensaje inicial.
     *
     * @param array $data
     * @return array
     */
    public function createConversation(array $data)
    {
        $model = new Message();

        try {
            // Validamos usando las reglas del modelo de Mensaje para una conversación inicial.
            $this->validatorService->validate($data, $model->getRulesCreate(true));
        } catch (ValidationException $e) {
            $messageError = $this->errorResponseFormatter->formatValidationErrors($e);
            return $this->resolve(
                true,
                $messageError,
                Constants::NOT_DATA,
                Constants::CODE_UNPROCESSABLE_ENTITY
            );
        }

        DB::beginTransaction();
        try {
            $user = auth()->user();
            $conversations = [];

            foreach ($data[MessagingConstants::PARTICIPANTS] as $participantId) {
                // 1. Crear conversación para ESTE participante
                $conversation = $this->conversationRepository->create([
                    MessagingConstants::COMPANY_ID => $user->company_id,
                    MessagingConstants::SUBJECT => $data[MessagingConstants::SUBJECT],
                    MessagingConstants::TYPE => $data[MessagingConstants::TYPE],
                    MessagingConstants::CREATED_BY => $user->user_id,
                    MessagingConstants::LAST_MESSAGE_AT => now(),
                ]);

                // 2. Mensaje inicial
                $message = $this->messageRepository->create([
                    MessagingConstants::CONVERSATION_ID => $conversation->conversation_id,
                    MessagingConstants::USER_ID => $user->user_id,
                    MessagingConstants::BODY => $data[MessagingConstants::BODY],
                ]);

                // 3. Asociar usuarios: remitente + este participante
                $conversation->users()->attach([$user->user_id, $participantId]);

                // 4. Opcional: cargar relaciones para la respuesta
                $conversation->load('user');
                if ($conversation->user) {
                    $conversation->user->makeHidden([
                        MessagingConstants::CREATED_BY,
                        MessagingConstants::UPDATED_BY,
                        MessagingConstants::CREATED_AT,
                        MessagingConstants::UPDATED_AT,
                        MessagingConstants::PROFILE_ID,
                        MessagingConstants::STATUS
                    ]);
                }

                $conversations[] = $conversation;
            }

            DB::commit();

            return $this->resolve(
                false,
                MessagingConstants::CONVERSATION_CREATED,
                $conversations,
                Constants::CODE_CREATED
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resolve(
                true,
                $e->getMessage(),
                Constants::NOT_DATA,
                Constants::CODE_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Agrega una respuesta a una conversación existente.
     *
     * @param int $conversationId
     * @param array $data
     * @return array
     */
    public function replyToConversation(int $conversationId, array $data)
    {
        $conversation = $this->conversationRepository->find($conversationId);
        if (!$conversation) {
            return $this->resolve(
                true,
                MessagingConstants::NOT_FOUND,
                Constants::NOT_DATA,
                Constants::CODE_SUCCESS_NO_CONTENT
            );
        }

        try {
            $model = new Message();
            $this->validatorService->validate($data, $model->getRulesCreate());
        } catch (ValidationException $e) {
            $messageError = $this->errorResponseFormatter->formatValidationErrors($e);
            return $this->resolve(
                true,
                $messageError,
                Constants::NOT_DATA,
                Constants::CODE_UNPROCESSABLE_ENTITY
            );
        }

        DB::beginTransaction();
        try {
            // 1. Crear el nuevo mensaje
            $message = $this->messageRepository->create([
                MessagingConstants::CONVERSATION_ID => $conversationId,
                MessagingConstants::USER_ID => auth()->user()->user_id,
                MessagingConstants::BODY => $data[MessagingConstants::BODY],
            ]);

            // 2. Actualizar el timestamp de la conversación
            $conversation->last_message_at = now();
            $conversation->save();

            // 3. Marcar la conversación como leída para el usuario actual
            $this->conversationRepository->markAsRead($conversationId, auth()->user()->user_id);

            DB::commit();

            return $this->resolve(
                false,
                MessagingConstants::MESSAGE_SENT,
                $message,
                Constants::CODE_SUCCESS
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resolve(
                true,
                $e->getMessage(),
                Constants::NOT_DATA,
                Constants::CODE_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Actualiza el estado de una conversación.
     *
     * @param int $conversationId
     * @param string $status
     * @return array
     */
    public function updateConversationStatus(int $conversationId, string $status)
    {
        $conversation = $this->conversationRepository->find($conversationId);

        if (!$conversation) {
            return $this->resolve(
                true,
                MessagingConstants::NOT_FOUND,
                Constants::NOT_DATA,
                Constants::CODE_SUCCESS_NO_CONTENT
            );
        }

        // Validación para asegurar que el estado es válido
        if (!in_array($status, [MessagingConstants::STATUS_OPEN, MessagingConstants::STATUS_CLOSED, MessagingConstants::STATUS_PENDING])) {
            return $this->resolve(
                true,
                MessagingConstants::INVALID_STATUS,
                Constants::NOT_DATA,
                Constants::CODE_BAD_REQUEST
            );
        }

        DB::beginTransaction();
        try {
            $conversation->status = $status;
            $conversation->save();
            DB::commit();

            return $this->resolve(
                false,
                MessagingConstants::UPDATED,
                $conversation,
                Constants::CODE_SUCCESS
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->resolve(
                true,
                $e->getMessage(),
                Constants::NOT_DATA,
                Constants::CODE_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Marca una conversación como archivada para el usuario actual.
     * @param int $conversationId
     * @param int $userId
     * @return array
     */
    public function archiveConversation(int $conversationId, int $userId)
    {
        $this->conversationRepository->updateUserConversationStatus($conversationId, $userId, [MessagingConstants::IS_ARCHIVED => true]);
        return $this->resolve(
            false,
            MessagingConstants::CONVERSATION_ARCHIVED,
            Constants::NOT_DATA,
            Constants::CODE_SUCCESS
        );
    }

    /**
     * Restaura una conversación archivada para el usuario actual.
     * @param int $conversationId
     * @param int $userId
     * @return array
     */
    public function unarchiveConversation(int $conversationId, int $userId)
    {
        $this->conversationRepository->updateUserConversationStatus($conversationId, $userId, [MessagingConstants::IS_ARCHIVED => false]);
        return $this->resolve(
            false,
            MessagingConstants::CONVERSATION_UNARCHIVED,
            Constants::NOT_DATA,
            Constants::CODE_SUCCESS
        );
    }

    /**
     * Marca una conversación como eliminada para el usuario actual (borrado suave).
     * @param int $conversationId
     * @param int $userId
     * @return array
     */
    public function deleteConversation(int $conversationId, int $userId)
    {
        $this->conversationRepository->updateUserConversationStatus($conversationId, $userId, [MessagingConstants::IS_DELETED => true]);
        return $this->resolve(
            false,
            MessagingConstants::CONVERSATION_DELETED,
            Constants::NOT_DATA,
            Constants::CODE_SUCCESS
        );
    }

    /**
     * Restaura una conversación eliminada para el usuario actual.
     * @param int $conversationId
     * @param int $userId
     * @return array
     */
    public function restoreConversation(int $conversationId, int $userId)
    {
        $this->conversationRepository->updateUserConversationStatus($conversationId, $userId, [MessagingConstants::IS_DELETED => false]);
        return $this->resolve(
            false,
            MessagingConstants::CONVERSATION_RESTORED,
            null,
            Constants::CODE_SUCCESS
        );
    }

    /**
     * Obtiene una conversación y la marca como leída para el usuario actual.
     *
     * @param int $conversationId
     * @return array
     */
    public function getConversation(int $conversationId)
    {
        $conversation = $this->conversationRepository->find($conversationId);

        if (!$conversation) {
            return $this->resolve(
                true,
                MessagingConstants::NOT_FOUND,
                Constants::NOT_DATA,
                Constants::CODE_SUCCESS_NO_CONTENT
            );
        }

        $userId = auth()->user()->user_id;

        // Verificar si el usuario autenticado está en la conversación
        if (!$conversation->users()->where(MessagingConstants::USERS_USER_ID, $userId)->exists()) {
            return $this->resolve(
                true,
                MessagingConstants::FORBIDDEN,
                Constants::NOT_DATA,
                Constants::CODE_FORBIDDEN
            );
        }

        $conversation->load('user', 'messages.user');
        $conversation->user->makeHidden([
            MessagingConstants::CREATED_BY,
            MessagingConstants::UPDATED_BY,
            MessagingConstants::CREATED_AT,
            MessagingConstants::UPDATED_AT
        ]);
        // Oculta los campos solo del objeto de usuario dentro de cada mensaje
        $conversation->messages->each(function ($message) {
            if ($message->user) {
                $message->user->makeHidden([
                    MessagingConstants::CREATED_BY,
                    MessagingConstants::UPDATED_BY,
                    MessagingConstants::CREATED_AT,
                    MessagingConstants::UPDATED_AT
                ]);
            }
        });
        $this->conversationRepository->markAsRead($conversationId, $userId);

        return $this->resolve(
            false,
            MessagingConstants::SUCCESSFUL_QUERY,
            $conversation,
            Constants::CODE_SUCCESS
        );
    }

    /**
     * Obtiene todas las conversaciones del usuario actual.
     *
     * @param int $userId
     * @return array
     */
    public function getConversationsForUser(int $userId)
    {
        // El servicio llama al repositorio para obtener los datos
        $conversations = $this->conversationRepository->getConversationsForUser($userId);

        // El servicio se encarga de dar formato a la respuesta
        $status = $conversations->isEmpty() ? Constants::CODE_SUCCESS_NO_CONTENT : null;

        return $this->resolve(
            false,
            MessagingConstants::SUCCESSFUL_QUERY,
            $conversations,
            $status
        );
    }
}
