<?php

declare(strict_types=1);

namespace App\Milestone\Presentation\Http\Request;

use Symfony\Component\Validator\Constraints\Positive;

readonly class GetMilestoneRequest
{
    public function __construct(
        #[Positive]
        public int $milestoneId,
    ) {
    }
}
