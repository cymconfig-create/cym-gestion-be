<?php

namespace App\Repositories;

use App\Models\Conversation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ConversationRepository
{
    public function create(array $data): Conversation
    {
        return Conversation::create($data);
    }

    public function find(int $id): ?Conversation
    {
        return Conversation::find($id);
    }

    public function getConversationsForUser(int $userId): Collection
    {
        return Conversation::whereHas('users', function ($query) use ($userId) {
            $query->where('conversation_user.user_id', $userId);
        })
        ->with(['users', 'messages' => function ($query) {
            // Se ordena los mensajes por fecha de creación de forma desc
            $query->orderBy('created_at', 'desc');
        }])
        ->get();
    }

    public function addParticipant(int $conversationId, int $userId): void
    {
        $conversation = $this->find($conversationId);
        if ($conversation) {
            $conversation->users()->attach($userId);
        }
    }

    public function markAsRead(int $conversationId, int $userId): void
    {
        $conversation = $this->find($conversationId);
        if ($conversation) {
            $conversation->users()->updateExistingPivot($userId, [
                'last_read_at' => now(),
            ]);
        }
    }

    public function markAsArchived(int $conversationId, int $userId): void
    {
        $conversation = $this->find($conversationId);
        if ($conversation) {
            $conversation->users()->updateExistingPivot($userId, [
                'is_archived' => true,
            ]);
        }
    }

    public function updateUserConversationStatus(int $conversationId, int $userId, array $updates)
    {
        DB::table('conversation_user')
            ->where('conversation_id', $conversationId)
            ->where('user_id', $userId)
            ->update($updates);
    }
}
