<?php


namespace App\Domain\Exception\User;

use App\Domain\Exception\DomainException;

class PasswordIsNotDifferentException extends DomainException
{
    public function __construct()
    {
        parent::__construct("Exceptions.User.PasswordIsNotDifferentException", 422);
    }
}
