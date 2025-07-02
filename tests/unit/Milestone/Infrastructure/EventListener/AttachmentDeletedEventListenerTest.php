<?php

declare(strict_types=1);

namespace App\Tests\unit\Milestone\Infrastructure\EventListener;

use App\Milestone\Application\Event\AttachmentDeletedEvent;
use App\Milestone\Application\Interface\AttachmentStorageService;
use App\Milestone\Infrastructure\EventListener\AttachmentDeletedEventListener;
use Error;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class AttachmentDeletedEventListenerTest extends TestCase
{
    private AttachmentDeletedEventListener $sut;
    private AttachmentStorageService&MockObject $storageServiceMock;
    private LoggerInterface&MockObject $loggerMock;

    protected function setUp(): void
    {
        $this->storageServiceMock = $this->createMock(AttachmentStorageService::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->sut = new AttachmentDeletedEventListener(
            $this->storageServiceMock,
            $this->loggerMock,
        );
    }

    #[Test]
    public function invokeHappyPath(): void
    {
        $filePath = 'milestone/x/event.jpg';
        $event = new AttachmentDeletedEvent($filePath);

        $this->storageServiceMock
            ->expects(self::once())
            ->method('delete')
            ->with($filePath)
        ;

        $this->loggerMock
            ->expects(self::once())
            ->method('info')
            ->with('Attachment deleted', ['path' => $filePath])
        ;

        ($this->sut)($event);
    }

    #[Test]
    public function invokeShouldLogWarningWhenDeleteThrowsException(): void
    {
        $event = new AttachmentDeletedEvent('file.wav');
        $exception = new Exception('something went wrong');

        $this->storageServiceMock
            ->expects(self::once())
            ->method('delete')
            ->willThrowException($exception)
        ;

        $this->loggerMock
            ->expects(self::once())
            ->method('warning')
            ->with(
                'Attachment delete failed',
                [
                    'path' => 'file.wav',
                    'message' => 'something went wrong',
                    'exception' => $exception,
                ]
            )
        ;

        ($this->sut)($event);
    }

    #[Test]
    public function invokeShouldLogErrorWhenDeleteThrowsThrowable(): void
    {
        $event = new AttachmentDeletedEvent('voices.mp3');
        $error = new Error('serious problem');

        $this->storageServiceMock
            ->expects(self::once())
            ->method('delete')
            ->willThrowException($error)
        ;

        $this->loggerMock
            ->expects(self::once())
            ->method('error')
            ->with(
                'Attachment delete failed',
                [
                    'path' => 'voices.mp3',
                    'message' => 'serious problem',
                    'exception' => $error,
                ]
            )
        ;

        ($this->sut)($event);
    }
}
