<?php

namespace App\Services\Pos\Exceptions;

use RuntimeException;

/**
 * Thrown by CartCalculator when a price or quantity is invalid, or when the
 * cart cannot produce a valid total (e.g. empty cart).
 */
class InvalidPriceException extends RuntimeException
{
}
