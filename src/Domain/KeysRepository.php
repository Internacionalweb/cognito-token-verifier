<?php

declare(strict_types=1);

namespace CognitoTokenVerifier\Domain;

use Firebase\JWT\Key;

interface KeysRepository
{
    public function findKeyByKid(string $kid): ?Key;
}
