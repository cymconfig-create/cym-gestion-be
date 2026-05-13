<?php

namespace Tests\Unit;

use App\Services\Service;
use App\Util\Constants;
use Tests\TestCase;

class ServiceResponseTest extends TestCase
{
    public function test_resolve_hides_internal_error_details_for_server_errors(): void
    {
        $service = new Service();

        $response = $service->resolve(true, 'SQLSTATE[HY000] sensitive detail', ['raw' => 'detail'], Constants::CODE_INTERNAL_SERVER_ERROR);
        $content = $response->getData(true);

        $this->assertTrue($content['error']);
        $this->assertSame(Constants::INTERNAL_SERVER_ERROR_MESSAGE, $content['message']);
        $this->assertNull($content['data']);
        $this->assertSame(Constants::CODE_INTERNAL_SERVER_ERROR, $content['status']);
    }
}

