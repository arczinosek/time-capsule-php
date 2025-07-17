<?php

declare(strict_types=1);

namespace App\Milestone\Presentation\Http\Controller\Api;

use App\Milestone\Application\Command\UpdateMilestoneCommand;
use App\Milestone\Application\Exception\MilestoneNotFoundException;
use App\Milestone\Application\Handler\UpdateMilestoneHandler;
use App\Milestone\Presentation\Http\Request\UpdateMilestoneRequest;
use App\Milestone\Presentation\Http\Response\MilestoneResponse;
use DateTimeImmutable;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/milestones', name: 'api_milestone_')]
class UpdateMilestoneController extends AbstractController
{
    public function __construct(
        private readonly UpdateMilestoneHandler $handler,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws Exception
     */
    #[Route('/{milestoneId}', name: 'update', methods: ['PATCH'])]
    public function update(
        int $milestoneId,
        #[MapRequestPayload(validationGroups: ['update'])]
        UpdateMilestoneRequest $updateMilestoneRequest,
        Request $request,
    ): JsonResponse {
        $this->logger->debug('UpdateMilestoneRequest', [
            'id' => $milestoneId,
            'request' => $updateMilestoneRequest,
            'data' => $request->getContent(),
            'contentType' => $request->headers->get('Content-Type'),
        ]);

        $dateOrNull = static fn (?string $value): DateTimeImmutable | null =>
            $value ? new DateTimeImmutable($value) : null;

        $command = new UpdateMilestoneCommand(
            $milestoneId,
            $updateMilestoneRequest->title,
            $updateMilestoneRequest->description,
            $dateOrNull($updateMilestoneRequest->startDate),
            $dateOrNull($updateMilestoneRequest->finishDate),
        );

        try {
            $milestone = $this->handler->handle($command);

            return $this->json(
                MilestoneResponse::createFromEntity($milestone)
            );
        } catch (MilestoneNotFoundException $e) {
            throw $this->createNotFoundException($e->getMessage());
        }
    }
}
