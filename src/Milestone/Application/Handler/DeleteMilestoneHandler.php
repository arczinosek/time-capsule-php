<?php

declare(strict_types=1);

namespace App\Milestone\Application\Handler;

use App\Milestone\Application\Command\DeleteMilestoneCommand;
use App\Milestone\Application\Query\GetMilestoneQuery;
use App\Milestone\Infrastructure\Repository\MilestoneRepository;

readonly class DeleteMilestoneHandler
{
    public function __construct(
        private GetMilestoneHandler $getMilestoneHandler,
        private MilestoneRepository $repository,
    ) {
    }

    public function handle(DeleteMilestoneCommand $command): void
    {
        $milestone = $this->getMilestoneHandler->handle(
            new GetMilestoneQuery($command->milestoneId)
        );

        if ($milestone) {
            $this->repository->remove($milestone);
        }
    }
}
