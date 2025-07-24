<?php

declare(strict_types=1);

namespace App\Milestone\Presentation\Http\Controller;

use App\Milestone\Application\DTO\FileId;
use App\Milestone\Application\Exception\CorruptedFileIdException;
use App\Milestone\Application\Exception\FileReadException;
use App\Milestone\Application\Exception\ResourceNotFoundException;
use App\Milestone\Application\Handler\GetAttachmentStreamHandler;
use App\Milestone\Application\Query\GetAttachmentStreamQuery;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Attribute\Route;

use function sprintf;

#[Route('/files', name: 'app_attachment_')]
class AttachmentController extends AbstractController
{
    public function __construct(
        private readonly GetAttachmentStreamHandler $getAttachmentStreamHandler,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[Route('/{fileId}', name: 'stream', methods: 'GET')]
    public function getFileStream(string $fileId): Response
    {
        try {
            $query = new GetAttachmentStreamQuery(FileId::decode($fileId));
            $readStream = $this->getAttachmentStreamHandler->handle($query);

            return new StreamedResponse(
                $readStream->getIterator(),
                headers: [
                    'Content-Transfer-Encoding' => 'binary',
                    'Content-Type' => $readStream->mimeType,
                ]
            );
        } catch (CorruptedFileIdException $e) {
            return new Response($e->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (FileReadException $e) {
            $this->logger->warning('Failed to read attachment file', [
                'exception' => $e->getMessage(),
                'fileId' => $fileId,
                'file' => sprintf('%s:%d', $e->getFile(), $e->getLine()),
            ]);

            return new Response(
                'Internal Server Error',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (ResourceNotFoundException $e) {
            return new Response($e->getMessage(), Response::HTTP_NOT_FOUND);
        }
    }
}
