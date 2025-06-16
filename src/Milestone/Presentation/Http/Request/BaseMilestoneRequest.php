<?php

declare(strict_types=1);

namespace App\Milestone\Presentation\Http\Request;

use App\Milestone\Domain\Model\Milestone;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class BaseMilestoneRequest
{
    #[NotBlank(groups: ['create'])]
    #[Length(
        min: Milestone::TITLE_LEN_MIN,
        max: Milestone::TITLE_LEN_MAX,
        groups: ['create', 'update']
    )]
    public ?string $title = null;

    #[NotBlank(groups: ['create'])]
    public ?string $description = null;

    #[NotBlank(groups: ['create'])]
    #[DateTime(groups: ['create', 'update'])]
    public ?string $startDate = null;

    #[NotBlank(groups: ['create'])]
    #[DateTime(groups: ['create', 'update'])]
    public ?string $finishDate = null;
}
