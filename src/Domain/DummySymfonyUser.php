<?php

declare(strict_types=1);

namespace CognitoTokenVerifier\Domain;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * This class is used to create a Dummy Symfony User object that implements the UserInterface,
 * which is required by the SecurityBundle to create a custom authenticator.
 *
 * If you want to use this class, you will need to install symfony/security-bundle package.
 *
 * @see https://symfony.com/doc/current/security.html#b-configuring-how-users-are-loaded
 *
 */

/**
 * @psalm-immutable
 */
final class DummySymfonyUser implements UserInterface
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
