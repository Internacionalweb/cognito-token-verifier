<?php

namespace JwtCognitoSignature\JWT\Domain;

final class Token
{
    /**
     * @param array<int,string> $scope
     */
    public function __construct(private string $kid, private string $iss, private string $tokenUse, private string $clientId, private array $scope, private int $exp)
    {
    }

    public function kid(): string
    {
        return $this->kid;
    }

    public function iss(): string
    {
        return $this->iss;
    }

    public function tokenUse(): string
    {
        return $this->tokenUse;
    }

    public function clientId(): string
    {
        return $this->clientId;
    }

    /**
     * @return array<int,string>
     */
    public function scope(): array
    {
        return $this->scope;
    }

    public function exp(): int
    {
        return $this->exp;
    }

    public function isActive(): bool
    {
        if (time() >= $this->exp) {
            return true;
        }

        return false;
    }
}
