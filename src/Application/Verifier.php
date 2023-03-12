<?php

declare(strict_types=1);

namespace CognitoTokenVerifier\Application;

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

    /**
     * @param string         $cognitoUserPoolId           AWS Cognito User Pool Id (You can find it in the AWS Cognito User Pool console) (Example: us-east-1_XXXXXXXXX)
     * @param KeysRepository $keysRepository              Keys Repository (You can use the one provided in the package 'FromFileKeysRepository' or create your own, just implement the KeysRepository interface)
     * @param string|null    $cognitoAppClientsIdsAllowed AWS Cognito APPs Client Ids allowed (Separated by comma) (Optional if you dont want to validate Apps just keep it on null)
     */
    public function __construct(
        private string $cognitoUserPoolId,
        private KeysRepository $keysRepository,
        private ?string $cognitoAppClientsIdsAllowed = null,
        private BearerTokenParser $bearerTokenParser = new BearerTokenParser(),
    ) {
        $this->setCognitoUserPoolRegionFromCognitoUserPoolId();
    }

    /**
     * @param  string                       $bearerToken    Bearer Token from Cognito Auth
     * @param  array<string>|null           $requiredScopes Scopes that the token must have to be valid (Optional if you dont want to validate scopes just keep it on null)
     * @throws InvalidTokenException        In case the token dont have the required parameters (kid, iss, tokenUse, clientId, scope, exp)
     * @throws InvalidSignature             In case the token signature is invalid (kid not found on JWK)
     * @throws InvalidTokenExpiredException In case the token is expired
     * @throws InvalidClientException       In case the token clientId is not on the list of allowed clients $cognitoAppClientsIdsAllowed (if you dont provide a list of allowed clients on constructor this exception will never be thrown)
     * @throws InvalidUserPoolException     In case the token issuer dont match with the user pool id provided on constructor
     * @throws InvalidUseException          In case the token use is not 'access'
     * @throws InvalidScopesException       In case the token dont have the required scopes (if you dont provide a list of required scopes on $requiredScopes this exception will never be thrown)
     */
    public function __invoke(string $bearerToken, ?array $requiredScopes = null): void
    {
        $this->token = $this->bearerTokenParser->decode($bearerToken);

        $this->ensureTokenHaveRequiredParameters();
        $this->ensureTokenKeyIdExistsOnJWK();
        $this->ensureTokenIssuerMatchWithUserPool();
        $this->ensureTokenIsNotExpired();
        $this->ensureTokenUseIsAccess();

        if (isset($this->cognitoAppClientsIdsAllowed) && !empty($this->cognitoAppClientsIdsAllowed)) {
            $this->ensureTokenAppClientIdItsAllowed();
        }

        if ($requiredScopes) {
            $this->ensureRequiredScopesExistsOnToken($requiredScopes);
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
