<?php

declare(strict_types=1);

namespace App\Milestone\Infrastructure\EventListener;

use App\Milestone\Application\Event\MilestoneDeletedEvent;
use App\Milestone\Application\Interface\AttachmentStorageService;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Throwable;

#[AsEventListener]
readonly class MilestoneDeletedEventListener
{
    public function __construct(
        private AttachmentStorageService $storageService,
        private LoggerInterface $logger,
    ) {
    }
    public function __invoke(MilestoneDeletedEvent $event): void
    {
        $this->logger->debug('MilestoneDeletedEvent handled', [
            'milestoneId' => $event->milestoneId,
            'attachments' => $event->attachmentStoredPaths,
        ]);

        foreach ($event->attachmentStoredPaths as $path) {
            $this->deleteAttachment($path);
        }

        $this->logger->info('Attachments of milestone deleted', [
            'milestoneId' => $event->milestoneId,
            'attachments' => $event->attachmentStoredPaths,
        ]);
    }

    private function deleteAttachment(string $path): void
    {
        try {
            $this->storageService->delete($path);
            $this->logger->info('Attachment deleted', [
                'path' => $path,
            ]);
        } catch (Exception $e) {
            $this->logger->warning('Attachment delete failed!', [
                'path' => $path,
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
        } catch (Throwable $throwable) {
            $this->logger->error('Attachment delete failed!', [
                'path' => $path,
                'message' => $throwable->getMessage(),
                'exception' => $throwable,
            ]);
        }
    }
}
