<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:54 PM
 */

namespace App\Timeline\Domain\Models;


use App\Timeline\Domain\ValueObjects\Email;
use App\Timeline\Domain\ValueObjects\UserId;
use Carbon\Carbon;

class User extends BaseModel
{
    /**
     * @var UserId
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var Email
     */
    private $email;
    /**
     * @var bool
     */
    private $isAdmin;
    /**
     * @var bool
     */
    private $isEditor;
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
     * @param UserId $id
     * @param string $name
     * @param Email $email
     * @param bool $isAdmin
     * @param bool $isEditor
     * @param Carbon $createdAt
     * @param Carbon|null $updatedAt
     */
    public function __construct(UserId $id, string $name, Email $email, bool $isAdmin, bool $isEditor, Carbon $createdAt, ?Carbon $updatedAt)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->isAdmin = $isAdmin;
        $this->isEditor = $isEditor;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return UserId
     */
    public function getId(): UserId
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
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * @return bool
     */
    public function isEditor(): bool
    {
        return $this->isEditor;
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
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId()->getValue(),
            'name' => $this->getName(),
            'email' => $this->getEmail()->getValue(),
            'isAdmin' => $this->isAdmin(),
            'isEditor' => $this->isEditor(),
            'createdAt' => $this->getCreatedAt()->toIso8601String(),
            'updatedAt' => $this->getUpdatedAt()->toIso8601String()
        ];
    }
}