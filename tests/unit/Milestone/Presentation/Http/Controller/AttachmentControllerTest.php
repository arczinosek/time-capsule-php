<?php

declare(strict_types=1);

namespace App\Tests\unit\Milestone\Presentation\Http\Controller;

use App\Milestone\Application\DTO\AttachmentReadStream;
use App\Milestone\Application\DTO\FileId;
use App\Milestone\Application\Exception\FileReadException;
use App\Milestone\Application\Exception\ResourceNotFoundException;
use App\Milestone\Application\Handler\GetAttachmentStreamHandler;
use App\Milestone\Application\Query\GetAttachmentStreamQuery;
use App\Milestone\Presentation\Http\Controller\AttachmentController;
use Base64Url\Base64Url;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentControllerTest extends TestCase
{
    private GetAttachmentStreamHandler&MockObject $getAttachmentStreamHandlerMock;
    private LoggerInterface&MockObject $loggerMock;
    private AttachmentController $sut;

    protected function setUp(): void
    {
        $this->getAttachmentStreamHandlerMock = $this->createMock(GetAttachmentStreamHandler::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->sut = new AttachmentController(
            $this->getAttachmentStreamHandlerMock,
            $this->loggerMock,
        );
    }

    #[Test]
    public function getFileStreamShouldReturnStreamedResponseForCorrectFileId(): void
    {
        $stream = fopen('php://memory', 'r');
        $attachmentReadStream = new AttachmentReadStream($stream, 'image/jpeg');
        $fileId = new FileId(100, 25);

        $this->getAttachmentStreamHandlerMock
            ->expects(self::once())
            ->method('handle')
            ->with(new GetAttachmentStreamQuery($fileId))
            ->willReturn($attachmentReadStream)
        ;

        $expectedResponse = new StreamedResponse(
            $attachmentReadStream->getIterator(),
            headers: [
                'Content-Transfer-Encoding' => 'binary',
                'Content-Type' => 'image/jpeg',
            ]
        );

        $response = $this->sut->getFileStream($fileId->encode());

        self::assertEquals($expectedResponse, $response);
    }

    public static function corruptedFileIdProvider(): array
    {
        return [
            'not valid base64' => ['foo:bar:baz'],
            'not valid fileId' => [Base64Url::encode('foo:bar:baz')],
        ];
    }

    #[Test]
    #[DataProvider('corruptedFileIdProvider')]
    public function getFileStreamShouldReturnBadRequestWhenFileIdIsCorrupted(
        string $corruptedFileId
    ): void {
        $this->getAttachmentStreamHandlerMock
            ->expects(self::never())
            ->method('handle')
        ;

        $expectedResponse = new Response(
            'Corrupted fileId provided',
            400
        );

        $response = $this->sut->getFileStream($corruptedFileId);

        self::assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function getFileStreamShouldReturnInternalServerErrorWhenHandlerThrowsFileReadException(): void
    {
        $fileId = new FileId(2, 1);

        $this->getAttachmentStreamHandlerMock
            ->expects(self::once())
            ->method('handle')
            ->with(new GetAttachmentStreamQuery($fileId))
            ->willThrowException(new FileReadException())
        ;

        $this->loggerMock
            ->expects(self::once())
            ->method('warning')
            ->with(
                'Failed to read attachment file',
                self::isArray()
            )
        ;

        $expectedResponse = new Response('Internal Server Error', 500);

        $response = $this->sut->getFileStream($fileId->encode());

        self::assertEquals($expectedResponse, $response);
    }

    #[Test]
    public function getFileStreamShouldReturnNotFoundWhenHandlerThrowsResourceNotFoundException(): void
    {
        $fileId = new FileId(18, 7);

        $this->getAttachmentStreamHandlerMock
            ->expects(self::once())
            ->method('handle')
            ->with(new GetAttachmentStreamQuery($fileId))
            ->willThrowException(
                new ResourceNotFoundException('attachment not found')
            )
        ;

        $expectedResponse = new Response('attachment not found', 404);

        $response = $this->sut->getFileStream($fileId->encode());

        self::assertEquals($expectedResponse, $response);
    }
}
