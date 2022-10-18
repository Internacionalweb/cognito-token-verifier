<?php

namespace JwtCognitoSignature\JWT\Domain;

use Firebase\JWT\Key;

interface JWKRepository
{
    public function findKeyWithKid(string $kid): ?Key;
}
