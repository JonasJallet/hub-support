<?php

namespace App\Infrastructure\Persistence\Factory;

use App\Domain\Entity\SupportCase;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<SupportCase>
 */
final class SupportCaseFactory extends PersistentProxyObjectFactory
{
    /**
     * @return array<string, mixed>
     */
    protected function defaults(): array
    {
        return [
            'subject' => self::faker()->sentence(5),
            'message' => self::faker()->paragraphs(3, true),
            'file' => null,
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }

    public static function class(): string
    {
        return SupportCase::class;
    }
}
