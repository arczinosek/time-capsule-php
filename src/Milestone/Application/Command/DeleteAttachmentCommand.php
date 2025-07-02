<?php

declare(strict_types=1);

namespace App\Milestone\Application\Command;

readonly class DeleteAttachmentCommand
{
    public function __construct(
        public int $milestoneId,
        public int $attachmentId,
    ) {
    }
}
