<?php

declare(strict_types=1);

namespace App\Milestone\Domain\Exception;

use Exception;

use function sprintf;

class TooManyAttachmentsException extends Exception
{
    public readonly string $uploadedFilePath;

    public function __construct(
        string $message,
        string $uploadedFilePath,
        ?Exception $previous = null
    ) {
        parent::__construct($message, previous: $previous);

        $this->uploadedFilePath = $uploadedFilePath;
    }

    public static function create(string $uploadedFilePath, int $limit): self
    {
        return new self(
            sprintf(
                'Limit of %d attachments for milestone reached',
                $limit,
            ),
            $uploadedFilePath
        );
    }
}
