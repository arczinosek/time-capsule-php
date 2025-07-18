<?php

declare(strict_types=1);

namespace App\Milestone\Presentation\Http\Response;

use App\Milestone\Domain\Model\Attachment;
use DateTimeInterface;

final readonly class AttachmentResponse
{
    public function __construct(
        public int $id,
        public string $url,
        public string $fileName,
        public string $fileId,
        public ?string $description,
        public string $mimeType,
        public int $sizeBytes,
        public string $createdAt,
        public ?string $updatedAt,
    ) {
    }

    public static function createFromEntity(Attachment $attachment): self
    {
        return new self(
            $attachment->getId(),
            $attachment->getFilePath(),
            $attachment->getOriginalFileName(),
            $attachment->getFileId()->encode(),
            $attachment->getDescription(),
            $attachment->getFileMimeType(),
            $attachment->getFileSizeBytes(),
            $attachment->getCreatedAt()->format(DateTimeInterface::ATOM),
            $attachment->getUpdatedAt()?->format(DateTimeInterface::ATOM),
        );
    }
}
