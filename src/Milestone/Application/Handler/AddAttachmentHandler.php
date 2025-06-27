<?php

declare(strict_types=1);

namespace App\Milestone\Application\Handler;

use App\Milestone\Application\Command\AddAttachmentCommand;
use App\Milestone\Application\Exception\FileUploadFailedException;
use App\Milestone\Application\Exception\MilestoneNotFoundException;
use App\Milestone\Application\Interface\AttachmentStorageService;
use App\Milestone\Application\Query\GetMilestoneQuery;
use App\Milestone\Domain\Exception\TooManyAttachmentsException;
use App\Milestone\Domain\Model\Attachment;
use App\Milestone\Domain\Model\Milestone;
use App\Milestone\Infrastructure\Repository\MilestoneRepository;
use Exception;
use Psr\Log\LoggerInterface;

final readonly class AddAttachmentHandler
{
    public function __construct(
        private GetMilestoneHandler $getMilestoneHandler,
        private LoggerInterface $logger,
        private MilestoneRepository $milestoneRepository,
        private AttachmentStorageService $storage,
    ) {
    }

    /**
     * @throws FileUploadFailedException
     * @throws MilestoneNotFoundException
     * @throws TooManyAttachmentsException
     */
    public function handle(AddAttachmentCommand $command): Attachment
    {
        $milestone = $this->getMilestone($command->milestoneId);

        $uploadedFileInfo = $this->storage->upload(
            $command->uploadedFilePath,
            $command->milestoneId,
            $command->originalFileName,
        );

        try {
            $attachment = $milestone->addAttachment(
                $uploadedFileInfo->path,
                $uploadedFileInfo->mimeType ?? $command->originalMimeType,
                $uploadedFileInfo->size ?? 0,
                $command->originalFileName,
                $command->attachmentDescription
            );
            $this->milestoneRepository->save($milestone);

            return $attachment;
        } catch (Exception $exception) {
            $this->logger->warning(
                'Failed to add attachment, uploaded file will be deleted',
                [
                    'milestoneId' => $command->milestoneId,
                    'error' => $exception->getMessage(),
                    'file' => $uploadedFileInfo,
                ]
            );

            // Warning!
            // An exception occurred while executing a query:
            // SQLSTATE[23000]: Integrity constraint violation:
            // 1062 Duplicate entry 'aeroplane.jpg' for key 'UNIQ_795FD9BB82A8E361'
            $this->storage->delete($uploadedFileInfo->path);

            throw $exception;
        }
    }

    /**
     * @throws MilestoneNotFoundException
     */
    private function getMilestone(int $milestoneId): Milestone
    {
        $milestone = $this->getMilestoneHandler->handle(
            new GetMilestoneQuery($milestoneId)
        );

        if (!$milestone) {
            throw MilestoneNotFoundException::forId($milestoneId);
        }

        return $milestone;
    }
}
