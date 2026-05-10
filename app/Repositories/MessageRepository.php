<?php

namespace App\Repositories;

use App\Ia\Mongo\MongoClientFactory;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

class MessageRepository
{
    private function collection(): Collection
    {
        return MongoClientFactory::database()->selectCollection('messages');
    }

    private function map(?object $doc): ?object
    {
        if (!$doc) return null;
        $row = (array) $doc;
        unset($row['_id']);
        return (object) $row;
    }

    public function nextMessageId(): int
    {
        $last = $this->collection()->findOne([], ['sort' => ['message_id' => -1], 'projection' => ['message_id' => 1]]);
        return $last ? ((int) $last->message_id + 1) : 1;
    }

    public function create(array $data): object
    {
        $id = $this->nextMessageId();
        $now = new UTCDateTime((int) (microtime(true) * 1000));
        $data['message_id'] = $id;
        $data['created_at'] = $data['created_at'] ?? $now;
        $data['updated_at'] = $data['updated_at'] ?? $now;
        $this->collection()->insertOne($data);
        return $this->find($id);
    }

    public function getConversationMessages(int $conversationId)
    {
        $docs = $this->collection()->find(['conversation_id' => $conversationId], ['sort' => ['created_at' => 1]]);
        $rows = [];
        foreach ($docs as $doc) $rows[] = $this->map($doc);
        return collect($rows);
    }

    public function find(int $messageId): ?object
    {
        return $this->map($this->collection()->findOne(['message_id' => $messageId]));
    }
}
