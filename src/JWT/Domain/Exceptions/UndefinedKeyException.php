<?php

declare(strict_types=1);

namespace JwtCognitoSignature\JWT\Domain\Exceptions;

use Exception;

final class UndefinedKeyException extends Exception
{
    public function __construct()
    {
        parent::__construct(sprintf('The App is not found into allowed apps'), 401);
    }

    public function getStatusCode() : int
    {
        return 401;
    }
}
