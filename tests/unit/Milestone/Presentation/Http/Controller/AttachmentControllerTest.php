<?php

declare(strict_types=1);

namespace App\Tests\unit\Milestone\Presentation\Http\Controller;

use App\Milestone\Application\Command\DeleteAttachmentCommand;
use App\Milestone\Application\Exception\MilestoneNotFoundException;
use App\Milestone\Application\Handler\AddAttachmentHandler;
use App\Milestone\Application\Handler\DeleteAttachmentHandler;
use App\Milestone\Presentation\Http\Controller\AttachmentController;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AttachmentControllerTest extends TestCase
{
    private AddAttachmentHandler&MockObject $addAttachmentHandlerMock;
    private DeleteAttachmentHandler&MockObject $deleteAttachmentHandlerMock;
    private LoggerInterface&MockObject $loggerMock;
    private AttachmentController $sut;

    protected function setUp(): void
    {
        $this->addAttachmentHandlerMock = $this->createMock(AddAttachmentHandler::class);
        $this->deleteAttachmentHandlerMock = $this->createMock(DeleteAttachmentHandler::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->sut = new AttachmentController(
            $this->addAttachmentHandlerMock,
            $this->deleteAttachmentHandlerMock,
            $this->loggerMock,
        );
    }

    #[Test]
    public function deleteAttachmentShouldRespondWithNoContentOnHappyPath(): void
    {
        $expectedResponse = new Response(status: 204);

        $this->deleteAttachmentHandlerMock
            ->expects(self::once())
            ->method('handle')
            ->with(new DeleteAttachmentCommand(42, 45))
        ;

        $response = $this->sut->deleteAttachment(42, 45);

        self::assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function deleteAttachmentShouldThrowNotFoundExceptionWhenHandlerThrowsException(): void
    {
        $notFoundException = MilestoneNotFoundException::forId(55);

        $expectedException = new NotFoundHttpException($notFoundException->getMessage());
        $this->expectExceptionObject($expectedException);

        $this->deleteAttachmentHandlerMock
            ->expects(self::once())
            ->method('handle')
            ->with(new DeleteAttachmentCommand(55, 60))
            ->willThrowException($notFoundException)
        ;

        $this->sut->deleteAttachment(55, 60);
    }
}
