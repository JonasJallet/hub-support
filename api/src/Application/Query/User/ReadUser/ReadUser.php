<?php

namespace App\Application\Query\User\ReadUser;

use App\Application\Bus\Query\Query;

class ReadUser implements Query
{
    public string $email;
}
