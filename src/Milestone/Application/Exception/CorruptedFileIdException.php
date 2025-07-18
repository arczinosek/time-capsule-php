<?php

declare(strict_types=1);

namespace App\Milestone\Application\Exception;

use Exception;

class CorruptedFileIdException extends Exception
{
    public function __construct(
        public readonly string $encoded,
        public readonly ?string $decoded = null,
    ) {
        parent::__construct('Corrupted fileId provided');
    }
}
