<?php

namespace App\Application\Command\User\ResetForgottenPassword;

use App\Application\Bus\Command\Command;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

class ResetForgottenPassword implements Command
{
    #[Ignore]
    public int $id;

    #[Assert\Email]
    public string $email;

    public function __construct(
        #[Assert\Regex(
            pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/',
            message: 'Exceptions.Assert.User.PasswordStrength',
        )]
        #[Assert\Length(min: 8, max: 255)]
        #[Assert\NotCompromisedPassword]
        public string $password,
    )
    {
    }
}
