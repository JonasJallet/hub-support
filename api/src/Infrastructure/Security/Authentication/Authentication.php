<?php

namespace App\Infrastructure\Security\Authentication;

use App\Domain\Entity\User;
use App\Domain\Exception\User\UserUnauthorizedException;
use App\Domain\Service\Authentication\AuthenticationInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class Authentication implements AuthenticationInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
    )
    {
    }

    public function ensureLoginIsValid(User $user, string $sendPassword): void
    {
        if (!$this->passwordEncoder->isPasswordValid($user, $sendPassword)) {
            if ($user instanceof User) {
                throw new UserUnauthorizedException($user->getEmail());
            }
        }
    }

    public function hashPassword(User $user, string $password): string
    {
        return $this->passwordEncoder->hashPassword($user, $password);
    }

    public function isPasswordValid(User $user, string $password): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $password);
    }
}
