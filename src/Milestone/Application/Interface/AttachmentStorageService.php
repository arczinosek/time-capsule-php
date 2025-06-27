<?php

declare(strict_types=1);

namespace App\Milestone\Application\Interface;

use App\Milestone\Application\DTO\UploadedFileInfo;
use App\Milestone\Application\Exception\FileUploadFailedException;

interface AttachmentStorageService
{
    /**
     * @throws FileUploadFailedException
     */
    public function upload(
        string $sourcePath,
        int $milestoneId,
        string $originalFileName
    ): UploadedFileInfo;

    public function delete(string $filePath): bool;
}
