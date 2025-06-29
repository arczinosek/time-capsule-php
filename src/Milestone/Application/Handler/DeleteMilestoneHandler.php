<?php

declare(strict_types=1);

namespace App\Milestone\Application\Handler;

use App\Milestone\Application\Command\DeleteMilestoneCommand;
use App\Milestone\Application\Event\MilestoneDeletedEvent;
use App\Milestone\Application\Query\GetMilestoneQuery;
use App\Milestone\Domain\Model\Attachment;
use App\Milestone\Infrastructure\Repository\MilestoneRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

readonly class DeleteMilestoneHandler
{
    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private GetMilestoneHandler $getMilestoneHandler,
        private LoggerInterface $logger,
        private MilestoneRepository $repository,
    ) {
    }

    public function handle(DeleteMilestoneCommand $command): void
    {
        $milestone = $this->getMilestoneHandler->handle(
            new GetMilestoneQuery($command->milestoneId)
        );

        if ($milestone === null) {
            $this->logger->info('Milestone not found, nothing to delete', [
                'milestoneId' => $command->milestoneId,
            ]);

            return;
        }

        $attachmentPaths = $milestone
            ->getAttachments()
            ->map(fn (Attachment $attachment): string => $attachment->getFilePath())
            ->toArray()
        ;

        $this->repository->remove($milestone);

        $this->logger->info('Milestone deleted', [
            'milestoneId' => $command->milestoneId,
            'attachmentPaths' => $attachmentPaths,
        ]);

        $this->eventDispatcher->dispatch(
            new MilestoneDeletedEvent($command->milestoneId, $attachmentPaths)
        );
    }
}
