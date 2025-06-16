<?php

declare(strict_types=1);

namespace App\Milestone\Application\Exception;

use Exception;

class MilestoneNotFoundException extends Exception
{
    public readonly int $milestoneId;

    public function __construct(string $message, int $milestoneId)
    {
        parent::__construct($message);

        $this->milestoneId = $milestoneId;
    }

    public static function forId(int $id): self
    {
        return new self("Milestone '$id' not found", $id);
    }
}
