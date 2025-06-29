<?php

declare(strict_types=1);

namespace App\Tests\unit\Milestone\Infrastructure\Service;

use App\Milestone\Infrastructure\Service\FlysystemAttachmentStorageService;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

use function sys_get_temp_dir;
use function tempnam;
use function unlink;

class FlysystemAttachmentStorageServiceTest extends TestCase
{
    private FlysystemAttachmentStorageService $sut;
    private FilesystemOperator&MockObject $flysystemStorageMock;
    private LoggerInterface&MockObject $loggerMock;

    protected function setUp(): void
    {
        $this->flysystemStorageMock = $this->createMock(FilesystemOperator::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->sut = new FlysystemAttachmentStorageService(
            $this->flysystemStorageMock,
            $this->loggerMock
        );
    }

    #[Test]
    public function uploadShouldCallFlysystemWithStreamFromSourceFile(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'test');
        $fileNameRegExp = '/^1\/[a-z0-9]{13}\-file\.ext$/';

        $this->flysystemStorageMock
            ->expects(self::once())
            ->method('writeStream')
            ->with(
                self::matchesRegularExpression($fileNameRegExp),
                self::isResource()
            )
        ;

        $result = $this->sut->upload($file, 1, 'file.ext');

        unlink($file);

        $this->assertMatchesRegularExpression($fileNameRegExp, $result->path);
    }

    #[Test]
    public function deleteHappyPath(): void
    {
        $this->flysystemStorageMock
            ->expects(self::once())
            ->method('delete')
            ->with('file.wav')
        ;

        $result = $this->sut->delete('file.wav');

        $this->assertTrue($result);
    }
}
