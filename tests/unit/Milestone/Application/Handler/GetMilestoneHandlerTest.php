<?php

declare(strict_types=1);

namespace App\Tests\unit\Milestone\Application\Handler;

use App\Milestone\Application\Handler\GetMilestoneHandler;
use App\Milestone\Application\Query\GetMilestoneQuery;
use App\Milestone\Domain\Model\Milestone;
use App\Milestone\Infrastructure\Repository\MilestoneRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GetMilestoneHandlerTest extends TestCase
{
    private GetMilestoneHandler $sut;
    private MilestoneRepository&MockObject $repositoryMock;

    protected function setUp(): void
    {
        $this->repositoryMock = $this->createMock(MilestoneRepository::class);
        $this->sut = new GetMilestoneHandler($this->repositoryMock);
    }

    #[Test]
    public function handleShouldReturnMilestoneWhenFoundInRepository(): void
    {
        $expectedMilestone = new Milestone();

        $query = new GetMilestoneQuery(1);

        $this->repositoryMock
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => 1])
            ->willReturn($expectedMilestone)
        ;

        $result = $this->sut->handle($query);

        self::assertSame($expectedMilestone, $result);
    }

    #[Test]
    public function handleShouldReturnNullWhenNotFoundInRepository(): void
    {
        $query = new GetMilestoneQuery(2);

        $this->repositoryMock
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['id' => 2])
            ->willReturn(null)
        ;

        $result = $this->sut->handle($query);

        self::assertNull($result);
    }
}
