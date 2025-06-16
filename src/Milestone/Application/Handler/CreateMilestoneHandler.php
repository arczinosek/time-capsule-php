<?php

declare(strict_types=1);

namespace App\Milestone\Application\Handler;

use App\Milestone\Application\Command\CreateMilestoneCommand;
use App\Milestone\Domain\Model\Milestone;
use App\Milestone\Infrastructure\Repository\MilestoneRepository;
use Exception;

readonly class CreateMilestoneHandler
{
    public function __construct(
        private MilestoneRepository $repository,
    ) {
    }

    /**
     * @throws Exception
     */
    public function handle(CreateMilestoneCommand $command): Milestone
    {
        $milestone = Milestone::create(
            $command->title,
            $command->description,
            $command->startDate,
            $command->finishDate
        );

        return $this->repository->save($milestone);
    }
}
