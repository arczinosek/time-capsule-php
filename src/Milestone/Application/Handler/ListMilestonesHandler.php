<?php

declare(strict_types=1);

namespace App\Milestone\Application\Handler;

use App\Milestone\Application\Query\ListMilestonesQuery;
use App\Milestone\Domain\Model\Milestone;
use App\Milestone\Infrastructure\Repository\MilestoneRepository;

readonly class ListMilestonesHandler
{
    public function __construct(
        private MilestoneRepository $milestoneRepository,
    ) {
    }

    /**
     * @return Milestone[]
     */
    public function handle(ListMilestonesQuery $query): array
    {
        return $this->milestoneRepository->findBy(
            [],
            ['startDate' => 'DESC'],
            $query->limit,
            ($query->page - 1) * $query->limit
        );
    }
}
