<?php

namespace JwtCognitoSignature\JWT\Domain\Exceptions;

use Exception;

final class InvalidScopesException extends Exception
{
    public function __construct()
    {
        parent::__construct(sprintf('Not permissions'), 401);
    }

    public function getStatusCode() : int
    {
        return 401;
    }
}
