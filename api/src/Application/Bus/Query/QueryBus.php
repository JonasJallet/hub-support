<?php

declare(strict_types=1);

namespace App\Application\Bus\Query;

interface QueryBus
{
    function ask(Query $query): mixed;
}
