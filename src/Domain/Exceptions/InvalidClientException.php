<?php

declare(strict_types=1);

namespace CognitoTokenVerifier\Domain\Exceptions;

use Exception;

final class InvalidClientException extends Exception
{
    private const STATUS_CODE = 401;

    public function __construct()
    {
        parent::__construct(sprintf('The client is not valid'), self::STATUS_CODE);
    }

    public function getStatusCode(): int
    {
        return self::STATUS_CODE;
    }
}
