<?php

declare(strict_types=1);

namespace App\Milestone\Application\Query;

use function explode;

readonly class GetAttachmentStreamQuery
{
    public function __construct(
        public int $milestoneId,
        public int $attachmentId,
    ) {
    }

    public static function createFromFileId(string $fileId): self
    {
        [$milestoneId, $attachmentId] = explode('_', $fileId);

        return new self((int) $milestoneId, (int) $attachmentId);
    }
}
