<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 6/21/18
 * Time: 9:24 PM
 */

namespace App\Timeline\Infrastructure\Persistence\Eloquent\Repositories;

use App\Timeline\Domain\Collections\UserCollection;
use App\Timeline\Domain\Models\User;
use App\Timeline\Domain\Models\UserToken;
use App\Timeline\Domain\Repositories\UserRepository;
use App\Timeline\Domain\ValueObjects\Email;
use App\Timeline\Domain\ValueObjects\UserId;
use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentUser;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Tymon\JWTAuth\JWTGuard;

class EloquentUserRepository implements UserRepository
{
    /**
     * @var EloquentUser
     */
    private $userModel;
    /**
     * @var Hasher
     */
    private $hasher;
    /**
     * @var JWTGuard
     */
    private $guard;

    /**
     * EloquentUserRepository constructor.
     * @param EloquentUser $userModel
     * @param Hasher $hasher
     * @param JWTGuard $guard
     */
    public function __construct(EloquentUser $userModel, Hasher $hasher, JWTGuard $guard)
    {
        $this->userModel = $userModel;
        $this->hasher = $hasher;
        $this->guard = $guard;
    }

    /**
     * @return User|null
     */
    public function getCurrentUser(): ?User
    {
        if (!$this->guard->check()) {
            return null;
        }

        /** @var EloquentUser $eloquentUser */
        $eloquentUser = $this->guard->user();

        return $this->constructUser($eloquentUser);
    }

    /**
     * @param Email $email
     * @param string $password
     * @return UserToken
     * @throws TimelineException
     */
    public function login(Email $email, string $password): UserToken
    {
        /** @var EloquentUser|null $user */
        $user = $this->userModel->where('email', $email->getValue())->first();

        if($user === null) {
            throw TimelineException::ofUserWithEmailDoesNotFound($email);
        }

        if(!$user->isActive()) {
            throw TimelineException::ofUserAccountIsLocked();
        }

        $token = $this->guard->attempt([
            'email' => $email->getValue(),
            'password' => $password
        ]);

        if ($token === false) {
            throw TimelineException::ofInvalidCredentials();
        }

        return new UserToken(
            'Bearer',
            $token
        );
    }

    public function validatePassword(UserId $id, string $password): bool
    {
        /** @var EloquentUser $eloquentUser */
        $eloquentUser = $this->userModel->find($id->getValue());
        if ($eloquentUser === null) {
            return false;
        }
        return $this->hasher->check($password, $eloquentUser->getPasswordHash());
    }

    /**
     * @return UserCollection
     */
    public function getAll(): UserCollection
    {
        return $this->constructUserCollection($this->userModel->orderBy('id')->get());
    }

    /**
     * @param string $name
     * @param Email $email
     * @param string $password
     * @param bool $isAdmin
     * @param bool $isEditor
     * @return User
     * @throws TimelineException
     */
    public function create(
        string $name,
        Email $email,
        string $password,
        bool $isAdmin = false,
        bool $isEditor = false
    ): User
    {
        try {
            $passwordHash = $this->hasher->make($password);

            $eloquentUser = $this->userModel
                ->create([
                    'name' => $name,
                    'email' => $email,
                    'password' => $passwordHash,
                    'is_admin' => $isAdmin ? 1 : 0,
                    'is_editor' => $isEditor ? 1 : 0
                ]);

            return $this->constructUser($eloquentUser);
        } catch (QueryException $e) {
            $errorInfo = $e->errorInfo;

            if ($errorInfo[1] === 1062) { // duplicated email
                throw TimelineException::ofDuplicatedUserEmail($email, $e);
            }

            throw $e;
        }
    }

    /**
     * @param UserId $id
     * @param null|string $name
     * @param null|string $password
     * @param bool|null $isAdmin
     * @param bool|null $isEditor
     * @param bool|null $isActive
     * @return User
     * @throws TimelineException
     */
    public function update(
        UserId $id,
        ?string $name = null,
        ?string $password = null,
        ?bool $isAdmin = null,
        ?bool $isEditor = null,
        ?bool $isActive = null
    ): User
    {
        $eloquentUser = $this->userModel->find($id->getValue());

        if ($eloquentUser === null) {
            throw TimelineException::ofUserWithIdDoesNotExist($id);
        }

        $update = [];

        if ($name !== null) {
            $update['name'] = $name;
        }

        if ($password !== null) {
            $passwordHash = $this->hasher->make($password);
            $update['password'] = $passwordHash;
        }

        if ($isAdmin !== null) {
            $update['is_admin'] = $isAdmin ? 1 : 0;
        }

        if ($isEditor !== null) {
            $update['is_editor'] = $isEditor ? 1 : 0;
        }

        if ($isActive !== null) {
            $update['is_active'] = $isActive ? 1 : 0;
        }

        if (count($update) !== 0) {
            $eloquentUser->update($update);
        }

        return $this->constructUser($this->userModel->find($id->getValue()));
    }

    /**
     * @param EloquentUser $eloquentUser
     * @return User
     */
    private function constructUser(EloquentUser $eloquentUser): User
    {
        return new User(
            new UserId($eloquentUser->getId()),
            $eloquentUser->getName(),
            new Email($eloquentUser->getEmail()),
            $eloquentUser->isAdmin(),
            $eloquentUser->isEditor(),
            $eloquentUser->isActive(),
            $eloquentUser->getCreatedAt(),
            $eloquentUser->getUpdatedAt()
        );
    }

    /**
     * @param Collection $eloquentUsers
     * @return UserCollection
     */
    private function constructUserCollection(Collection $eloquentUsers): UserCollection
    {
        return new UserCollection(
            $eloquentUsers->map(function (EloquentUser $eloquentUser) {
                return $this->constructUser($eloquentUser);
            })->toArray()
        );
    }
}