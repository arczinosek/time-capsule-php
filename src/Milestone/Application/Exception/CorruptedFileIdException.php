<?php

declare(strict_types=1);

namespace App\Milestone\Application\Exception;

use Exception;

class CorruptedFileIdException extends Exception
{
    public function __construct(
        string $message,
        public readonly string $encoded,
        public readonly string $decoded,
    ) {
        parent::__construct($message);
    }
}
