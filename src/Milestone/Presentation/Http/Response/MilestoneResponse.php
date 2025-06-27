<?php

declare(strict_types=1);

namespace App\Milestone\Presentation\Http\Response;

use App\Milestone\Domain\Model\Attachment;
use App\Milestone\Domain\Model\Milestone;
use DateTimeInterface;

use function array_map;

readonly class MilestoneResponse
{
    /**
     * @param AttachmentResponse[] $attachments
     */
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public string $startDate,
        public string $finishDate,
        public array $attachments,
        public string $createdAt,
        public ?string $updatedAt,
    ) {
    }

    public static function createFromEntity(Milestone $milestone): self
    {
        return new self(
            $milestone->getId(),
            $milestone->getTitle(),
            $milestone->getDescription(),
            $milestone->getStartDate()->format(DateTimeInterface::ATOM),
            $milestone->getFinishDate()->format(DateTimeInterface::ATOM),
            array_map(
                fn (Attachment $attachment): AttachmentResponse =>
                    AttachmentResponse::createFromEntity($attachment),
                $milestone->getAttachments()->toArray()
            ),
            $milestone->getCreatedAt()->format(DateTimeInterface::ATOM),
            $milestone->getUpdatedAt()?->format(DateTimeInterface::ATOM),
        );
    }
}
