<?php

namespace App\Domain\Service;

interface MapToEntityInterface
{
    public function map(string $class, int|array $id): object|array|null;
}
