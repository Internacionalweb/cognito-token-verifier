<?php

declare(strict_types=1);

namespace CognitoTokenVerifier\Domain\Exceptions;

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
