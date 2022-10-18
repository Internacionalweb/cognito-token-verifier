<?php

namespace JwtCognitoSignature\JWT\Domain\Exceptions;

use Exception;

final class InvalidClientException extends Exception
{
    public function __construct()
    {
        parent::__construct(sprintf('The client_id is not valid'), 401);
    }

    public function getStatusCode() : int
    {
        return 401;
    }
}
