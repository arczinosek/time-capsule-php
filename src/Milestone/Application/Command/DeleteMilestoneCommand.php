<?php

declare(strict_types=1);

namespace App\Milestone\Application\Command;

readonly class DeleteMilestoneCommand
{
    public function __construct(
        public int $milestoneId,
    ) {
    }
}
