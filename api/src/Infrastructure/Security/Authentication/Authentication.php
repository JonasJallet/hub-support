<?php

namespace App\Infrastructure\Security\Authentication;

use App\Domain\Entity\User;
use App\Domain\Exception\User\UserUnauthorizedException;
use App\Domain\Service\Authentication\AuthenticationInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

readonly class Authentication implements AuthenticationInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private JWTTokenManagerInterface $jwtManager,
    )
    {
    }

    public function ensureLoginIsValid(User $user, string $providedPassword): string
    {
        if (!$this->passwordEncoder->isPasswordValid($user, $providedPassword)) {

            throw new UserUnauthorizedException($user->getEmail());
        }

        return $this->generateToken($user);
    }

    public function hashPassword(User $user, string $password): string
    {
        return $this->passwordEncoder->hashPassword($user, $password);
    }

    public function isPasswordValid(User $user, string $password): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $password);
    }

    private function generateToken($user): string
    {
        return $this->jwtManager->create($user);
    }
}
