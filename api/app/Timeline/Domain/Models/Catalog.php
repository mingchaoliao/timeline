<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:45 PM
 */

namespace App\Timeline\Domain\Models;


use App\Timeline\Domain\ValueObjects\CatalogId;
use App\Timeline\Domain\ValueObjects\UserId;
use Carbon\Carbon;

class Catalog extends BaseModel
{
    /**
     * @var CatalogId
     */
    private $id;
    /**
     * @var string
     */
    private $value;
    /**
     * @var int
     */
    private $numberOfEvents;
    /**
     * @var UserId
     */
    private $createUserId;
    /**
     * @var string
     */
    private $createUserName;
    /**
     * @var UserId
     */
    private $updateUserId;
    /**
     * @var string
     */
    private $updateUserName;
    /**
     * @var Carbon
     */
    private $createdAt;
    /**
     * @var Carbon
     */
    private $updatedAt;

    /**
     * Catalog constructor.
     * @param CatalogId $id
     * @param string $value
     * @param int $numberOfEvents
     * @param UserId $createUserId
     * @param string $createUserName
     * @param UserId $updateUserId
     * @param string $updateUserName
     * @param Carbon $createdAt
     * @param Carbon $updatedAt
     */
    public function __construct(CatalogId $id, string $value, int $numberOfEvents, UserId $createUserId, string $createUserName, UserId $updateUserId, string $updateUserName, Carbon $createdAt, Carbon $updatedAt)
    {
        $this->id = $id;
        $this->value = $value;
        $this->numberOfEvents = $numberOfEvents;
        $this->createUserId = $createUserId;
        $this->createUserName = $createUserName;
        $this->updateUserId = $updateUserId;
        $this->updateUserName = $updateUserName;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return CatalogId
     */
    public function getId(): CatalogId
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
    public function getNumberOfEvents(): int
    {
        return $this->numberOfEvents;
    }

    /**
     * @return UserId
     */
    public function getCreateUserId(): UserId
    {
        return $this->createUserId;
    }

    /**
     * @return string
     */
    public function getCreateUserName(): string
    {
        return $this->createUserName;
    }

    /**
     * @return UserId
     */
    public function getUpdateUserId(): UserId
    {
        return $this->updateUserId;
    }

    /**
     * @return string
     */
    public function getUpdateUserName(): string
    {
        return $this->updateUserName;
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
            'value' => $this->getValue(),
            'numberOfEvents' => $this->getNumberOfEvents(),
            'createUserId' => $this->getCreateUserId()->getValue(),
            'createUserName' => $this->getCreateUserName(),
            'updateUserId' => $this->getUpdateUserId()->getValue(),
            'updateUserName' => $this->getUpdateUserName(),
            'createdAt' => $this->getCreatedAt()->toIso8601String(),
            'updatedAt' => $this->getUpdatedAt()->toIso8601String()
        ];
    }
}