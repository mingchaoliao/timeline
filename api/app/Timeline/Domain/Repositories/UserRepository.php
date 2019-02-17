<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 9:15 PM
 */

namespace App\Timeline\Domain\Repositories;


use App\Timeline\Domain\Collections\UserCollection;
use App\Timeline\Domain\Models\User;
use App\Timeline\Domain\Models\UserToken;
use App\Timeline\Domain\ValueObjects\Email;
use App\Timeline\Domain\ValueObjects\UserId;

interface UserRepository
{
    public function getCurrentUser(): ?User;

    public function getByEmail(Email $email): ?User;

    public function login(Email $email, string $password): UserToken;

    public function validatePassword(UserId $id, string $password): bool;

    public function getAll(): UserCollection;

    public function create(
        string $name,
        Email $email,
        string $password,
        bool $isAdmin = false,
        bool $isEditor = false
    ): User;

    public function update(
        UserId $id,
        ?string $name = null,
        ?string $password = null,
        ?bool $isAdmin = null,
        ?bool $isEditor = null,
        ?bool $isActive = null
    ): User;
}