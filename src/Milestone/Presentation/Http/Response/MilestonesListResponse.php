<?php

declare(strict_types=1);

namespace App\Milestone\Presentation\Http\Response;

use App\Milestone\Domain\Model\Milestone;

readonly class MilestonesListResponse
{
    /**
     * @param MilestoneResponse[] $milestones
     */
    public function __construct(
        public array $milestones,
    ) {
    }

    /**
     * @param Milestone[] $milestones
     * @return self
     */
    public static function createFromArray(array $milestones): self
    {
        return new self(
            array_map(
                fn (Milestone $milestone) => MilestoneResponse::createFromEntity($milestone),
                $milestones
            )
        );
    }
}
