<?php

namespace App\Infrastructure\Symfony\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final readonly class MapToEntityAttribute
{
    public function __construct(
        public string $entityClass
    ) {}
}
