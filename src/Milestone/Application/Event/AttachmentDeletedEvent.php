<?php

declare(strict_types=1);

namespace App\Milestone\Application\Event;

readonly class AttachmentDeletedEvent
{
    public function __construct(
        public string $filePath,
    ) {
    }
}
