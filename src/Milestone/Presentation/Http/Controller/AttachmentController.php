<?php

declare(strict_types=1);

namespace App\Milestone\Presentation\Http\Controller;

use App\Milestone\Application\Command\AddAttachmentCommand;
use App\Milestone\Application\Exception\FileUploadFailedException;
use App\Milestone\Application\Exception\MilestoneNotFoundException;
use App\Milestone\Application\Handler\AddAttachmentHandler;
use App\Milestone\Domain\Exception\TooManyAttachmentsException;
use App\Milestone\Presentation\Http\Response\AttachmentResponse;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;

#[Route('/api/milestones')]
class AttachmentController extends AbstractController
{
    public function __construct(
        private readonly AddAttachmentHandler $addAttachmentHandler,
        private readonly LoggerInterface $logger,
    ) {
    }

    // TODO: Add PATCH & DELETE methods

    #[Route(
        '/{milestoneId}/attachments',
        name: 'app_milestone_add_attachment',
        methods: ['POST'],
    )]
    public function addAttachment(
        int $milestoneId,
        #[MapUploadedFile(
            new Assert\File(
                maxSize: '10M',
                mimeTypes: ['image/*', 'video/mp4', 'audio/*'],
            ),
            'file'
        )]
        UploadedFile $uploadedFile,
        Request $request,
    ): JsonResponse {
        $command = new AddAttachmentCommand(
            $milestoneId,
            $uploadedFile->getRealPath(),
            $uploadedFile->getClientOriginalName(),
            $uploadedFile->getClientMimeType(),
            $request->request->get('description'),
        );

        try {
            $attachment = $this->addAttachmentHandler->handle($command);

            return $this->json(
                AttachmentResponse::createFromEntity($attachment)
            );
        } catch (FileUploadFailedException $e) {
            $this->logger->error($e->getMessage());

            throw new BadRequestHttpException($e->getMessage());
        } catch (MilestoneNotFoundException $e) {
            throw $this->createNotFoundException($e->getMessage());
        } catch (TooManyAttachmentsException $e) {
            // TODO: which HTTP code?
            throw new BadRequestHttpException($e->getMessage());
        }
    }
}
