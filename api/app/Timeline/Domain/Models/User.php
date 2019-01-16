<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:54 PM
 */

namespace App\Timeline\Domain\Models;


use Carbon\Carbon;

class User extends AbstractBase
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $passwordHash;
    /**
     * @var bool
     */
    private $isAdmin;
    /**
     * @var Carbon
     */
    private $createdAt;
    /**
     * @var Carbon|null
     */
    private $updatedAt;

    /**
     * User constructor.
     * @param int $id
     * @param string $name
     * @param string $email
     * @param string $passwordHash
     * @param bool $isAdmin
     * @param Carbon $createdAt
     * @param Carbon|null $updatedAt
     */
    public function __construct(int $id, string $name, string $email, string $passwordHash, bool $isAdmin, Carbon $createdAt, ?Carbon $updatedAt)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->isAdmin = $isAdmin;
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isAdmin;
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
            'name' => $this->getName(),
            'email' => $this->getEmail(),
            'isAdmin' => $this->isAdmin(),
            'createdAt' => $this->getCreatedAt()->format(DATE_ISO8601),
            'updatedAt' => $this->getUpdatedAt()->format(DATE_ISO8601)
        ];
    }
}