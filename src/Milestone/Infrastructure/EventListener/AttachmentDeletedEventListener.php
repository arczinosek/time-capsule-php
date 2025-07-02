<?php

declare(strict_types=1);

namespace App\Milestone\Infrastructure\EventListener;

use App\Milestone\Application\Event\AttachmentDeletedEvent;
use App\Milestone\Application\Interface\AttachmentStorageService;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Throwable;

#[AsEventListener]
readonly class AttachmentDeletedEventListener
{
    public function __construct(
        private AttachmentStorageService $storageService,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(AttachmentDeletedEvent $event): void
    {
        try {
            $this->storageService->delete($event->filePath);
            $this->logger->info('Attachment deleted', [
                'path' => $event->filePath,
            ]);
        } catch (Exception $e) {
            $this->logger->warning('Attachment delete failed', [
                'path' => $event->filePath,
                'message' => $e->getMessage(),
                'exception' => $e,
            ]);
        } catch (Throwable $throwable) {
            $this->logger->error('Attachment delete failed', [
                'path' => $event->filePath,
                'message' => $throwable->getMessage(),
                'exception' => $throwable,
            ]);
        }
    }
}
