<?php

declare(strict_types=1);

namespace App\Milestone\Infrastructure\Repository;

use App\Milestone\Domain\Model\Attachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Attachment>
 */
class AttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attachment::class);
    }

    public function save(Attachment $attachment, bool $flush = true): Attachment
    {
        $this->getEntityManager()->persist($attachment);

        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $attachment;
    }
}
