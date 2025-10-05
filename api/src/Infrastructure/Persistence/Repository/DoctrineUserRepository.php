<?php

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Entity\User;
use App\Domain\Repository\User\UserRepositoryInterface;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 */
class DoctrineUserRepository extends ServiceEntityRepository implements UserRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $user, bool $newEntity): void
    {
        if ($newEntity) $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    public function delete(User $user): void
    {
        $this->getEntityManager()->remove($user);
        $this->getEntityManager()->flush();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }

    public function browse(): array
    {
        return $this->findAll();
    }

    public function countUsersLoggedToday(): int
    {
        $startOfDay = (new DateTimeImmutable('today'))->setTime(0, 0, 0);
        $endOfDay = $startOfDay->setTime(23, 59, 59);

        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->where('u.lastLoginAt BETWEEN :start AND :end')
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
