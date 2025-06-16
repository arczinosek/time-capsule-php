<?php

declare(strict_types=1);

namespace App\Milestone\Application\Handler;

use App\Milestone\Application\Command\UpdateMilestoneCommand;
use App\Milestone\Application\Exception\MilestoneNotFoundException;
use App\Milestone\Application\Query\GetMilestoneQuery;
use App\Milestone\Domain\Model\Milestone;
use App\Milestone\Infrastructure\Repository\MilestoneRepository;
use Exception;
use Psr\Log\LoggerInterface;

readonly class UpdateMilestoneHandler
{
    public function __construct(
        private MilestoneRepository $repository,
        private GetMilestoneHandler $getMilestoneHandler,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws MilestoneNotFoundException
     */
    public function handle(UpdateMilestoneCommand $command): Milestone
    {
        $this->logger->debug('UpdateMilestoneCommand', ['command' => $command]);

        $milestone = $this->getMilestoneHandler->handle(
            new GetMilestoneQuery($command->milestoneId)
        );

        if (!$milestone) {
            throw MilestoneNotFoundException::forId($command->milestoneId);
        }

        if ($command->title !== null) {
            $milestone->setTitle($command->title);
        }

        if ($command->description !== null) {
            $milestone->setDescription($command->description);
        }

        if ($command->startDate || $command->finishDate) {
            $milestone->setPeriod(
                $command->startDate ?? $milestone->getStartDate(),
                $command->finishDate ?? $milestone->getFinishDate()
            );
        }

        $milestone->touch();

        $this->repository->save($milestone);

        return $milestone;
    }
}
