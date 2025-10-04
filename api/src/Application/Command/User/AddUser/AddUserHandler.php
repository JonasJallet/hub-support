<?php

namespace App\Application\Command\User\AddUser;

use App\Application\Bus\Command\CommandHandler;
use App\Domain\Repository\User\UserRepository;
use App\Domain\Service\Authentication\AuthenticationInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class AddUserHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
        private AuthenticationInterface $authentication,
    )
    {
    }

    public function __invoke(AddUser $addUser): void
    {
        $user = $addUser->toEntity();

        $hashedPassword = $this->authentication->hashPassword($user, $addUser->password);
        $user->setPassword($hashedPassword);

        $this->userRepository->add($user);
    }
}
