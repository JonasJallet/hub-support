<?php


namespace App\Domain\Service\Authentication;

use App\Domain\Entity\User;

interface AuthenticationInterface
{
    public function hashPassword(User $user, string $password): string;

    public function isPasswordValid(User $user, string $password): bool;
}
