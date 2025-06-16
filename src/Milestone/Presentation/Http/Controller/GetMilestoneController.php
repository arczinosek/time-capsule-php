<?php

declare(strict_types=1);

namespace App\Milestone\Presentation\Http\Controller;

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

#[Route('/api/milestones')]
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
    #[Route('', name: 'api_list_milestones', methods: ['GET'])]
    public function list(
        #[MapQueryString] ListMilestonesRequest $request
    ): JsonResponse {
        $milestones = $this->listMilestonesHandler->handle(
            new ListMilestonesQuery($request->page, $request->limit)
        );

        return $this->json(
            MilestonesListResponse::fromMilestones($milestones)
        );
    }

    #[Route('/{id<\d+>}', name: 'api_get_milestone', methods: ['GET'])]
    public function get(
        int $id,
    ): JsonResponse {
        $milestone = $this->getMilestoneHandler->handle(
            new GetMilestoneQuery($id)
        );

        if ($milestone === null) {
            throw $this->createNotFoundException("Milestone '$id' not found.");
        }

        return $this->json(
            MilestoneResponse::fromMilestone($milestone)
        );
    }
}
