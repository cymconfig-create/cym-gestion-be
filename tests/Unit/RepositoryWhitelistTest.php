<?php

namespace Tests\Unit;

use App\Repositories\AttachmentRepository;
use App\Repositories\UserRepository;
use InvalidArgumentException;
use Tests\TestCase;

class RepositoryWhitelistTest extends TestCase
{
    public function test_user_repository_rejects_non_whitelisted_column(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $repository = new UserRepository();
        $repository->findBy('password', 'secret');
    }

    public function test_attachment_repository_rejects_non_whitelisted_column(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $repository = new AttachmentRepository();
        $repository->findBy('drop_table', 'value');
    }
}

