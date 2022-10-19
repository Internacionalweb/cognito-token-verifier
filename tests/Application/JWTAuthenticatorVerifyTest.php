<?php

namespace JwtCognitoSignature\Tests\Application;

use DG\BypassFinals;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use JwtCognitoSignature\JWT\Application\JWTAuthenticatorVerify;

final class JWTAuthenticatorVerifyTest extends TestCase
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
        $mockAuthenticator = $this->createMock(JWTAuthenticatorVerify::class);
        $mockAuthenticator->method('__invoke')->willThrowException(new InvalidArgumentException());

        /**
         * @var JWTAuthenticatorVerify $mockAuthenticator
         */
        $mockAuthenticator->__invoke('token', []);
    }
}