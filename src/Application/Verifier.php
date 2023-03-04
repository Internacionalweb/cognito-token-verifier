<?php

declare(strict_types=1);

namespace JwtCognitoSignature\Application;

use InvalidArgumentException;
use JwtCognitoSignature\Domain\Token;
use JwtCognitoSignature\Domain\KeysRepository;
use JwtCognitoSignature\Infrastructure\BearerTokenParser;
use JwtCognitoSignature\Domain\Exceptions\InvalidSignature;
use JwtCognitoSignature\Domain\Exceptions\InvalidUseException;
use JwtCognitoSignature\Domain\Exceptions\InvalidTokenException;
use JwtCognitoSignature\Domain\Exceptions\InvalidClientException;
use JwtCognitoSignature\Domain\Exceptions\InvalidScopesException;
use JwtCognitoSignature\Domain\Exceptions\InvalidUserPoolException;
use JwtCognitoSignature\Domain\Exceptions\InvalidTokenExpiredException;

final class Verifier
{
    private Token $token;

    public function __construct(private KeysRepository $keysRepository)
    {
    }

    /**
     * @param  string                       $bearerToken
     * @param  array<int,string>            $scopesRoutes
     * @throws InvalidArgumentException
     * @throws InvalidTokenException
     * @throws InvalidSignature
     * @throws InvalidTokenExpiredException
     * @throws InvalidClientException
     * @throws InvalidUserPoolException
     * @throws InvalidUseException
     * @throws InvalidScopesException
     */
    public function __invoke(string $bearerToken, ?array $scopesRoutes): void
    {
        $this->token = BearerTokenParser::decode($bearerToken);

        $this->ensureTokenHaveRequiredParameters();

        if ($_ENV['APP_ENV'] === 'prod') {
            $this->ensureSignature();
            $this->ensureClientId();
            $this->ensureIssuer();
        }

        $this->ensureTokenIsNotExpired();
        $scopesRoutesFormated = $this->formatScopesRoutes($scopesRoutes);
        $this->ensureAccess();

        if (count($scopesRoutesFormated) > 0) {
            $this->ensureScopes($scopesRoutesFormated);
        }
    }

    private function ensureSignature(): void
    {
        $jwk = $this->keysRepository->findKeyWithKid($this->token->kid());

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

    /**
     * @param  ?array<int,string> $scopesRoutes
     * @return array<int,string>
     */
    private function formatScopesRoutes(?array $scopesRoutes): array
    {
        if (!isset($scopesRoutes) || count($scopesRoutes) == 0) {
            return [];
        }

        return array_map(function ($scope) {
            return $_ENV['AWS_SCOPE_URL'] . '/' . $scope;
        }, $scopesRoutes);
    }

    private function ensureClientId(): void
    {
        $clients = explode(',', $_ENV['AWS_CLIENTS_ID_ALLOWNED']);

        if (!in_array($this->token->clientId(), $clients)) {
            throw new InvalidClientException();
        }
    }

    private function ensureIssuer(): void
    {
        if ($this->token->iss() != 'https://cognito-idp.' . $_ENV['AWS_COGNITO_REGION'] . '.amazonaws.com/' . $_ENV['AWS_COGNITO_USER_POOL_ID']) {
            throw new InvalidUserPoolException();
        }
    }

    private function ensureAccess(): void
    {
        if ($this->token->tokenUse() != 'access') {
            throw new InvalidUseException();
        }
    }

    /**
     * @param array<int,string> $scopeRoutes
     */
    private function ensureScopes(array $scopeRoutes): void
    {
        if (count(array_intersect($this->token->scope(), $scopeRoutes)) != count($scopeRoutes)) {
            throw new InvalidScopesException();
        }
    }
}
