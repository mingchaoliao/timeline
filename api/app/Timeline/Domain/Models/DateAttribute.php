<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:51 PM
 */

namespace App\Timeline\Domain\Models;


use Carbon\Carbon;

class DateAttribute extends AbstractBase
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
     * DateAttribute constructor.
     * @param int $id
     * @param string $value
     * @param int $createUserId
     * @param string $createUserName
     * @param int|null $updateUserId
     * @param null|string $updateUserName
     * @param Carbon $createdAt
     * @param Carbon|null $updatedAt
     */
    public function __construct(int $id, string $value, int $createUserId, string $createUserName, ?int $updateUserId, ?string $updateUserName, Carbon $createdAt, ?Carbon $updatedAt)
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
     * @return string
     */
    public function getCreateUserName(): string
    {
        return $this->createUserName;
    }

    /**
     * @return int|null
     */
    public function getUpdateUserId(): ?int
    {
        return $this->updateUserId;
    }

    /**
     * @return null|string
     */
    public function getUpdateUserName(): ?string
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
            'createUserName' => $this->getCreateUserName(),
            'updateUserId' => $this->getUpdateUserId(),
            'updateUserName' => $this->getUpdateUserName(),
            'createdAt' => $this->getCreatedAt()->format(DATE_ISO8601),
            'updatedAt' => $this->getUpdatedAt()->format(DATE_ISO8601)
        ];
    }
}