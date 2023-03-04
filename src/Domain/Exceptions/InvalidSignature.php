<?php

declare(strict_types=1);

namespace CognitoTokenVerifier\Domain\Exceptions;

use Exception;

final class InvalidSignature extends Exception
{
    public function __construct()
    {
        parent::__construct('Invalid Signature', 401);
    }

    public function getStatusCode() : int
    {
        return 401;
    }
}
