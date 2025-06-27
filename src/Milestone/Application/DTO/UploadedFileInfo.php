<?php

declare(strict_types=1);

namespace App\Milestone\Application\DTO;

final readonly class UploadedFileInfo
{
    public function __construct(
        public string $path,
        public ?int $size = null,
        public ?string $mimeType = null,
    ) {
    }
}
