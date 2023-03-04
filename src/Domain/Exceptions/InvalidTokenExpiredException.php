<?php

declare(strict_types=1);

namespace CognitoTokenVerifier\Domain\Exceptions;

use Exception;

final class InvalidTokenExpiredException extends Exception
{
    public function __construct()
    {
        parent::__construct('Token is expired', 401);
    }

    public function getStatusCode() : int
    {
        return 401;
    }
}
