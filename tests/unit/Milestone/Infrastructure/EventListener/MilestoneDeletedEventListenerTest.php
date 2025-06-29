<?php

declare(strict_types=1);

namespace App\Tests\unit\Milestone\Infrastructure\EventListener;

use App\Milestone\Application\Event\MilestoneDeletedEvent;
use App\Milestone\Application\Interface\AttachmentStorageService;
use App\Milestone\Infrastructure\EventListener\MilestoneDeletedEventListener;
use Error;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class MilestoneDeletedEventListenerTest extends TestCase
{
    private MilestoneDeletedEventListener $sut;

    private AttachmentStorageService&MockObject $storageServiceMock;
    private LoggerInterface&MockObject $loggerMock;

    protected function setUp(): void
    {
        $this->storageServiceMock = $this->createMock(AttachmentStorageService::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->sut = new MilestoneDeletedEventListener(
            $this->storageServiceMock,
            $this->loggerMock
        );
    }

    #[Test]
    public function happyPath(): void
    {
        $event = new MilestoneDeletedEvent(1, [
            'file1', 'file2'
        ]);

        $file1DeleteCount = 0;
        $file2DeleteCount = 0;

        $this->storageServiceMock
            ->expects(self::exactly(2))
            ->method('delete')
            ->willReturnCallback(function ($filePath) use (&$file1DeleteCount, &$file2DeleteCount) {
                if ($filePath === 'file1') {
                    ++$file1DeleteCount;
                }

                if ($filePath === 'file2') {
                    ++$file2DeleteCount;
                }

                return true;
            });

        $this->storageServiceMock
            ->expects(self::exactly(2))
            ->method('delete')
        ;

        ($this->sut)($event);

        $this->assertSame(1, $file1DeleteCount, 'file1 deletion count');
        $this->assertSame(1, $file2DeleteCount);
    }

    #[Test]
    public function invokeShouldLogWarningWhenDeleteThrowsExceptionAndDoNotBreakExecution(): void
    {
        $event = new MilestoneDeletedEvent(2, ['file1', 'file2']);

        $this->loggerMock
            ->expects(self::once())
            ->method('warning')
        ;

        $file1DeleteCount = 0;
        $file2DeleteCount = 0;

        $this->storageServiceMock
            ->expects(self::exactly(2))
            ->method('delete')
            ->willReturnCallback(
                function ($filePath) use (&$file1DeleteCount, &$file2DeleteCount) {
                    if ($filePath === 'file1') {
                        ++$file1DeleteCount;
                        throw new Exception();
                    }

                    if ($filePath === 'file2') {
                        ++$file2DeleteCount;
                    }

                    return true;
                }
            )
        ;

        ($this->sut)($event);

        $this->assertSame(1, $file1DeleteCount);
        $this->assertSame(1, $file2DeleteCount);
    }

    #[Test]
    public function invokeShouldLogErrorWhenDeleteThrowsThrowableAndDoNotBreakExecution(): void
    {
        $event = new MilestoneDeletedEvent(3, ['file1', 'file2']);

        $this->loggerMock
            ->expects(self::once())
            ->method('error')
        ;

        $file1DeleteCount = 0;
        $file2DeleteCount = 0;

        $this->storageServiceMock
            ->expects(self::exactly(2))
            ->method('delete')
            ->willReturnCallback(
                function ($filePath) use (&$file1DeleteCount, &$file2DeleteCount) {
                    if ($filePath === 'file1') {
                        ++$file1DeleteCount;
                        throw new Error();
                    }

                    if ($filePath === 'file2') {
                        ++$file2DeleteCount;
                    }

                    return true;
                }
            )
        ;

        ($this->sut)($event);

        $this->assertSame(1, $file1DeleteCount);
        $this->assertSame(1, $file2DeleteCount);
    }
}
