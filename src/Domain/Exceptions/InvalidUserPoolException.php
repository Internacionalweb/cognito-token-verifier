<?php

declare(strict_types=1);

namespace JwtCognitoSignature\Domain\Exceptions;

use Exception;

final class InvalidUserPoolException extends Exception
{
    public function __construct()
    {
        parent::__construct('UserPool is not correct', 401);
    }

    public function getStatusCode() : int
    {
        return 401;
    }
}
