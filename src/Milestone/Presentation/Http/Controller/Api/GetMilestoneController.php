<?php

declare(strict_types=1);

namespace App\Milestone\Presentation\Http\Controller\Api;

use App\Milestone\Application\Handler\GetMilestoneHandler;
use App\Milestone\Application\Handler\ListMilestonesHandler;
use App\Milestone\Application\Query\GetMilestoneQuery;
use App\Milestone\Application\Query\ListMilestonesQuery;
use App\Milestone\Presentation\Http\Request\ListMilestonesRequest;
use App\Milestone\Presentation\Http\Response\MilestoneResponse;
use App\Milestone\Presentation\Http\Response\MilestonesListResponse;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/milestones', name: 'api_milestone_')]
class GetMilestoneController extends AbstractController
{
    public function __construct(
        private readonly ListMilestonesHandler $listMilestonesHandler,
        private readonly GetMilestoneHandler $getMilestoneHandler,
    ) {
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'list', methods: ['GET'])]
    public function list(
        #[MapQueryString] ListMilestonesRequest $request
    ): JsonResponse {
        $milestones = $this->listMilestonesHandler->handle(
            new ListMilestonesQuery($request->page, $request->limit)
        );

        return $this->json(
            MilestonesListResponse::createFromArray($milestones)
        );
    }

    #[Route('/{milestoneId}', name: 'get', methods: ['GET'])]
    public function get(
        int $milestoneId,
    ): JsonResponse {
        $milestone = $this->getMilestoneHandler->handle(
            new GetMilestoneQuery($milestoneId)
        );

        if ($milestone === null) {
            throw $this->createNotFoundException("Milestone '$milestoneId' not found.");
        }

        return $this->json(
            MilestoneResponse::createFromEntity($milestone)
        );
    }
}
