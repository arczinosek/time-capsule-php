<?php

declare(strict_types=1);

namespace App\Milestone\Application\Exception;

class AttachmentNotFoundException extends ResourceNotFoundException
{
    public function __construct(
        string $message,
        public readonly int $attachmentId,
        public readonly int $milestoneId,
    ) {
        parent::__construct($message);
    }

    public static function forId(int $attachmentId, int $milestoneId): self
    {
        return new self(
            "Attachment '$attachmentId' ($milestoneId) not found",
            $attachmentId,
            $milestoneId
        );
    }
}
