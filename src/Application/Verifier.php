<?php

declare(strict_types=1);

namespace CognitoTokenVerifier\Application;

use InvalidArgumentException;
use CognitoTokenVerifier\Domain\Token;
use CognitoTokenVerifier\Domain\KeysRepository;
use CognitoTokenVerifier\Infrastructure\BearerTokenParser;
use CognitoTokenVerifier\Domain\Exceptions\InvalidSignature;
use CognitoTokenVerifier\Domain\Exceptions\InvalidUseException;
use CognitoTokenVerifier\Domain\Exceptions\InvalidTokenException;
use CognitoTokenVerifier\Domain\Exceptions\InvalidClientException;
use CognitoTokenVerifier\Domain\Exceptions\InvalidScopesException;
use CognitoTokenVerifier\Domain\Exceptions\InvalidUserPoolException;
use CognitoTokenVerifier\Domain\Exceptions\InvalidTokenExpiredException;

final class Verifier
{
    private Token $token;
    private string $cognitoUserPoolRegion;

    public function __construct(
        private string $cognitoUserPoolId,
        private KeysRepository $keysRepository,
        private ?string $cognitoAppClientsIdsAllowed = null,
    ) {
        $this->setCognitoUserPoolRegionFromCognitoUserPoolId();
    }

    /**
     * @param  string                       $bearerToken
     * @param  array<int,string>|null       $requiredResourceServerScopes
     * @throws InvalidArgumentException
     * @throws InvalidTokenException
     * @throws InvalidSignature
     * @throws InvalidTokenExpiredException
     * @throws InvalidClientException
     * @throws InvalidUserPoolException
     * @throws InvalidUseException
     * @throws InvalidScopesException
     */
    public function __invoke(string $bearerToken, ?array $requiredResourceServerScopes = null): void
    {
        $this->token = BearerTokenParser::decode($bearerToken);

        $this->ensureTokenHaveRequiredParameters();
        $this->ensureTokenKeyIdExistsOnJWK();
        $this->ensureTokenIssuerMatchWithUserPool();
        $this->ensureTokenIsNotExpired();
        $this->ensureTokenUseIsAccess();

        if (isset($this->cognitoAppClientsIdsAllowed) && !empty($this->cognitoAppClientsIdsAllowed)) {
            $this->ensureTokenAppClientIdItsAllowed();
        }

        if ($requiredResourceServerScopes) {
            $this->ensureRequiredScopesExistsOnToken($requiredResourceServerScopes);
        }
    }

    private function ensureTokenKeyIdExistsOnJWK(): void
    {
        $jwk = $this->keysRepository->findKeyByKid($this->token->kid());

        if (null === $jwk) {
            throw new InvalidSignature();
        }
    }

    private function ensureTokenIsNotExpired(): void
    {
        if ($this->token->isActive()) {
            throw new InvalidTokenExpiredException();
        }
    }

    private function ensureTokenHaveRequiredParameters(): void
    {
        if (empty($this->token->kid()) || empty($this->token->iss()) || empty($this->token->tokenUse()) || empty($this->token->clientId()) || empty($this->token->scope()) || empty($this->token->exp())) {
            throw new InvalidTokenException();
        }
    }

    private function ensureTokenAppClientIdItsAllowed(): void
    {
        $clients = explode(',', $this->cognitoAppClientsIdsAllowed);

        if (!in_array($this->token->clientId(), $clients)) {
            throw new InvalidClientException();
        }
    }

    private function ensureTokenIssuerMatchWithUserPool(): void
    {
        if ($this->token->iss() !== 'https://cognito-idp.' . $this->cognitoUserPoolRegion . '.amazonaws.com/' . $this->cognitoUserPoolId) {
            throw new InvalidUserPoolException();
        }
    }

    private function ensureTokenUseIsAccess(): void
    {
        if ($this->token->tokenUse() !== 'access') {
            throw new InvalidUseException();
        }
    }

    /**
     * @param array<string> $scopeRoutes
     */
    private function ensureRequiredScopesExistsOnToken(array $scopeRoutes): void
    {
        if (count(array_intersect($this->token->scope(), $scopeRoutes)) !== count($scopeRoutes)) {
            throw new InvalidScopesException();
        }
    }

    private function setCognitoUserPoolRegionFromCognitoUserPoolId(): void
    {
        $this->cognitoUserPoolRegion = explode('_', $this->cognitoUserPoolId)[0];
    }
}
