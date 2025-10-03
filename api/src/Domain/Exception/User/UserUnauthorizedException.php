<?php

namespace App\Domain\Exception\User;

use App\Domain\Exception\DomainException;

class UserUnauthorizedException extends DomainException
{
    public function __construct(string $email)
    {
        parent::__construct("Exceptions.User.UserUnauthorized", 401, ["%email%" => $email]);
    }
}
