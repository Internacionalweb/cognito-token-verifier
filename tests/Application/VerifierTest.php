<?php

namespace CognitoTokenVerifier\Tests\Application;

use DG\BypassFinals;
use Firebase\JWT\Key;
use PHPUnit\Framework\TestCase;
use CognitoTokenVerifier\Domain\Token;
use PHPUnit\Framework\MockObject\MockObject;
use CognitoTokenVerifier\Application\Verifier;
use CognitoTokenVerifier\Domain\KeysRepository;
use CognitoTokenVerifier\Infrastructure\BearerTokenParser;
use CognitoTokenVerifier\Domain\Exceptions\InvalidSignature;
use CognitoTokenVerifier\Domain\Exceptions\InvalidUseException;
use CognitoTokenVerifier\Domain\Exceptions\InvalidTokenException;
use CognitoTokenVerifier\Domain\Exceptions\InvalidClientException;
use CognitoTokenVerifier\Domain\Exceptions\InvalidScopesException;
use CognitoTokenVerifier\Domain\Exceptions\InvalidUserPoolException;
use CognitoTokenVerifier\Domain\Exceptions\InvalidTokenExpiredException;

final class VerifierTest extends TestCase
{
    /** @var MockObject | KeysRepository $keysRepositoryMock */
    private KeysRepository $keysRepositoryMock;

    /** @var MockObject | BearerTokenParser $bearerTokenParserMock */
    private BearerTokenParser $bearerTokenParserMock;

    /** @var MockObject | Key $keyMock */
    private Key $keyMock;

    private Verifier $verifier;

    public function setUp(): void
    {
        BypassFinals::enable();

        $this->keyMock = $this->createMock(Key::class);

        $this->keysRepositoryMock = $this->createMock(KeysRepository::class);

        $this->bearerTokenParserMock = $this->createMock(BearerTokenParser::class);

        $this->verifier = new Verifier(
            cognitoUserPoolId: 'eu-west-3_XXXXXXXX',
            keysRepository: $this->keysRepositoryMock,
            cognitoAppClientsIdsAllowed: 'clientId1,clientId2',
            bearerTokenParser: $this->bearerTokenParserMock,
        );
    }

    /** @test */
    public function it_should_throw_an_invalid_token_exception_when_token_dont_have_required_parameters(): void
    {
        $this->expectException(InvalidTokenException::class);
        $this->expectExceptionMessage('The token is not valid');
        $this->expectExceptionCode(401);

        $this->keysRepositoryMock->method('findKeyByKid')->willReturn($this->keyMock);

        $this->bearerTokenParserMock->method('decode')->willReturn(
            new Token(
                kid: '',
                iss: '',
                tokenUse: '',
                clientId: '',
                scope: [],
                exp: 123,
            )
        );

        $this->verifier->__invoke('token', ['scope']);
    }

    /** @test */
    public function it_should_throw_an_invalid_signature_exception_when_kid_dont_exists_on_jwk(): void
    {
        $this->expectException(InvalidSignature::class);
        $this->expectExceptionMessage('The signature is not valid');
        $this->expectExceptionCode(401);

        $this->keysRepositoryMock->method('findKeyByKid')->willReturn(null);

        $this->bearerTokenParserMock->method('decode')->willReturn(
            new Token(
                kid: 'kid',
                iss: 'https://cognito-idp.eu-west-3.amazonaws.com/eu-west-3_WRONG',
                tokenUse: 'access',
                clientId: 'clientIdNotInThelist',
                scope: ['scope'],
                exp: 9999999999,
            )
        );

        $this->verifier->__invoke('token', ['scope']);
    }

    /** @test */
    public function it_should_throw_an_invalid_user_pool_exception_when_issuer_dont_match_with_config()
    {
        $this->expectException(InvalidUserPoolException::class);
        $this->expectExceptionMessage('The user pool is not valid');
        $this->expectExceptionCode(401);

        $this->keysRepositoryMock->method('findKeyByKid')->willReturn($this->keyMock);

        $this->bearerTokenParserMock->method('decode')->willReturn(
            new Token(
                kid: 'kid',
                iss: 'https://cognito-idp.eu-west-3.amazonaws.com/eu-west-3_WRONG',
                tokenUse: 'access',
                clientId: 'clientIdNotInThelist',
                scope: ['scope'],
                exp: 9999999999,
            )
        );

        $this->verifier->__invoke('token', ['scope']);
    }

    /** @test */
    public function it_should_throw_an_invalid_token_expire_exception_when_token_is_expired(): void
    {
        $this->expectException(InvalidTokenExpiredException::class);
        $this->expectExceptionMessage('The token is expired');
        $this->expectExceptionCode(401);

        $this->keysRepositoryMock->method('findKeyByKid')->willReturn($this->keyMock);

        $this->bearerTokenParserMock->method('decode')->willReturn(
            new Token(
                kid: 'kid',
                iss: 'https://cognito-idp.eu-west-3.amazonaws.com/eu-west-3_XXXXXXXX',
                tokenUse: 'wrongTokenUse',
                clientId: 'clientId1',
                scope: ['scope'],
                exp: 1,
            )
        );

        $this->verifier->__invoke('token', ['scope']);
    }

