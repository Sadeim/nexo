<?php

namespace App\Services\NexoPos\Payment\Exceptions;

class PlutoPayException extends \RuntimeException
{
    public ?string $providerCode = null;

    public static function fromResponse(string $message, ?string $code = null): self
    {
        $e = new self($message);
        $e->providerCode = $code;
        return $e;
    }
}
