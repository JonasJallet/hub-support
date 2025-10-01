<?php

namespace App\Domain\Repository\User;

use App\Domain\Entity\User;

readonly class UserRepository
{
    public function __construct(
        private UserRepositoryInterface $userRepositoryInterface,
    ) {
    }

    public function browse(): array
    {
        return $this->userRepositoryInterface->browse();
    }

    public function read(string $email): ?User
    {
        return $this->userRepositoryInterface->findByEmail($email);
    }

    public function edit(User $user): void
    {
        $this->userRepositoryInterface->save($user, false);
    }

    public function add(User $user): void
    {
        $this->userRepositoryInterface->save($user, true);
    }

    public function delete(User $user): void
    {
        $this->userRepositoryInterface->delete($user);
    }
}
