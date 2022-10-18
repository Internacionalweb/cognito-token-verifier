<?php

namespace JwtCognitoSignature\JWT\Domain\Exceptions;

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
