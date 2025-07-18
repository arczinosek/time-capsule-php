<?php

declare(strict_types=1);

namespace App\Milestone\Application\DTO;

use App\Milestone\Application\Exception\CorruptedFileIdException;
use Base64Url\Base64Url;

use function preg_match;
use function sprintf;

final readonly class FileId
{
    /**
     * @throws CorruptedFileIdException
     */
    public static function decode(string $fileId): self
    {
        $decoded = Base64Url::decode($fileId);
        $matches = [];

        if (!preg_match('/^(\d+)_(\d+)$/', $decoded, $matches)) {
            throw new CorruptedFileIdException(
                'Corrupted fileId, cannot be decoded',
                $fileId,
                $decoded
            );
        }

        return new self((int) $matches[1], (int) $matches[2]);
    }

    public function __construct(
        public int $attachmentId,
        public int $milestoneId,
    ) {
    }

    public function encode(): string
    {
        return Base64Url::encode(
            sprintf('%d_%d', $this->attachmentId, $this->milestoneId)
        );
    }

    public function __toString(): string
    {
        return $this->encode();
    }
}
