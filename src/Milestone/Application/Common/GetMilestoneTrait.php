<?php

declare(strict_types=1);

namespace App\Milestone\Application\Common;

use App\Milestone\Application\Exception\MilestoneNotFoundException;
use App\Milestone\Application\Query\GetMilestoneQuery;
use App\Milestone\Domain\Model\Milestone;

trait GetMilestoneTrait
{
    /**
     * @throws MilestoneNotFoundException
     */
    private function getMilestone(int $milestoneId): Milestone
    {
        $milestone = $this->getMilestoneHandler->handle(
            new GetMilestoneQuery($milestoneId)
        );

        if (!$milestone) {
            throw MilestoneNotFoundException::forId($milestoneId);
        }

        return $milestone;
    }
}
