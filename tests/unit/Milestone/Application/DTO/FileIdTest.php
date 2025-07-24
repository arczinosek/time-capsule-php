<?php

declare(strict_types=1);

namespace App\Tests\unit\Milestone\Application\DTO;

use App\Milestone\Application\DTO\FileId;
use App\Milestone\Application\Exception\CorruptedFileIdException;
use Base64Url\Base64Url;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class FileIdTest extends TestCase
{
    public static function correctFileIdProvider(): array
    {
        return [
            '4_12' => ['MTJfNA', 12, 4],
            '0_0' => ['MF8w', 0, 0],
            '666_666' => ['NjY2XzY2Ng', 666, 666],
        ];
    }

    #[Test]
    #[DataProvider('correctFileIdProvider')]
    public function encodeShouldReturnProperString(
        string $expectedResult,
        int $attachmentId,
        int $milestoneId,
    ): void {
        $fileId = new FileId($attachmentId, $milestoneId);

        self::assertEquals($expectedResult, $fileId->encode());
    }

    #[Test]
    public function toStringShouldReturnEncodedValue(): void
    {
        $expectedResult = 'MjAwXzE';

        $fileId = new FileId(200, 1);

        self::assertSame($expectedResult, $fileId->__toString());
    }

    #[Test]
    #[DataProvider('correctFileIdProvider')]
    public function decodeShouldCreateFileIdWithCorrectIds(
        string $encoded,
        int $expectedAttachmentId,
        int $expectedMilestoneId,
    ): void {
        $fileId = FileId::decode($encoded);

        self::assertEquals($expectedAttachmentId, $fileId->attachmentId);
        self::assertEquals($expectedMilestoneId, $fileId->milestoneId);
    }

    public static function corrutpedFileIdProvider(): array
    {
        return [
            'missing milestoneId' => ['100_'],
            'missing attachmentId' => ['_200'],
            'missing both ids' => ['_'],
            'non digit attachmentId' => ['a_200'],
            'non digit milestoneId' => ['200_f'],
            'non digit both ids' => ['a_f'],
            'garbage' => ['garbage'],
        ];
    }

    #[Test]
    #[DataProvider('corrutpedFileIdProvider')]
    public function decodeShouldThrowExceptionWhenDecodedStringHaveInvalidFormat(
        string $decoded
    ): void {
        $encoded = Base64Url::encode($decoded);

        $expectedException = new CorruptedFileIdException(
            'Corrupted fileId, cannot be decoded',
            $encoded,
            $decoded
        );

        self::expectExceptionObject($expectedException);

        FileId::decode($encoded);
    }
}
