<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:45 PM
 */

namespace App\DomainModels;


use Illuminate\Support\Carbon;

class Catalog extends AbstractBase
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $value;
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
     * Catalog constructor.
     *
     * @param int $id
     * @param string $value
     * @param int $createUserId
     * @param int|null $updateUserId
     * @param Carbon $createdAt
     * @param Carbon|null $updatedAt
     */
    public function __construct(
        int $id,
        string $value,
        int $createUserId,
        ?int $updateUserId,
        Carbon $createdAt,
        ?Carbon $updatedAt
    ) {
        $this->id = $id;
        $this->value = $value;
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
    public function getValue(): string
    {
        return $this->value;
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
            'value' => $this->getValue(),
            'createUserId' => $this->getCreateUserId(),
            'updateUserId' => $this->getUpdateUserId(),
            'createdAt' => $this->getCreatedAt()->format(DATE_ISO8601),
            'updatedAt' => $this->getUpdatedAt()->format(DATE_ISO8601),
        ];
    }
}