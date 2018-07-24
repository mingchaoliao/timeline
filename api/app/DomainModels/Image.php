<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:45 PM
 */

namespace App\DomainModels;


use Illuminate\Support\Carbon;

class Image extends AbstractBase
{
    public const TMP_PATH = 'images/tmp';
    public const PATH = 'images';

    /**
     * @var int
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
     * @var int
     */
    private $createUserId;
    /**
     * @var int|null
     */
    private $updateUserId;
    /**
     * @var Carbon
     */
    private $createdAt;
    /**
     * @var Carbon|null
     */
    private $updatedAt;

    /**
     * Image constructor.
     *
     * @param int $id
     * @param string $path
     * @param null|string $description
     * @param int $createUserId
     * @param int|null $updateUserId
     * @param Carbon $createdAt
     * @param Carbon|null $updatedAt
     */
    public function __construct(
        int $id,
        string $path,
        ?string $description,
        int $createUserId,
        ?int $updateUserId,
        Carbon $createdAt,
        ?Carbon $updatedAt
    ) {
        $this->id = $id;
        $this->path = $path;
        $this->description = $description;
        $this->createUserId = $createUserId;
        $this->updateUserId = $updateUserId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return int
     */
    public function getId(): int
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
     * @return int
     */
    public function getCreateUserId(): int
    {
        return $this->createUserId;
    }

    /**
     * @return int|null
     */
    public function getUpdateUserId(): ?int
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
     * @return Carbon|null
     */
    public function getUpdatedAt(): ?Carbon
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'path' => $this->getPath(),
            'description' => $this->getDescription(),
            'createUserId' => $this->getCreateUserId(),
            'updateUserId' => $this->getUpdateUserId(),
            'createdAt' => $this->getCreatedAt()->format(DATE_ISO8601),
            'updatedAt' => $this->getUpdatedAt()->format(DATE_ISO8601)
        ];
    }
}