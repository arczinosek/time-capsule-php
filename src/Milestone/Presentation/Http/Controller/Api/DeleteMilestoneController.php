<?php

declare(strict_types=1);

namespace App\Milestone\Presentation\Http\Controller\Api;

use App\Milestone\Application\Command\DeleteMilestoneCommand;
use App\Milestone\Application\Handler\DeleteMilestoneHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/milestones', name: 'api_milestone_')]
class DeleteMilestoneController extends AbstractController
{
    public function __construct(
        private readonly DeleteMilestoneHandler $handler,
    ) {
    }

    #[Route('/{milestoneId}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        int $milestoneId,
    ): Response {
        $this->handler->handle(new DeleteMilestoneCommand($milestoneId));

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
