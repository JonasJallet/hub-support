<?php

namespace App\Application\Command\User\ResetForgottenPassword;

use App\Domain\Exception\User\PasswordIsNotDifferentException;
use App\Domain\Exception\User\UserNotFoundException;
use App\Domain\Repository\User\UserRepository;
use App\Domain\Service\Authentication\AuthenticationInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
readonly class ResetForgottenPasswordHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private AuthenticationInterface $authentication,
    )
    {
    }

    public function __invoke(ResetForgottenPassword $resetForgottenPassword): void
    {
        $plainPassword = $resetForgottenPassword->password;
        $email = $resetForgottenPassword->email;

        $user = $this->userRepository->read($email);

        if ($user === null || $user->getEmail() !== $email) {
            throw new UserNotFoundException($email);
        }

        if ($this->authentication->isPasswordValid($user, $plainPassword)) {
            throw new PasswordIsNotDifferentException();
        }

        $user->setPassword($this->authentication->hashPassword($user, $plainPassword));
        $this->userRepository->edit($user);
    }
}

