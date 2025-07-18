<?php

declare(strict_types=1);

namespace App\Milestone\Domain\Model;

use App\Milestone\Application\DTO\FileId;
use App\Milestone\Infrastructure\Repository\AttachmentRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

use function explode;

#[ORM\Entity(repositoryClass: AttachmentRepository::class)]
class Attachment
{
    public const FILE_PATH_MAX_LENGTH = 255;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    // @phpstan-ignore property.unusedType
    private ?int $id = null;

    #[ORM\Column(length: self::FILE_PATH_MAX_LENGTH, unique: true, updatable: false)]
    private string $filePath;

    #[ORM\Column(length: 64)]
    private string $fileMimeType;

    #[ORM\Column(type: Types::BIGINT)]
    private int $fileSizeBytes;

    #[ORM\Column(length: 255, updatable: false)]
    private string $originalFileName;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'attachments')]
    private Milestone $milestone;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE, updatable: false)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE, nullable: true)]
    private ?DateTimeInterface $updatedAt;

    public static function create(
        Milestone $milestone,
        string $filePath,
        string $fileMimeType,
        int $fileSizeBytes,
        string $originalFileName,
        ?string $description = null,
    ): self {
        $entity = new self();
        $entity->milestone = $milestone;
        $entity->filePath = $filePath;
        $entity->fileMimeType = $fileMimeType;
        $entity->fileSizeBytes = $fileSizeBytes;
        $entity->originalFileName = $originalFileName;
        $entity->description = $description;
        $entity->createdAt = new DateTimeImmutable();
        $entity->updatedAt = null;

        return $entity;
    }

    public function updateDescription(?string $description): self
    {
        $this->description = $description;
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilePath(): string
    {
        return $this->filePath;
    }

    public function getFileId(): FileId
    {
        return new FileId($this->getId(), $this->milestone->getId());
    }

    public function getFileMimeType(): string
    {
        return $this->fileMimeType;
    }

    public function isOfType(string $type): bool
    {
        [$currentType,] = explode('/', $this->getFileMimeType());

        return $currentType === $type;
    }

    public function isImage(): bool
    {
        return $this->isOfType('image');
    }

    public function getFileSizeBytes(): int
    {
        return $this->fileSizeBytes;
    }

    public function getOriginalFileName(): string
    {
        return $this->originalFileName;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getMilestone(): Milestone
    {
        return $this->milestone;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }
}
