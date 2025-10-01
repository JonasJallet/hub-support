<?php

namespace App\Domain\Exception\FormSupport;

use App\Domain\Exception\DomainException;

class SupportCaseNotFoundException extends DomainException
{
    public function __construct()
    {
        parent::__construct("Exceptions.SupportCase.FormNotFound", 404);
    }
}
