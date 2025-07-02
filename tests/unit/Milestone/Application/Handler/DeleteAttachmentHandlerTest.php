<?php

declare(strict_types=1);

namespace App\Tests\unit\Milestone\Application\Handler;

use App\Milestone\Application\Command\DeleteAttachmentCommand;
use App\Milestone\Application\Event\AttachmentDeletedEvent;
use App\Milestone\Application\Exception\MilestoneNotFoundException;
use App\Milestone\Application\Handler\DeleteAttachmentHandler;
use App\Milestone\Application\Handler\GetMilestoneHandler;
use App\Milestone\Application\Query\GetMilestoneQuery;
use App\Milestone\Domain\Model\Attachment;
use App\Milestone\Domain\Model\Milestone;
use App\Milestone\Infrastructure\Repository\MilestoneRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use ReflectionClass;

final class DeleteAttachmentHandlerTest extends TestCase
{
    private EventDispatcherInterface&MockObject $eventDispatcherMock;
    private GetMilestoneHandler&MockObject $getMilestoneHandlerMock;
    private LoggerInterface&MockObject $loggerMock;
    private MilestoneRepository&MockObject $milestoneRepositoryMock;
    private DeleteAttachmentHandler $sut;

    protected function setUp(): void
    {
        $this->eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);
        $this->getMilestoneHandlerMock = $this->createMock(GetMilestoneHandler::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->milestoneRepositoryMock = $this->createMock(MilestoneRepository::class);
        $this->sut = new DeleteAttachmentHandler(
            $this->eventDispatcherMock,
            $this->getMilestoneHandlerMock,
            $this->loggerMock,
            $this->milestoneRepositoryMock
        );
    }

    #[Test]
    public function handleShouldThrowExceptionWhenMilestoneNotExists(): void
    {
        $this->expectExceptionObject(
            MilestoneNotFoundException::forId(1)
        );

        $command = new DeleteAttachmentCommand(1, 2);

        $this->getMilestoneHandlerMock
            ->expects(self::once())
            ->method('handle')
            ->with(new GetMilestoneQuery(1))
            ->willReturn(null);
        ;

        $this->milestoneRepositoryMock
            ->expects(self::never())
            ->method('save')
        ;

        $this->eventDispatcherMock
            ->expects(self::never())
            ->method('dispatch')
        ;

        $this->sut->handle($command);
    }

    #[Test]
    public function handleShouldLogInfoAndDoNothingMoreWhenAttachmentNotExists(): void
    {
        $milestone = new Milestone();

        $this->getMilestoneHandlerMock
            ->method('handle')
            ->willReturn($milestone)
        ;

        $this->milestoneRepositoryMock
            ->expects(self::never())
            ->method('save')
        ;

        $this->eventDispatcherMock
            ->expects(self::never())
            ->method('dispatch')
        ;

        $this->sut->handle(new DeleteAttachmentCommand(1, 2));
    }

    #[Test]
    public function handleHappyPathShouldRemoveAttachmentAndDispatchEvent(): void
    {
        $milestone = new Milestone();
        $attachment = $milestone->addAttachment(
            'file.mp4',
            'video/mp4',
            987,
            'VIDEO_01233.mp4'
        );
        self::setAttachmentId($attachment, 105);

        $command = new DeleteAttachmentCommand(107, 105);

        $this->getMilestoneHandlerMock
            ->method('handle')
            ->with(new GetMilestoneQuery(107))
            ->willReturn($milestone)
        ;

        $this->milestoneRepositoryMock
            ->expects(self::once())
            ->method('save')
            ->with($milestone)
            ->willReturnArgument(0)
        ;

        $this->eventDispatcherMock
            ->expects(self::once())
            ->method('dispatch')
            ->with(new AttachmentDeletedEvent('file.mp4'))
        ;

        $this->sut->handle($command);

        $this->assertCount(0, $milestone->getAttachments());
        $this->assertNotNull($milestone->getUpdatedAt());
    }

    private static function setAttachmentId(Attachment $attachment, int $id): void
    {
        $attachmentReflection = new ReflectionClass($attachment);

        $idProperty = $attachmentReflection->getProperty('id');
        $idProperty->setValue($attachment, $id);
    }
}
