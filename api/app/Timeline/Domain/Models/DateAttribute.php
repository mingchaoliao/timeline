<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:45 PM
 */

namespace App\Timeline\Domain\Models;


use App\Timeline\Domain\ValueObjects\DateAttributeId;
use App\Timeline\Domain\ValueObjects\UserId;
use Illuminate\Support\Carbon;

class DateAttribute extends BaseModel
{
    /**
     * @var DateAttributeId
     */
    private $id;
    /**
     * @var string
     */
    private $value;
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
     * DateAttribute constructor.
     * @param DateAttributeId $id
     * @param string $value
     * @param UserId $createUserId
     * @param string $createUserName
     * @param UserId $updateUserId
     * @param string $updateUserName
     * @param Carbon $createdAt
     * @param Carbon $updatedAt
     */
    public function __construct(DateAttributeId $id, string $value, UserId $createUserId, string $createUserName, UserId $updateUserId, string $updateUserName, Carbon $createdAt, Carbon $updatedAt)
    {
        $this->id = $id;
        $this->value = $value;
        $this->createUserId = $createUserId;
        $this->createUserName = $createUserName;
        $this->updateUserId = $updateUserId;
        $this->updateUserName = $updateUserName;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return DateAttributeId
     */
    public function getId(): DateAttributeId
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
            'createUserId' => $this->getCreateUserId()->getValue(),
            'createUserName' => $this->getCreateUserName(),
            'updateUserId' => $this->getUpdateUserId()->getValue(),
            'updateUserName' => $this->getUpdateUserName(),
            'createdAt' => $this->getCreatedAt()->toIso8601String(),
            'updatedAt' => $this->getUpdatedAt()->toIso8601String()
        ];
    }
}