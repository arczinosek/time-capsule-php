<?php

declare(strict_types=1);

namespace App\Milestone\Application\Event;

readonly class MilestoneDeletedEvent
{
    /**
     * @param string[] $attachmentStoredPaths
     */
    public function __construct(
        public int $milestoneId,
        public array $attachmentStoredPaths,
    ) {
    }
}
