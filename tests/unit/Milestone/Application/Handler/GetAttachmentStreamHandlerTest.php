<?php

declare(strict_types=1);

namespace App\Tests\unit\Milestone\Application\Handler;

use App\Milestone\Application\DTO\AttachmentReadStream;
use App\Milestone\Application\DTO\FileId;
use App\Milestone\Application\Exception\AttachmentNotFoundException;
use App\Milestone\Application\Handler\GetAttachmentStreamHandler;
use App\Milestone\Application\Handler\GetMilestoneHandler;
use App\Milestone\Application\Interface\AttachmentStorageService;
use App\Milestone\Application\Query\GetAttachmentStreamQuery;
use App\Milestone\Application\Query\GetMilestoneQuery;
use App\Milestone\Domain\Model\Milestone;
use App\Tests\Helper\Reflection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

use function fopen;

class GetAttachmentStreamHandlerTest extends TestCase
{
    private GetMilestoneHandler&MockObject $getMilestoneHandlerMock;
    private AttachmentStorageService&MockObject $attachmentStorageServiceMock;
    private GetAttachmentStreamHandler $sut;

    protected function setUp(): void
    {
        $this->getMilestoneHandlerMock = $this->createMock(GetMilestoneHandler::class);
        $this->attachmentStorageServiceMock = $this->createMock(AttachmentStorageService::class);
        $this->sut = new GetAttachmentStreamHandler(
            $this->getMilestoneHandlerMock,
            $this->attachmentStorageServiceMock
        );
    }

    #[Test]
    public function handleShouldReturnProperStreamObject(): void
    {
        $stream = fopen('php://memory', 'r');
        $milestone = new Milestone();
        $attachment = $milestone->addAttachment(
            '/storage/file.pdf',
            'application/pdf',
            123456,
            'historic-document.pdf',
        );
        Reflection::setObjectProperty($milestone, 123);
        Reflection::setObjectProperty($attachment, 321);

        $this->getMilestoneHandlerMock
            ->expects(self::once())
            ->method('handle')
            ->with(new GetMilestoneQuery(123))
            ->willReturn($milestone)
        ;

        $this->attachmentStorageServiceMock
            ->expects(self::once())
            ->method('getReadStream')
            ->with('/storage/file.pdf')
            ->willReturn($stream)
        ;

        $expectedReadStream = new AttachmentReadStream(
            $stream,
            'application/pdf'
        );

        $readStream = $this->sut->handle(
            new GetAttachmentStreamQuery(new FileId(321, 123))
        );

        self::assertEquals($expectedReadStream, $readStream);
    }

    #[Test]
    public function handleShouldThrowExceptionWhenMilestoneHaveNoAttachmentWithGivenId(): void
    {
        $fileId = new FileId(1, 123);
        $milestone = new Milestone();
        Reflection::setObjectProperty($milestone, $fileId->milestoneId);

        $this->getMilestoneHandlerMock
            ->expects(self::once())
            ->method('handle')
            ->with(new GetMilestoneQuery(123))
            ->willReturn($milestone)
        ;

        $this->attachmentStorageServiceMock
            ->expects(self::never())
            ->method('getReadStream')
        ;

        self::expectExceptionObject(
            AttachmentNotFoundException::forFileId($fileId)
        );

        $this->sut->handle(new GetAttachmentStreamQuery($fileId));
    }
}
