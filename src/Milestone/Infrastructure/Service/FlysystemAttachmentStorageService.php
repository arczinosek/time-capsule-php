<?php

declare(strict_types=1);

namespace App\Milestone\Infrastructure\Service;

use App\Milestone\Application\DTO\UploadedFileInfo;
use App\Milestone\Application\Exception\FileReadException;
use App\Milestone\Application\Exception\FileUploadFailedException;
use App\Milestone\Application\Interface\AttachmentStorageService;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\UnableToRetrieveMetadata;
use League\Flysystem\UnableToWriteFile;
use Psr\Log\LoggerInterface;

use function fclose;
use function fopen;
use function is_resource;
use function sprintf;
use function uniqid;

final readonly class FlysystemAttachmentStorageService implements AttachmentStorageService
{
    public function __construct(
        private FilesystemOperator $milestoneAttachmentStorage,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @throws FileUploadFailedException
     */
    public function upload(string $sourcePath, int $milestoneId, string $originalFileName): UploadedFileInfo
    {
        $sourceFileStream = fopen($sourcePath, 'r');

        if ($sourceFileStream === false) {
            throw new FileUploadFailedException('Source file could not be opened.');
        }

        $newFileLocation = sprintf(
            '%d/%s-%s',
            $milestoneId,
            uniqid(),
            $originalFileName
        );

        try {
            $this->milestoneAttachmentStorage->writeStream($newFileLocation, $sourceFileStream);

            return new UploadedFileInfo(
                $newFileLocation,
                $this->milestoneAttachmentStorage->fileSize($newFileLocation),
                $this->milestoneAttachmentStorage->mimeType($newFileLocation),
            );
        } catch (UnableToRetrieveMetadata $e) {
            $this->logger->warning('Unable to retrieve metadata for uploaded file.', [
                'file' => $newFileLocation,
                'error' => $e->getMessage(),
            ]);

            return new UploadedFileInfo($newFileLocation);
        } catch (FilesystemException | UnableToWriteFile $e) {
            throw new FileUploadFailedException($e->getMessage(), previous: $e);
        } finally {
            if (is_resource($sourceFileStream)) {
                fclose($sourceFileStream);
            }
        }
    }

    /**
     * @throws FileReadException
     * @return resource
     */
    public function getReadStream(string $filePath)
    {
        try {
            return $this->milestoneAttachmentStorage->readStream($filePath);
        } catch (FilesystemException $e) {
            throw new FileReadException($e->getMessage(), previous: $e);
        }
    }

    public function delete(string $filePath): bool
    {
        try {
            $this->milestoneAttachmentStorage->delete($filePath);

            return true;
        } catch (FilesystemException $e) {
            $this->logger->error('Failed to delete file', [
                'file' => $filePath,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
