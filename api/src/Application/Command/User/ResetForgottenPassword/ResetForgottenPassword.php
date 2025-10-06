<?php

namespace App\Application\Command\User\ResetForgottenPassword;

use App\Application\Bus\Command\Command;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Validator\Constraints as Assert;

class ResetForgottenPassword implements Command
{
    #[Ignore]
    public int $id;

    #[Assert\Email]
    public string $email;

    #[Assert\Regex(
        pattern: '/^(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).{8,}$/',
        message: 'Exceptions.Assert.User.PasswordStrength',
    )]
    #[Assert\Length(min: 8, max: 255)]
    #[Assert\NotCompromisedPassword]
    public string $newPassword;

    public function __construct(string $newPassword)
    {
        $this->newPassword = $newPassword;
    }
}
