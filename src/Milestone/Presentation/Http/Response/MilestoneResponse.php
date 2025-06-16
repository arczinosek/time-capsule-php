<?php

declare(strict_types=1);

namespace App\Milestone\Presentation\Http\Response;

use App\Milestone\Domain\Model\Milestone;
use DateTimeInterface;

readonly class MilestoneResponse
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public string $startDate,
        public string $finishDate,
        public string $createdAt,
        public ?string $updatedAt,
    ) {
    }

    public static function fromMilestone(Milestone $milestone): self
    {
        return new self(
            $milestone->getId(),
            $milestone->getTitle(),
            $milestone->getDescription(),
            $milestone->getStartDate()->format(DateTimeInterface::ATOM),
            $milestone->getFinishDate()->format(DateTimeInterface::ATOM),
            $milestone->getCreatedAt()->format(DateTimeInterface::ATOM),
            $milestone->getUpdatedAt()?->format(DateTimeInterface::ATOM),
        );
    }
}
