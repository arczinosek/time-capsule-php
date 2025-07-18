<?php

declare(strict_types=1);

namespace App\Tests\unit\Milestone\Application\DTO;

use App\Milestone\Application\DTO\AttachmentReadStream;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function fclose;
use function fopen;
use function fwrite;
use function rewind;
use function str_repeat;

class AttachmentReadStreamTest extends TestCase
{
    #[Test]
    public function constructorShouldThrowExceptionWhenGivenResourceIsNotResource(): void
    {
        self::expectException(InvalidArgumentException::class);

        $stream = fopen('php://memory', 'r');
        fclose($stream);

        new AttachmentReadStream($stream);
    }

    #[Test]
    public function getIteratorShouldReturnIteratorToReadStreamContents(): void
    {
        $expectedFileContents = str_repeat('a', 511);

        $stream = fopen('php://memory', 'rw');
        fwrite($stream, $expectedFileContents);
        rewind($stream);

        $sut = new AttachmentReadStream($stream, 'text/plain');

        self::assertEquals('text/plain', $sut->mimeType);

        $iterator = $sut->getIterator(256);

        self::assertIsIterable($iterator);

        $fileContents = '';
        $iterations = 0;

        foreach ($iterator as $chunk) {
            $fileContents .= $chunk;
            ++$iterations;
        }

        self::assertSame(2, $iterations);
        self::assertEquals($expectedFileContents, $fileContents);
    }
}
