<?php

namespace App\Repositories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class MessageRepository
{
    public function create(array $data): Message
    {
        return Message::create($data);
    }

    public function getConversationMessages(int $conversationId): Collection
    {
        return Message::where('conversation_id', $conversationId)->orderBy('created_at', 'asc')->get();
    }

    public function find(int $messageId): ?Message
    {
        return Message::find($messageId);
    }
}
