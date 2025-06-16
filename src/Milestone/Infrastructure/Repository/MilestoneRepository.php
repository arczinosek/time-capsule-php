<?php

namespace App\Milestone\Infrastructure\Repository;

use App\Milestone\Domain\Model\Milestone;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Milestone>
 */
class MilestoneRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Milestone::class);
    }

    public function save(Milestone $milestone): Milestone
    {
        $this->getEntityManager()->persist($milestone);
        $this->getEntityManager()->flush();

        return $milestone;
    }

    public function remove(Milestone $milestone): void
    {
        $this->getEntityManager()->remove($milestone);
        $this->getEntityManager()->flush();
    }
}
