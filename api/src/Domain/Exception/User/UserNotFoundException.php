<?php

namespace App\Domain\Exception\User;

use App\Domain\Exception\DomainException;

class UserNotFoundException extends DomainException
{
    public function __construct(string $email)
    {
        parent::__construct("Exceptions.User.UserNotFound", 404, ["%email%" => $email]);
    }
}
