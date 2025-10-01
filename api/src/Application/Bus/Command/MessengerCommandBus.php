<?php

declare(strict_types=1);

namespace App\Application\Bus\Command;

use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class MessengerCommandBus implements CommandBus
{
    /**
     * @var MessageBusInterface
     */
    private MessageBusInterface $commandBus;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @throws ExceptionInterface
     */
    public function dispatch(Command $command): void
    {
        $this->commandBus->dispatch($command);
    }
}
