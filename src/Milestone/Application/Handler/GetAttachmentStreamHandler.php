<?php

declare(strict_types=1);

namespace App\Milestone\Application\Handler;

use App\Milestone\Application\Common\GetMilestoneTrait;
use App\Milestone\Application\DTO\AttachmentReadStream;
use App\Milestone\Application\Exception\AttachmentNotFoundException;
use App\Milestone\Application\Exception\FileReadException;
use App\Milestone\Application\Exception\MilestoneNotFoundException;
use App\Milestone\Application\Interface\AttachmentStorageService;
use App\Milestone\Application\Query\GetAttachmentStreamQuery;

class GetAttachmentStreamHandler
{
    use GetMilestoneTrait;

    public function __construct(
        private readonly GetMilestoneHandler $getMilestoneHandler,
        private readonly AttachmentStorageService $attachmentStorageService,
    ) {
    }

    /**
     * @throws MilestoneNotFoundException
     * @throws FileReadException
     * @throws AttachmentNotFoundException
     */
    public function handle(GetAttachmentStreamQuery $query): AttachmentReadStream
    {
        $fileId = $query->fileId;
        $milestone = $this->getMilestone($fileId->milestoneId);
        $attachment = $milestone->getAttachmentById($fileId->attachmentId);

        if (!$attachment) {
            throw AttachmentNotFoundException::forFileId($query->fileId);
        }

        $resource = $this->attachmentStorageService->getReadStream($attachment->getFilePath());

        return new AttachmentReadStream(
            $resource,
            $attachment->getFileMimeType()
        );
    }
}
