<?php

declare(strict_types=1);

namespace App\Milestone\Domain\Model;

use App\Milestone\Domain\Exception\TooManyAttachmentsException;
use App\Milestone\Infrastructure\Repository\MilestoneRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Exception;

use function strlen;

#[Entity(MilestoneRepository::class)]
final class Milestone
{
    public const TITLE_LEN_MIN = 3;
    public const TITLE_LEN_MAX = 127;

    private const ATTACHMENT_LIMIT = 20;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['unsigned' => true])]

    // @phpstan-ignore property.onlyRead
    private int $id;

    #[ORM\Column(length: self::TITLE_LEN_MAX)]
    private string $title;

    #[ORM\Column(type: Types::TEXT)]
    private string $description;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    private DateTimeInterface $startDate;

    #[ORM\Column(type: Types::DATETIMETZ_MUTABLE)]
    private DateTimeInterface $finishDate;

    /**
     * @var Collection<int, Attachment>
     */
    #[ORM\OneToMany(
        targetEntity: Attachment::class,
        mappedBy: 'milestone',
        cascade: ['persist', 'remove'],
        orphanRemoval: true,
    )]
    private Collection $attachments;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->attachments = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = null;
    }

    /**
     * @throws Exception
     */
    public static function create(
        string $title,
        string $description,
        DateTimeImmutable $startDate,
        DateTimeImmutable $finishDate,
    ): Milestone {
        $milestone = new self();

        $milestone
            ->setTitle($title)
            ->setDescription($description)
            ->setPeriod($startDate, $finishDate)
            ->updatedAt = null
        ;

        return $milestone;
    }

    public function touch(): self
    {
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    /**
     * @throws Exception
     */
    public function setTitle(string $newTitle): self
    {
        if (strlen($newTitle) < self::TITLE_LEN_MIN) {
            throw new Exception('Title is too short!');
        }

        if (strlen($newTitle) > self::TITLE_LEN_MAX) {
            throw new Exception('Title is too long!');
        }

        $this->title = $newTitle;

        return $this->touch();
    }

    public function setDescription(string $newDescription): self
    {
        $this->description = $newDescription;

        return $this->touch();
    }

    /**
     * @throws Exception
     */
    public function setPeriod(DateTimeInterface $startDate, DateTimeInterface $finishDate): self
    {
        if ($startDate > $finishDate) {
            throw new Exception('Start date cannot be after finish date!');
        }

        $this->startDate = $startDate;
        $this->finishDate = $finishDate;

        return $this->touch();
    }

    /**
     * @throws TooManyAttachmentsException
     */
    public function addAttachment(
        string $filePath,
        string $fileMimeType,
        int $fileSizeBytes,
        string $originalFileName,
        ?string $description = null,
    ): Attachment {
        if ($this->attachments->count() >= self::ATTACHMENT_LIMIT) {
            throw TooManyAttachmentsException::create($filePath, self::ATTACHMENT_LIMIT);
        }

        $attachment = Attachment::create(
            $this,
            $filePath,
            $fileMimeType,
            $fileSizeBytes,
            $originalFileName,
            $description
        );

        $this->attachments->add($attachment);
        $this->touch();

        return $attachment;
    }

    public function removeAttachment(Attachment $attachment): self
    {
        if ($this->attachments->removeElement($attachment)) {
            $this->touch();
        }

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStartDate(): DateTimeInterface
    {
        return $this->startDate;
    }

    public function getFinishDate(): DateTimeInterface
    {
        return $this->finishDate;
    }

    /**
     * @return Collection<int, Attachment>
     */
    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function getAttachmentById(int $attachmentId): ?Attachment
    {
        return $this->attachments->findFirst(
            fn (int $id, Attachment $attachment): bool => $attachment->getId() === $attachmentId
        );
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }
}
