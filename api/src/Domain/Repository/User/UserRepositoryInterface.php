<?php

declare(strict_types=1);

namespace App\Domain\Repository\User;

use App\Domain\Entity\User;

interface UserRepositoryInterface
{
    public function save(User $user, bool $newEntity): void;

    public function delete(User $user): void;

    public function findByEmail(string $email): ?User;

    public function browse(): array;
}

