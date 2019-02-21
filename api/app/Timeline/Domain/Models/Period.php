<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:45 PM
 */

namespace App\Timeline\Domain\Models;


use App\Timeline\Domain\ValueObjects\PeriodId;
use App\Timeline\Domain\ValueObjects\UserId;
use Carbon\Carbon;

class Period extends BaseModel
{
    /**
     * @var PeriodId
     */
    private $id;
    /**
     * @var string
     */
    private $value;
    /**
     * @var Carbon|null
     */
    private $startDate;
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
     * Period constructor.
     * @param PeriodId $id
     * @param string $value
     * @param Carbon|null $startDate
     * @param int $numberOfEvents
     * @param UserId $createUserId
     * @param string $createUserName
     * @param UserId $updateUserId
     * @param string $updateUserName
     * @param Carbon $createdAt
     * @param Carbon $updatedAt
     */
    public function __construct(PeriodId $id, string $value, ?Carbon $startDate, int $numberOfEvents, UserId $createUserId, string $createUserName, UserId $updateUserId, string $updateUserName, Carbon $createdAt, Carbon $updatedAt)
    {
        $this->id = $id;
        $this->value = $value;
        $this->startDate = $startDate;
        $this->numberOfEvents = $numberOfEvents;
        $this->createUserId = $createUserId;
        $this->createUserName = $createUserName;
        $this->updateUserId = $updateUserId;
        $this->updateUserName = $updateUserName;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return PeriodId
     */
    public function getId(): PeriodId
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
     * @return Carbon|null
     */
    public function getStartDate(): ?Carbon
    {
        return $this->startDate;
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

    public function toValueArray(): array
    {
        return [
            'id' => $this->getId()->getValue(),
            'value' => $this->getValue(),
            'startDate' => $this->getStartDate() === null ? null : $this->getStartDate()->toIso8601String(),
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