<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Messaging\MessagingService;

class MessageController extends Controller
{
    private $messagingService;

    public function __construct(
        MessagingService $messagingService
    ) {
        $this->messagingService = $messagingService;
    }

    /**
     * Obtiene las conversaciones del usuario autenticado.
     */
    public function getMyConversations()
    {
        return $this->messagingService->getConversationsForUser(auth()->user()->user_id);
    }

    /**
     * Muestra una conversación específica.
     *
     * @param int $conversationId
     */
    public function show($conversationId)
    {
        return $this->messagingService->getConversation($conversationId);
    }

    /**
     * Crea una nueva conversación.
     *
     * @param Request $request
     */
    public function createConversation(Request $request)
    {
        return $this->messagingService->createConversation($request->all());
    }

    /**
     * Responde a una conversación.
     *
     * @param Request $request
     * @param int $conversationId
     */
    public function reply(Request $request, $conversationId)
    {
        return $this->messagingService->replyToConversation($conversationId, $request->all());
    }

    /**
     * Actualiza el estado de una conversación.
     *
     * @param Request $request
     * @param int $conversationId
     */
    public function updateStatus(Request $request, $conversationId)
    {
        $request->validate(['status' => 'required|in:open,closed,pending']);
        return $this->messagingService->updateConversationStatus($conversationId, $request->input('status'));
    }

    /**
     * Archiva una conversación.
     *
     * @param int $conversationId
     */
    public function archive($conversationId)
    {
        return $this->messagingService->archiveConversation($conversationId, auth()->user()->user_id);
    }

    /**
     * Desarchiva una conversación.
     *
     * @param int $conversationId
     */
    public function unarchive($conversationId)
    {
        return $this->messagingService->unarchiveConversation($conversationId, auth()->user()->user_id);
    }

    /**
     * Elimina una conversación.
     *
     * @param int $conversationId
     */
    public function delete($conversationId)
    {
        return $this->messagingService->deleteConversation($conversationId, auth()->user()->user_id);
    }

    /**
     * Restaura una conversación.
     *
     * @param int $conversationId
     */
    public function restore($conversationId)
    {
        return $this->messagingService->restoreConversation($conversationId, auth()->user()->user_id);
    }
}
