<?php

namespace CognitoTokenVerifier\Tests\Application;

use DG\BypassFinals;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use CognitoTokenVerifier\Application\Verifier;

final class VerifierTest extends TestCase
{
    public function setUp(): void
    {
        BypassFinals::enable();
    }

    /** @test */
    public function whenTokenIsMalformed(): void
    {
        $this->expectException(InvalidArgumentException::class);

        /**
         * @var \PHPUnit\Framework\MockObject\MockObject $mockAuthenticator
         */
        $mockAuthenticator = $this->createMock(Verifier::class);
        $mockAuthenticator->method('__invoke')->willThrowException(new InvalidArgumentException());

        /**
         * @var Verifier $mockAuthenticator
         */
        $mockAuthenticator->__invoke('token', []);
    }
}
