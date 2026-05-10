<?php

namespace App\Repositories;

use App\Ia\Mongo\MongoClientFactory;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Collection;

class ConversationRepository
{
    private function conversations(): Collection
    {
        return MongoClientFactory::database()->selectCollection('conversations');
    }

    private function messages(): Collection
    {
        return MongoClientFactory::database()->selectCollection('messages');
    }

    private function pivots(): Collection
    {
        return MongoClientFactory::database()->selectCollection('conversation_user');
    }

    private function map(?object $doc): ?object
    {
        if (!$doc) return null;
        $row = (array) $doc;
        unset($row['_id']);
        return (object) $row;
    }

    public function nextConversationId(): int
    {
        $last = $this->conversations()->findOne([], ['sort' => ['conversation_id' => -1], 'projection' => ['conversation_id' => 1]]);
        return $last ? ((int) $last->conversation_id + 1) : 1;
    }

    public function create(array $data): object
    {
        $id = $this->nextConversationId();
        $now = new UTCDateTime((int) (microtime(true) * 1000));
        $data['conversation_id'] = $id;
        $data['status'] = $data['status'] ?? 'open';
        $data['created_at'] = $data['created_at'] ?? $now;
        $data['updated_at'] = $data['updated_at'] ?? $now;
        $this->conversations()->insertOne($data);
        return $this->find($id);
    }

    public function find(int $id): ?object
    {
        return $this->map($this->conversations()->findOne(['conversation_id' => $id]));
    }

    public function getConversationsForUser(int $userId)
    {
        $pivotDocs = $this->pivots()->find(['user_id' => $userId, 'is_deleted' => ['$ne' => true]]);
        $conversationIds = [];
        $pivotMap = [];
        foreach ($pivotDocs as $p) {
            $cid = (int) $p->conversation_id;
            $conversationIds[] = $cid;
            $pivotMap[$cid] = (array) $p;
        }
        if ($conversationIds === []) {
            return collect([]);
        }

        $convDocs = $this->conversations()->find(['conversation_id' => ['$in' => array_values(array_unique($conversationIds))]]);
        $rows = [];
        foreach ($convDocs as $c) {
            $conv = (array) $c;
            unset($conv['_id']);
            $cid = (int) $conv['conversation_id'];

            $msgs = [];
            $msgDocs = $this->messages()->find(['conversation_id' => $cid], ['sort' => ['created_at' => -1]]);
            foreach ($msgDocs as $m) {
                $msg = (array) $m;
                unset($msg['_id']);
                $msgs[] = (object) $msg;
            }

            $participants = [];
            $partDocs = $this->pivots()->find(['conversation_id' => $cid]);
            foreach ($partDocs as $pd) {
                $participants[] = (int) $pd->user_id;
            }

            $conv['messages'] = $msgs;
            $conv['participants'] = $participants;
            $conv['pivot'] = $pivotMap[$cid] ?? null;
            $rows[] = (object) $conv;
        }

        return collect($rows)->sortByDesc('last_message_at')->values();
    }

    public function addParticipant(int $conversationId, int $userId): void
    {
        $this->pivots()->updateOne(
            ['conversation_id' => $conversationId, 'user_id' => $userId],
            ['$setOnInsert' => [
                'conversation_id' => $conversationId,
                'user_id' => $userId,
                'is_archived' => false,
                'is_deleted' => false,
                'last_read_at' => null,
                'created_at' => new UTCDateTime((int) (microtime(true) * 1000)),
            ]],
            ['upsert' => true]
        );
    }

    public function addParticipants(int $conversationId, array $userIds): void
    {
        foreach ($userIds as $uid) {
            $this->addParticipant($conversationId, (int) $uid);
        }
    }

    public function markAsRead(int $conversationId, int $userId): void
    {
        $this->pivots()->updateOne(
            ['conversation_id' => $conversationId, 'user_id' => $userId],
            ['$set' => ['last_read_at' => new UTCDateTime((int) (microtime(true) * 1000))]]
        );
    }

    public function markAsArchived(int $conversationId, int $userId): void
    {
        $this->updateUserConversationStatus($conversationId, $userId, ['is_archived' => true]);
    }

    public function updateUserConversationStatus(int $conversationId, int $userId, array $updates)
    {
        $this->pivots()->updateOne(
            ['conversation_id' => $conversationId, 'user_id' => $userId],
            ['$set' => $updates]
        );
    }

    public function updateConversation(int $conversationId, array $updates): void
    {
        $updates['updated_at'] = new UTCDateTime((int) (microtime(true) * 1000));
        $this->conversations()->updateOne(['conversation_id' => $conversationId], ['$set' => $updates]);
    }

    public function userIsParticipant(int $conversationId, int $userId): bool
    {
        return $this->pivots()->countDocuments(['conversation_id' => $conversationId, 'user_id' => $userId, 'is_deleted' => ['$ne' => true]]) > 0;
    }
}
