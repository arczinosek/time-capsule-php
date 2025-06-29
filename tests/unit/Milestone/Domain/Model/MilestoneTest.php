<?php

declare(strict_types=1);

namespace App\Tests\unit\Milestone\Domain\Model;

use App\Milestone\Domain\Exception\TooManyAttachmentsException;
use App\Milestone\Domain\Model\Milestone;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MilestoneTest extends TestCase
{
    private const ATTACHMENTS_LIMIT = 20;

    #[Test]
    public function setTitleShouldThrowExceptionWhenTitleIsTooShort(): void
    {
        $milestone = new Milestone();
        $this->expectExceptionMessage('Title is too short!');

        $milestone->setTitle('tw');
    }

    #[Test]
    public function setTitleShouldThrowExceptionWhenTitleIsTooLong(): void
    {
        $milestone = new Milestone();
        $this->expectExceptionMessage('Title is too long!');

        $milestone->setTitle(str_repeat('x', 128));
    }

    #[Test]
    public function setTitleHappyPath(): void
    {
        $milestone = new Milestone();
        $milestone->setTitle('some title');

        $this->assertNotNull($milestone->getUpdatedAt());
    }

    #[Test]
    public function setPeriodShouldThrowExceptionWhenFinishDateIsBeforeStartDate(): void
    {
        $milestone = new Milestone();

        $this->expectExceptionMessage('Start date cannot be after finish date!');

        $milestone->setPeriod(
            new DateTimeImmutable('2025-06-16'),
            new DateTimeImmutable('2025-06-10')
        );
    }

    #[Test]
    public function setPeriodHappyPath(): void
    {
        $milestone = new Milestone();
        $startDate = new DateTimeImmutable('2025-06-29 21:39:00');
        $finishDate = new DateTimeImmutable('2025-06-29 21:40:03');

        $milestone->setPeriod($startDate, $finishDate);

        $this->assertSame($startDate, $milestone->getStartDate());
        $this->assertSame($finishDate, $milestone->getFinishDate());
        $this->assertNotNull($milestone->getUpdatedAt());
    }

    #[Test]
    public function setDescriptionHappyPath(): void
    {
        $milestone = new Milestone();
        $description = 'some description';

        $milestone->setDescription($description);

        $this->assertSame($description, $milestone->getDescription());
        $this->assertNotNull($milestone->getUpdatedAt());
    }

    #[Test]
    public function addAttachmentHappyPath(): void
    {
        $milestone = new Milestone();

        $attachment = $milestone->addAttachment(
            '/storage/milestone/uploaded_file.jpg',
            'image/jpeg',
            666,
            'awesome_summer.jpg',
            'awesome summer \'25'
        );

        $this->assertNotNull($milestone->getUpdatedAt());
        $this->assertSame($milestone, $attachment->getMilestone());
        $this->assertSame('/storage/milestone/uploaded_file.jpg', $attachment->getFilePath());
        $this->assertSame('image/jpeg', $attachment->getFileMimeType());
        $this->assertSame(666, $attachment->getFileSizeBytes());
        $this->assertSame('awesome_summer.jpg', $attachment->getOriginalFileName());
        $this->assertSame('awesome summer \'25', $attachment->getDescription());
    }

    #[Test]
    public function addAttachmentShouldThrowExceptionWhenLimitOfAttachmentsAchieved(): void
    {
        $expectedException = TooManyAttachmentsException::create(
            '/storage/milestone/uploaded_file_z.jpg',
            self::ATTACHMENTS_LIMIT
        );
        $milestone = new Milestone();

        for ($i = 0; $i < self::ATTACHMENTS_LIMIT; $i++) {
            $milestone->addAttachment(
                '/storage/milestone/uploaded_file.jpg',
                'image/jpeg',
                ($i + 1) * 2,
                'awesome_summer.jpg',
            );
        }

        $lastUpdatedAt = $milestone->getUpdatedAt();

        $this->expectExceptionObject($expectedException);

        $milestone->addAttachment(
            '/storage/milestone/video.wav',
            'image/wav',
            987,
            'awesome_summer.wav',
        );

        $this->assertCount(self::ATTACHMENTS_LIMIT, $milestone->getAttachments());
        $this->assertSame($lastUpdatedAt, $milestone->getUpdatedAt());
    }
}
