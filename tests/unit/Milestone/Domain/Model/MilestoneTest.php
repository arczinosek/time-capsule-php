<?php

declare(strict_types=1);

namespace App\Tests\unit\Milestone\Domain\Model;

use App\Milestone\Domain\Model\Milestone;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MilestoneTest extends TestCase
{
    #[Test]
    public function setTitleShouldThrowExceptionWhenTitleIsTooShort(): void
    {
        $milestone = new Milestone();
        $this->expectExceptionMessage('Title is too short!');

        $milestone->setTitle('tw');
    }

    #[Test]
    public function setTitleShouldThrowExceptionWhenTitleIsTooLong(): void
    {
        $milestone = new Milestone();
        $this->expectExceptionMessage('Title is too long!');

        $milestone->setTitle(str_repeat('x', 128));
    }

    #[Test]
    public function setPeriodShouldThrowExceptionWhenFinishDateIsBeforeStartDate(): void
    {
        $milestone = new Milestone();

        $this->expectExceptionMessage('Start date cannot be after finish date!');

        $milestone->setPeriod(
            new DateTimeImmutable('2025-06-16'),
            new DateTimeImmutable('2025-06-10')
        );
    }
}
