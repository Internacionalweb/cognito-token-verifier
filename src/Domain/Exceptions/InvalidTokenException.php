<?php

declare(strict_types=1);

namespace CognitoTokenVerifier\Domain\Exceptions;

use Exception;

final class InvalidTokenException extends Exception
{
    public function __construct()
    {
        parent::__construct('The token not contain required parameters', 401);
    }

    public function getStatusCode() : int
    {
        return 401;
    }
}
