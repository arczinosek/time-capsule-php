<?php

declare(strict_types=1);

namespace App\Milestone\Application\Query;

use Exception;

use function sprintf;

readonly class ListMilestonesQuery
{
    public const LIMIT_MAX = 25;
    public const LIMIT_DEFAULT = 10;

    /**
     * @throws Exception
     */
    public function __construct(
        public int $page = 1,
        public int $limit = self::LIMIT_DEFAULT,
    ) {
        if ($limit > self::LIMIT_MAX) {
            throw new Exception(
                sprintf("limit is greater than allowed %d", self::LIMIT_MAX)
            );
        }

        if ($limit < 1) {
            throw new Exception('limit is not positive');
        }

        if ($page < 1) {
            throw new Exception('page is not positive');
        }
    }
}
