<?php

declare(strict_types=1);

namespace App\Milestone\Presentation\Http\Controller;

use App\Milestone\Application\Command\CreateMilestoneCommand;
use App\Milestone\Application\Handler\CreateMilestoneHandler;
use App\Milestone\Presentation\Http\Request\CreateMilestoneRequest;
use App\Milestone\Presentation\Http\Response\MilestoneResponse;
use DateTimeImmutable;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/milestones', name: 'api_milestone_')]
class CreateMilestoneController extends AbstractController
{
    public function __construct(
        private readonly CreateMilestoneHandler $handler,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws Exception
     */
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(validationGroups: ['create'])]
        CreateMilestoneRequest $request
    ): JsonResponse {
        $this->logger->debug('CreateMilestoneRequest', ['request' => $request]);

        $command = new CreateMilestoneCommand(
            $request->title,
            $request->description,
            new DateTimeImmutable($request->startDate),
            new DateTimeImmutable($request->finishDate)
        );

        $milestone = $this->handler->handle($command);

        return $this->json(
            MilestoneResponse::createFromEntity($milestone)
        );
    }
}