    /** @test */
    public function it_should_throw_an_invalid_use_exception_when_token_use_is_not_access(): void
    {
        $this->expectException(InvalidUseException::class);
        $this->expectExceptionMessage('The token use is not valid');
        $this->expectExceptionCode(401);

        $this->keysRepositoryMock->method('findKeyByKid')->willReturn($this->keyMock);

        $this->bearerTokenParserMock->method('decode')->willReturn(
            new Token(
                kid: 'kid',
                iss: 'https://cognito-idp.eu-west-3.amazonaws.com/eu-west-3_XXXXXXXX',
                tokenUse: 'WRONG_TOKEN_USE',
                clientId: 'clientIdNotInThelist',
                scope: ['scope'],
                exp: 9999999999,
            )
        );

        $this->verifier->__invoke('token', ['scope']);
    }

    /** @test */
    public function it_should_throw_an_invalid_client_exception_when_client_id_is_not_allowed(): void
    {
        $this->expectException(InvalidClientException::class);
        $this->expectExceptionMessage('The client is not valid');
        $this->expectExceptionCode(401);

        $this->keysRepositoryMock->method('findKeyByKid')->willReturn($this->keyMock);

        $this->bearerTokenParserMock->method('decode')->willReturn(
            new Token(
                kid: 'kid',
                iss: 'https://cognito-idp.eu-west-3.amazonaws.com/eu-west-3_XXXXXXXX',
                tokenUse: 'access',
                clientId: 'clientIdNotInThelist',
                scope: ['scope'],
                exp: 9999999999,
            )
        );

        $this->verifier->__invoke('token', ['scope']);
    }

    /** @test */
    public function it_should_throw_an_invalid_scopes_exception_when_required_scope_is_not_on_token_scope_list(): void
    {
        $this->expectException(InvalidScopesException::class);
        $this->expectExceptionMessage('The scopes are not valid');
        $this->expectExceptionCode(401);

        $this->keysRepositoryMock->method('findKeyByKid')->willReturn($this->keyMock);

        $this->bearerTokenParserMock->method('decode')->willReturn(
            new Token(
                kid: 'kid',
                iss: 'https://cognito-idp.eu-west-3.amazonaws.com/eu-west-3_XXXXXXXX',
                tokenUse: 'access',
                clientId: 'clientId1',
                scope: ['notInScopeListScope'],
                exp: 9999999999,
            )
        );

        $this->verifier->__invoke('token', ['scope', 'otherScope']);
    }

    /** @test */
    public function it_should_throw_an_invalid_scopes_exception_when_not_all_required_scopes_are_on_token_scope_list(): void
    {
        $this->expectException(InvalidScopesException::class);
        $this->expectExceptionMessage('The scopes are not valid');
        $this->expectExceptionCode(401);

        $this->keysRepositoryMock->method('findKeyByKid')->willReturn($this->keyMock);

        $this->bearerTokenParserMock->method('decode')->willReturn(
            new Token(
                kid: 'kid',
                iss: 'https://cognito-idp.eu-west-3.amazonaws.com/eu-west-3_XXXXXXXX',
                tokenUse: 'access',
                clientId: 'clientId1',
                scope: ['scope'],
                exp: 9999999999,
            )
        );

        $this->verifier->__invoke('token', ['scope', 'otherScopeMissingOnTokenScopeList']);
    }

    /** @test */
    public function it_shouldnt_throw_any_exception_when_token_is_correct(): void
    {
        $this->keysRepositoryMock->method('findKeyByKid')->willReturn($this->keyMock);

        $this->bearerTokenParserMock->method('decode')->willReturn(
            new Token(
                kid: 'kid',
                iss: 'https://cognito-idp.eu-west-3.amazonaws.com/eu-west-3_XXXXXXXX',
                tokenUse: 'access',
                clientId: 'clientId1',
                scope: ['scope'],
                exp: 9999999999,
            )
        );

        $this->verifier->__invoke('token', ['scope']);

        $this->assertTrue(true);
    }

    /** @test */
    public function it_shouldnt_throw_any_exception_when_token_is_correct_with_multiple_scopes(): void
    {
        $this->keysRepositoryMock->method('findKeyByKid')->willReturn($this->keyMock);

        $this->bearerTokenParserMock->method('decode')->willReturn(
            new Token(
                kid: 'kid',
                iss: 'https://cognito-idp.eu-west-3.amazonaws.com/eu-west-3_XXXXXXXX',
                tokenUse: 'access',
                clientId: 'clientId1',
                scope: ['scope', 'otherScope', 'otherScope2'],
                exp: 9999999999,
            )
        );

        $this->verifier->__invoke('token', ['scope', 'otherScope', 'otherScope2']);

        $this->assertTrue(true);
    }
}
