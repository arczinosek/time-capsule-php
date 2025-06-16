<?php

declare(strict_types=1);

namespace App\Milestone\Application\Command;

use DateTimeImmutable;

readonly class CreateMilestoneCommand
{
    public function __construct(
        public string $title,
        public string $description,
        public DateTimeImmutable $startDate,
        public DateTimeImmutable $finishDate,
    ) {
    }
}
