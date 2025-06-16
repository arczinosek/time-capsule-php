<?php

declare(strict_types=1);

namespace App\Milestone\Application\Query;

readonly class GetMilestoneQuery
{
    public function __construct(
        public int $milestoneId,
    ) {
    }
}
