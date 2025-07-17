<?php

declare(strict_types=1);

namespace App\Milestone\Application\Interface;

use App\Milestone\Application\DTO\UploadedFileInfo;
use App\Milestone\Application\Exception\FileReadException;
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

    /**
     * @throws FileReadException
     * @return resource
     */
    public function getReadStream(string $filePath);

    public function delete(string $filePath): bool;
}
