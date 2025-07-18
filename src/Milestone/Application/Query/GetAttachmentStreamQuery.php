<?php

declare(strict_types=1);

namespace App\Milestone\Application\Query;

use App\Milestone\Application\DTO\FileId;

readonly class GetAttachmentStreamQuery
{
    public function __construct(
        public FileId $fileId,
    ) {
    }
}
