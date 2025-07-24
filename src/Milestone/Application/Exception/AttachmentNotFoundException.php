<?php

declare(strict_types=1);

namespace App\Milestone\Application\Exception;

use App\Milestone\Application\DTO\FileId;

class AttachmentNotFoundException extends ResourceNotFoundException
{
    public function __construct(
        string $message,
        public readonly int $attachmentId,
        public readonly int $milestoneId,
    ) {
        parent::__construct($message);
    }

    public static function forFileId(FileId $fileId): self
    {
        return new self(
            "Attachment '$fileId->attachmentId' ($fileId->milestoneId) not found",
            $fileId->attachmentId,
            $fileId->milestoneId
        );
    }
}
