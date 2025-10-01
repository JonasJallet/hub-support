<?php

namespace App\Application\Query\User\ReadUser;

use App\Application\Bus\Query\QueryHandler;
use App\Application\Query\User\UserResponse;
use App\Domain\Exception\User\UserNotFoundException;
use App\Domain\Repository\User\UserRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ReadUserHandler implements QueryHandler
{
    public function __construct(
        private UserRepository $userRepository
    )
    {
    }

    public function __invoke(ReadUser $readUser): UserResponse
    {
        $user = $this->userRepository->read($readUser->email);

        if ($user === null) {
            throw new UserNotFoundException($readUser->email);
        }

        return (new UserResponse())->fromEntity($user);
    }

}
