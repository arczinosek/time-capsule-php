<?php

declare(strict_types=1);

namespace App\Milestone\Application\Handler;

use App\Milestone\Application\Query\GetMilestoneQuery;
use App\Milestone\Domain\Model\Milestone;
use App\Milestone\Infrastructure\Repository\MilestoneRepository;

readonly class GetMilestoneHandler
{
    public function __construct(private MilestoneRepository $repository)
    {
    }

    public function handle(GetMilestoneQuery $query): ?Milestone
    {
        return $this->repository->findOneBy(['id' => $query->milestoneId]);
    }
}
