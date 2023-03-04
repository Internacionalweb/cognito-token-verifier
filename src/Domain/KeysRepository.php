<?php

declare(strict_types=1);

namespace JwtCognitoSignature\Domain;

use Firebase\JWT\Key;

interface KeysRepository
{
    public function findKeyWithKid(string $kid): ?Key;
}
