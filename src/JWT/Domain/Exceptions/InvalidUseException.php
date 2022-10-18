<?php

namespace JwtCognitoSignature\JWT\Domain\Exceptions;

use Exception;

final class InvalidUseException extends Exception
{
    public function __construct()
    {
        parent::__construct('The access token havent access', 401);
    }

    public function getStatusCode(): int
    {
        return 401;
    }
}
