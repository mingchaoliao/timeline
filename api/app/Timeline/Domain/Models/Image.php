<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:45 PM
 */

namespace App\Timeline\Domain\Models;


use App\Timeline\Domain\ValueObjects\ImageId;
use App\Timeline\Domain\ValueObjects\UserId;
use Illuminate\Support\Carbon;

class Image extends BaseModel
{
    public const TMP_PATH = 'images/tmp';
    public const PATH = 'images';

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
     * @param null|string $description
     * @param UserId $createUserId
     * @param UserId $updateUserId
     * @param Carbon $createdAt
     * @param Carbon $updatedAt
     */
    public function __construct(ImageId $id, string $path, ?string $description, UserId $createUserId, UserId $updateUserId, Carbon $createdAt, Carbon $updatedAt)
    {
        $this->id = $id;
        $this->path = $path;
        $this->description = $description;
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
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
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
            'createUserId' => $this->getCreateUserId()->getValue(),
            'updateUserId' => $this->getUpdateUserId()->getValue(),
            'createdAt' => $this->getCreatedAt()->format(DATE_ISO8601),
            'updatedAt' => $this->getUpdatedAt()->format(DATE_ISO8601)
        ];
    }
}