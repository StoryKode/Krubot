<?php

declare(strict_types=1);

namespace KrubiK\WebApps\Exceptions;

use Exception;

/**
 * Thrown when the hash of the WebApp initData does not match the calculated signature.
 * This indicates that the data may have been tampered with or is not authentic.
 */
class InvalidSignatureException extends Exception
{
    public function __construct(string $message = "WebApp initData signature is invalid.", int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
