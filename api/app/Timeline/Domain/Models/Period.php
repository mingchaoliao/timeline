<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:45 PM
 */

namespace App\Timeline\Domain\Models;


use Illuminate\Support\Carbon;

class Period extends AbstractBase
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
    private $numberOfEvents;
    /**
     * @var int
     */
    private $createUserId;
    /**
     * @var string
     */
    private $createUserName;
    /**
     * @var int|null
     */
    private $updateUserId;
    /**
     * @var string|null
     */
    private $updateUserName;
    /**
     * @var Carbon
     */
    private $createdAt;
    /**
     * @var Carbon|null
     */
    private $updatedAt;

    /**
     * Period constructor.
     * @param int $id
     * @param string $value
     * @param int $numberOfEvents
     * @param int $createUserId
     * @param string $createUserName
     * @param int|null $updateUserId
     * @param string|null $updateUserName
     * @param Carbon $createdAt
     * @param Carbon|null $updatedAt
     */
    public function __construct(int $id, string $value, int $numberOfEvents, int $createUserId, string $createUserName, ?int $updateUserId, ?string $updateUserName, Carbon $createdAt, ?Carbon $updatedAt)
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

    /**
     * @return int
     */
    public function getNumberOfEvents(): int
    {
        return $this->numberOfEvents;
    }

    /**
     * @return string
     */
    public function getCreateUserName(): string
    {
        return $this->createUserName;
    }

    /**
     * @return string|null
     */
    public function getUpdateUserName(): ?string
    {
        return $this->updateUserName;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'value' => $this->getValue(),
            'numberOfEvents' => $this->getNumberOfEvents(),
            'createUserId' => $this->getCreateUserId(),
            'createUserName' => $this->getCreateUserName(),
            'updateUserId' => $this->getUpdateUserId(),
            'updateUserName' => $this->getUpdateUserName(),
            'createdAt' => $this->getCreatedAt()->format(DATE_ISO8601),
            'updatedAt' => $this->getUpdatedAt()->format(DATE_ISO8601)
        ];
    }
}