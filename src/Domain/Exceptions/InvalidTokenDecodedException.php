<?php

declare(strict_types=1);

namespace CognitoTokenVerifier\Domain\Exceptions;

use Exception;

final class InvalidTokenDecodedException extends Exception
{
    public function __construct()
    {
        parent::__construct(sprintf('The token have an incorrect format or is not valid.'), 401);
    }

    public function getStatusCode() : int
    {
        return 401;
    }
}
