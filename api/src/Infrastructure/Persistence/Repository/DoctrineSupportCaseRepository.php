<?php

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Entity\SupportCase;
use App\Domain\Repository\SupportCase\SupportCaseRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SupportCase|null find($id, $lockMode = null, $lockVersion = null)
 * @method SupportCase[]    findAll()
 * @method SupportCase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method SupportCase|null findOneBy(array $criteria, array $orderBy = null)
 */
class DoctrineSupportCaseRepository extends ServiceEntityRepository implements SupportCaseRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SupportCase::class);
    }

    public function save(SupportCase $form, bool $newEntity): void
    {
        if ($newEntity) $this->getEntityManager()->persist($form);
        $this->getEntityManager()->flush();
    }

    public function delete(SupportCase $form): void
    {
        $this->getEntityManager()->remove($form);
        $this->getEntityManager()->flush();
    }

    public function findById(int $id): ?SupportCase
    {
        return $this->findOneBy(['id' => $id]);
    }

    public function browse(): array
    {
        return $this->findAll();
    }
}
