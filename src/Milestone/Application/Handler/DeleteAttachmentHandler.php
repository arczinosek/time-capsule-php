<?php

declare(strict_types=1);

namespace App\Milestone\Application\Handler;

use App\Milestone\Application\Command\DeleteAttachmentCommand;
use App\Milestone\Application\Common\GetMilestoneTrait;
use App\Milestone\Application\Event\AttachmentDeletedEvent;
use App\Milestone\Application\Exception\MilestoneNotFoundException;
use App\Milestone\Infrastructure\Repository\MilestoneRepository;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

readonly class DeleteAttachmentHandler
{
    use GetMilestoneTrait;

    public function __construct(
        private EventDispatcherInterface $eventDispatcher,
        private GetMilestoneHandler $getMilestoneHandler,
        private LoggerInterface $logger,
        private MilestoneRepository $milestoneRepository,
    ) {
    }

    /**
     * @throws MilestoneNotFoundException
     */
    public function handle(DeleteAttachmentCommand $command): void
    {
        $milestone = $this->getMilestone($command->milestoneId);
        $attachment = $milestone->getAttachmentById($command->attachmentId);

        if (!$attachment) {
            $this->logger->info('Attachment not found, nothing to delete', [
                'milestoneId' => $command->milestoneId,
                'attachmentId' => $command->attachmentId,
            ]);

            return ;
        }

        $milestone->removeAttachment($attachment);

        $this->milestoneRepository->save($milestone);

        $this->eventDispatcher->dispatch(
            new AttachmentDeletedEvent($attachment->getFilePath())
        );
    }
}
