<?php

namespace App\Infrastructure\Service;

use App\Domain\Service\MapToEntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

final readonly class MapToEntity implements MapToEntityInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function map(string $class, int|array $id): object|array|null
    {
        if (is_array($id)) {
            return $this->entityManager->getRepository($class)->findBy(['id' => $id]);
        }

        return $this->entityManager->find($class, $id);
    }
}
