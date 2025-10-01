<?php

namespace App\Application\Bus\Middleware;

use App\Domain\Service\MapToEntityInterface;
use App\Infrastructure\Symfony\Attribute\MapToEntityAttribute;
use ReflectionObject;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

final readonly class MapToEntityMiddleware implements MiddlewareInterface
{
    public function __construct(
        private MapToEntityInterface $mapToEntityInterface
    ) {}

    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        $message = $envelope->getMessage();

        $reflection = new ReflectionObject($message);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $attributes = $property->getAttributes(MapToEntityAttribute::class);
            if (empty($attributes)) {
                continue;
            }

            $attribute = $attributes[0]->newInstance();
            $value = $property->getValue($message);

            if ($value !== null) {
                $resolved = $this->mapToEntityInterface->map($attribute->entityClass, $value);
                $property->setValue($message, $resolved);
            }
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
