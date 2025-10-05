<?php

namespace App\Domain\Exception\User;

use DomainException;

final class UserAlreadyExistException extends DomainException
{
    public function __construct(private readonly string $email)
    {
        parent::__construct('Exceptions.User.UserAlreadyExist');
    }

    public function getTranslationParameters(): array
    {
        return ['%email%' => $this->email];
    }

    public function getTranslationDomain(): string
    {
        return 'exceptions';
    }
}
