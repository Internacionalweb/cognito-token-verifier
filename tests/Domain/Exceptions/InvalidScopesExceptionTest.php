<?php

declare(strict_types=1);

namespace CognitoTokenVerifier\Tests\Domain\Exceptions;

use PHPUnit\Framework\TestCase;
use CognitoTokenVerifier\Domain\Exceptions\InvalidScopesException;

final class InvalidScopesExceptionTest extends TestCase
{
    /** @test */
    public function it_should_return_status_code(): void
    {
        $this->assertSame(401, (new InvalidScopesException())->getStatusCode());
    }
}
