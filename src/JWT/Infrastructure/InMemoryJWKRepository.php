<?php

namespace JwtCognitoSignature\JWT\Infrastructure;

use Firebase\JWT\JWK;
use Firebase\JWT\Key;
use JwtCognitoSignature\JWT\Domain\JWKRepository;

final class InMemoryJWKRepository implements JWKRepository
{
    private string $keys;

    public function __construct()
    {
        $this->keys = file_get_contents(__DIR__ . '/jwks.json');
    }

    public function findKeyWithKid(string $kid) : ?Key
    {
        $keys = json_decode($this->keys, true)['keys'];

        foreach ($keys as $key) {
            if ($kid === $key['kid']) {
                $jwk = new JWK();
                return $jwk->parseKey($key);
            }
        }
        return null;
    }
}
