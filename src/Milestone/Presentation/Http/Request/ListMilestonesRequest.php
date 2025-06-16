<?php

declare(strict_types=1);

namespace App\Milestone\Presentation\Http\Request;

use App\Milestone\Application\Query\ListMilestonesQuery;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\Positive;

readonly class ListMilestonesRequest
{
    public function __construct(
        #[Positive]
        public int $page = 1,
        #[Positive]
        #[LessThanOrEqual(ListMilestonesQuery::LIMIT_MAX)]
        public int $limit = ListMilestonesQuery::LIMIT_DEFAULT,
    ) {
    }
}
