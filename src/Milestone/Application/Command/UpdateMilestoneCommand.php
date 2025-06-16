<?php

declare(strict_types=1);

namespace App\Milestone\Application\Command;

use DateTimeImmutable;

readonly class UpdateMilestoneCommand
{
    public function __construct(
        public int $milestoneId,
        public ?string $title = null,
        public ?string $description = null,
        public ?DateTimeImmutable $startDate = null,
        public ?DateTimeImmutable $finishDate = null,
    ) {
    }
}
