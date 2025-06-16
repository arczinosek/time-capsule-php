<?php

declare(strict_types=1);

namespace App\Tests\unit\Milestone\Application\Query;

use App\Milestone\Application\Query\ListMilestonesQuery;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ListMilestonesQueryTest extends TestCase
{
    #[Test]
    public function shouldCreateQueryWithDefaultValues(): void
    {
        $query = new ListMilestonesQuery();

        self::assertSame(1, $query->page);
        self::assertSame(10, $query->limit);
    }

    #[Test]
    public function constructorShouldThrowExceptionWhenPageIsZero(): void
    {
        $this->expectExceptionMessage('page is not positive');

        new ListMilestonesQuery(0);
    }

    #[Test]
    public function constructorShouldThrowExceptionWhenPageIsNegativeNumber(): void
    {
        $this->expectExceptionMessage('page is not positive');

        new ListMilestonesQuery(-1);
    }

    #[Test]
    public function constructorShouldThrowExceptionWhenLimitIsNegativeNumber(): void
    {
        $this->expectExceptionMessage('limit is not positive');

        new ListMilestonesQuery(1, -1);
    }

    #[Test]
    public function constructorShouldThrowExceptionWhenLimitIsGreaterThanAllowedMax(): void
    {
        $this->expectExceptionMessage('limit is greater than allowed 25');

        new ListMilestonesQuery(1, 26);
    }
}
