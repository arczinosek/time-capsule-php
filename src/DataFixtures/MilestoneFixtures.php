<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Milestone\Domain\Model\Milestone;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;

class MilestoneFixtures extends Fixture
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $milestone = Milestone::create(
            'First event!',
            "There was many people.",
            new DateTimeImmutable('2025-06-16 23:00:00'),
            new DateTimeImmutable('2025-06-17 03:00:00'),
        );

        $manager->persist($milestone);
        $manager->flush();
    }
}
