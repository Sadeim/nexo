<?php

namespace Tests\Unit\Pos;

use App\Services\Pos\Payment\Exceptions\PlutoPayException;
use App\Services\Pos\Payment\PlutoPayClient;
use Tests\TestCase;

/**
 * The TEST-MODE guard must make it impossible to construct a client (and thus
 * issue any request) with a non-test secret key.
 */
class PlutoPayClientGuardTest extends TestCase
{
    public function test_live_key_is_rejected_at_construction(): void
    {
        config()->set('services.plutopay.secret_key', 'sk_live_danger');

        $this->expectException(PlutoPayException::class);
        new PlutoPayClient();
    }

    public function test_missing_key_is_rejected(): void
    {
        config()->set('services.plutopay.secret_key', null);

        $this->expectException(PlutoPayException::class);
        new PlutoPayClient();
    }

    public function test_test_key_is_accepted(): void
    {
        config()->set('services.plutopay.secret_key', 'sk_test_ok');

        $this->assertInstanceOf(PlutoPayClient::class, new PlutoPayClient());
    }
}
