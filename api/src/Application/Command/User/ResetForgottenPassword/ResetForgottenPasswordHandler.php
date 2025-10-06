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
        $email = $resetForgottenPassword->email;
        $user = $this->userRepository->read($email);

        if ($user === null || $user->getEmail() !== $email) {
            throw new UserNotFoundException($email);
        }

        $newPassword = $resetForgottenPassword->newPassword;
        if ($this->authentication->isPasswordValid($user, $newPassword)) {
            throw new PasswordIsNotDifferentException();
        }

        $user->setPassword($this->authentication->hashPassword($user, $newPassword));
        $this->userRepository->edit($user);
    }
}

