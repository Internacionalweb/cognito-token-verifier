<?php

declare(strict_types=1);

namespace CognitoTokenVerifier\Infrastructure;

use Firebase\JWT\JWT;
use CognitoTokenVerifier\Domain\Token;
use CognitoTokenVerifier\Domain\Exceptions\InvalidTokenDecodedException;

final class BearerTokenParser
{
    public static function decode(string $bearerToken): Token
    {
        try {
            $scopes = [];
            $headerDecoded = JWT::jsonDecode(JWT::urlsafeB64Decode(explode('.', $bearerToken)[0]));
            $bodyDecoded = JWT::jsonDecode(JWT::urlsafeB64Decode(explode('.', $bearerToken)[1]));

            if (!empty($bodyDecoded->scope)) {
                $scopes = explode(' ', $bodyDecoded->scope);
            }

            return new Token(
                $headerDecoded->kid ?? '',
                $bodyDecoded->iss ?? '',
                $bodyDecoded->token_use ?? '',
                $bodyDecoded->client_id ?? '',
                $scopes,
                $bodyDecoded->exp ?? 0
            );
        } catch (\Exception $e) {
            throw new InvalidTokenDecodedException();
        }
    }
}
