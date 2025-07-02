<?php

namespace App\Tests\unit\Milestone\Application\Handler;

use App\Milestone\Application\Command\DeleteMilestoneCommand;
use App\Milestone\Application\Event\MilestoneDeletedEvent;
use App\Milestone\Application\Handler\DeleteMilestoneHandler;
use App\Milestone\Application\Handler\GetMilestoneHandler;
use App\Milestone\Application\Query\GetMilestoneQuery;
use App\Milestone\Domain\Model\Milestone;
use App\Milestone\Infrastructure\Repository\MilestoneRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

final class DeleteMilestoneHandlerTest extends TestCase
{
    private DeleteMilestoneHandler $sut;
    private EventDispatcherInterface&MockObject $eventDispatcherMock;
    private GetMilestoneHandler&MockObject $getMilestoneHandlerMock;
    private LoggerInterface&MockObject $loggerMock;
    private MilestoneRepository&MockObject $repositoryMock;

    protected function setUp(): void
    {
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->getMilestoneHandlerMock = $this->createMock(GetMilestoneHandler::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->repositoryMock = $this->createMock(MilestoneRepository::class);
        $this->sut = new DeleteMilestoneHandler(
            $this->eventDispatcherMock,
            $this->getMilestoneHandlerMock,
            $this->loggerMock,
            $this->repositoryMock
        );
    }

    #[Test]
    public function handleShouldNotCallRepositoryNorEmitEventWhenMilestoneNotExists(): void
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

        $this->eventDispatcherMock
            ->expects(self::never())
            ->method('dispatch')
        ;

        $this->loggerMock
            ->expects(self::once())
            ->method('info')
            ->with(
                self::stringContains('nothing to delete'),
                ['milestoneId' => 1]
            )
        ;

        $this->sut->handle($command);
    }

    #[Test]
    public function handleShouldCallRepositoryAndEmitEventWhenMilestoneExists(): void
    {
        $command = new DeleteMilestoneCommand(2);
        $milestone = new Milestone();
        $milestone->addAttachment(
            'milestones/2/awesome-summer.jpg',
            'image/jpeg',
            123,
            'awesome-summer.jpg'
        );
        $expectedEvent = new MilestoneDeletedEvent(2, ['milestones/2/awesome-summer.jpg']);

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

        $this->eventDispatcherMock
            ->expects(self::once())
            ->method('dispatch')
            ->with($expectedEvent)
        ;

        $this->sut->handle($command);
    }
}
