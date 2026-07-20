<?php

namespace App\Services\Pos\Payment\Exceptions;

use RuntimeException;

/**
 * Thrown for any PlutoPay API / configuration failure (network, non-2xx,
 * or the TEST-MODE guard rejecting a non sk_test_ key).
 */
class PlutoPayException extends RuntimeException
{
    public ?string $errorCode = null;

    public static function fromResponse(string $message, ?string $code = null): self
    {
        $e = new self($message);
        $e->errorCode = $code;

        return $e;
    }
}
