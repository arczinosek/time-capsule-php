<?php

namespace App\Tests\unit\Milestone\Application\Handler;

use App\Milestone\Application\Command\DeleteMilestoneCommand;
use App\Milestone\Application\Handler\DeleteMilestoneHandler;
use App\Milestone\Application\Handler\GetMilestoneHandler;
use App\Milestone\Application\Query\GetMilestoneQuery;
use App\Milestone\Domain\Model\Milestone;
use App\Milestone\Infrastructure\Repository\MilestoneRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class DeleteMilestoneHandlerTest extends TestCase
{
    private DeleteMilestoneHandler $sut;
    private GetMilestoneHandler&MockObject $getMilestoneHandlerMock;
    private MilestoneRepository&MockObject $repositoryMock;

    protected function setUp(): void
    {
        $this->getMilestoneHandlerMock = $this->createMock(GetMilestoneHandler::class);
        $this->repositoryMock = $this->createMock(MilestoneRepository::class);
        $this->sut = new DeleteMilestoneHandler(
            $this->getMilestoneHandlerMock,
            $this->repositoryMock
        );
    }

    #[Test]
    public function handleShouldNotCallRepositoryWhenMilestoneIsNotFound(): void
    {
        $command = new DeleteMilestoneCommand(1);

        $this->getMilestoneHandlerMock
            ->expects(self::once())
            ->method('handle')
            ->with(new GetMilestoneQuery(1))
            ->willReturn(null)
        ;

        $this->repositoryMock
            ->expects(self::never())
            ->method('remove')
        ;

        $this->sut->handle($command);
    }

    #[Test]
    public function handleShouldCallRepositoryWithFoundMilestone(): void
    {
        $command = new DeleteMilestoneCommand(2);
        $milestone = new Milestone();

        $this->getMilestoneHandlerMock
            ->expects(self::once())
            ->method('handle')
            ->with(new GetMilestoneQuery(2))
            ->willReturn($milestone)
        ;

        $this->repositoryMock
            ->expects(self::once())
            ->method('remove')
            ->with($milestone)
        ;

        $this->sut->handle($command);
    }
}
