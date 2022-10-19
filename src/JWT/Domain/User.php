<?php

declare(strict_types=1);

namespace JwtCognitoSignature\JWT\Domain;

use Symfony\Component\Security\Core\User\UserInterface;

final class User implements UserInterface
{
    public function __construct(
        private string $userId
    ) {
    }

    public function userId(): string
    {
        return $this->userId;
    }

    /**
     * @return array<mixed,mixed>
     */
    public function getRoles(): array
    {
        return [];
    }

    public function getUserIdentifier(): string
    {
        return $this->userId;
    }

    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function eraseCredentials(): void
    {
    }
}
