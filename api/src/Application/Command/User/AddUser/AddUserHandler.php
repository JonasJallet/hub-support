<?php

namespace App\Application\Command\User\AddUser;

use App\Application\Bus\Command\CommandHandler;
use App\Domain\Repository\User\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class AddUserHandler implements CommandHandler
{
    public function __construct(
        private UserRepository $userRepository,
    )
    {
    }

    public function __invoke(AddUser $addUser): void
    {
        $user = $addUser->toEntity();
        $this->userRepository->add($user);
    }
}
