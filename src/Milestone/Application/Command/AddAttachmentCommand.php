<?php

declare(strict_types=1);

namespace App\Milestone\Application\Command;

readonly class AddAttachmentCommand
{
    public function __construct(
        public int $milestoneId,
        public string $uploadedFilePath,
        public string $originalFileName,
        public string $originalMimeType,
        public ?string $attachmentDescription,
    ) {
    }
}
