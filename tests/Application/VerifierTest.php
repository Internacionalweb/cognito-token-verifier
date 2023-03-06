<?php

namespace CognitoTokenVerifier\Tests\Application;

use DG\BypassFinals;
use PHPUnit\Framework\TestCase;
use CognitoTokenVerifier\Application\Verifier;
use CognitoTokenVerifier\Domain\KeysRepository;
use CognitoTokenVerifier\Domain\Exceptions\InvalidTokenDecodedException;

final class VerifierTest extends TestCase
{
    private KeysRepository $keysRepositoryMock;
    private Verifier $verifier;

    public function setUp(): void
    {
        BypassFinals::enable();

        $this->keysRepositoryMock = $this->createMock(KeysRepository::class);
        $this->verifier = new Verifier($this->keysRepositoryMock);
    }

    /** @test */
    public function it_should_throw_an_invalid_token_decoded_exception_when_the_token_is_malformed(): void
    {
        $this->expectException(InvalidTokenDecodedException::class);
        $this->expectExceptionMessage('The token have an incorrect format or is not valid.');
        $this->expectExceptionCode(401);

        $this->verifier->__invoke('token', []);
        
    }

    /** @test */
    public function it_should_throw_an_invalid_token_decoded_exception_when_the_token_is_not_a_string(): void
    {
        $this->expectException(InvalidTokenDecodedException::class);
        $this->expectExceptionMessage('The token have an incorrect format or is not valid.');
        $this->expectExceptionCode(401);

        $this->verifier->__invoke(123, []);
    }

    /** @test */
    public function it_should_throw_an_invalid_token_decoded_exception_when_the_token_is_empty(): void
    {
        $this->expectException(InvalidTokenDecodedException::class);
        $this->expectExceptionMessage('The token have an incorrect format or is not valid.');
        $this->expectExceptionCode(401);

        $this->verifier->__invoke('', []);
    }
}
