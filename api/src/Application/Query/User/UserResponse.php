<?php

namespace App\Application\Query\User;

use App\Domain\Entity\User;

class UserResponse
{
    public int $id;
    public string $firstName;
    public string $lastName;
    public string $email;

    public function fromEntity(User $user): self
    {
        $this->id = $user->getId();
        $this->firstName = $user->getFirstName();
        $this->lastName = $user->getLastName();
        $this->email = $user->getEmail();

        return $this;
    }
}
