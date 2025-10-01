<?php

declare(strict_types=1);

namespace App\Application\Bus\Command;

interface CommandBus
{
    function dispatch(Command $command): void;
}
