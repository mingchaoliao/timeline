<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:45 PM
 */

namespace App\Timeline\Domain\Models;


use App\Timeline\Domain\ValueObjects\EventId;
use App\Timeline\Domain\ValueObjects\ImageId;
use App\Timeline\Domain\ValueObjects\UserId;
use Carbon\Carbon;

class Image extends BaseModel
{
    public const TMP_PATH = 'images';
    public const PATH = 'public/images';

    /**
     * @var ImageId
     */
    private $id;
    /**
     * @var string
     */
    private $path;
    /**
     * @var string|null
     */
    private $description;
    /**
     * @var string
     */
    private $originalName;
    /**
     * @var EventId|null
     */
    private $eventId;
    /**
     * @var UserId
     */
    private $createUserId;
    /**
     * @var UserId
     */
    private $updateUserId;
    /**
     * @var Carbon
     */
    private $createdAt;
    /**
     * @var Carbon
     */
    private $updatedAt;

    /**
     * Image constructor.
     * @param ImageId $id
     * @param string $path
     * @param string|null $description
     * @param string $originalName
     * @param EventId|null $eventId
     * @param UserId $createUserId
     * @param UserId $updateUserId
     * @param Carbon $createdAt
     * @param Carbon $updatedAt
     */
    public function __construct(ImageId $id, string $path, ?string $description, string $originalName, ?EventId $eventId, UserId $createUserId, UserId $updateUserId, Carbon $createdAt, Carbon $updatedAt)
    {
        $this->id = $id;
        $this->path = $path;
        $this->description = $description;
        $this->originalName = $originalName;
        $this->eventId = $eventId;
        $this->createUserId = $createUserId;
        $this->updateUserId = $updateUserId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return ImageId
     */
    public function getId(): ImageId
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    /**
     * @return EventId|null
     */
    public function getEventId(): ?EventId
    {
        return $this->eventId;
    }

    /**
     * @return UserId
     */
    public function getCreateUserId(): UserId
    {
        return $this->createUserId;
    }

    /**
     * @return UserId
     */
    public function getUpdateUserId(): UserId
    {
        return $this->updateUserId;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    /**
     * @return Carbon
     */
    public function getUpdatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId()->getValue(),
            'path' => $this->getPath(),
            'description' => $this->getDescription(),
            'originalName' => $this->getOriginalName(),
            'eventId' => $this->getEventId() === null ? null : $this->getEventId()->getValue(),
            'createUserId' => $this->getCreateUserId()->getValue(),
            'updateUserId' => $this->getUpdateUserId()->getValue(),
            'createdAt' => $this->getCreatedAt()->toIso8601String(),
            'updatedAt' => $this->getUpdatedAt()->toIso8601String()
        ];
    }
}