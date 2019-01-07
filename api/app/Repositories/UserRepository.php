<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 6/21/18
 * Time: 9:24 PM
 */

namespace App\Repositories;

use App\DomainModels\Collections\UserCollection;
use App\DomainModels\User;
use App\EloquentModels\EloquentUser;
use App\Exceptions\UserNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class UserRepository extends BaseRepository
{

    public function getCurrentUser()
    {
        return $this->constructUser(Auth::user());
    }

    public function getAll(): UserCollection
    {
        return $this->constructUserCollection(EloquentUser::all());
    }

    public function constructUser(EloquentUser $eloquentUser): User
    {
        return new User(
            $eloquentUser->getId(),
            $eloquentUser->getName(),
            $eloquentUser->getEmail(),
            $eloquentUser->getPasswordHash(),
            $eloquentUser->isAdmin(),
            $eloquentUser->getCreatedAt(),
            $eloquentUser->getUpdatedAt()
        );
    }

    public function constructUserCollection(Collection $eloquentUsers): UserCollection
    {
        return new UserCollection($eloquentUsers->map(function (EloquentUser $eloquentUser) {
            return $this->constructUser($eloquentUser);
        })->toArray());
    }

    public function grantOrRevokeAdminPrivilege(int $id, bool $isAdmin): bool {
        $user = EloquentUser::where('id', '=', $id)->first();

        if($user === null) {
            throw new \InvalidArgumentException(sprintf(
                'User with ID "%d" not found',
                $id
            ));
        }

        return $user->update([
            'is_admin' => $isAdmin
        ]);
    }

    public function createUser(string $name, string $email, string $password, bool $isAdmin = false): User
    {
        if (EloquentUser::where('email', '=', $email)->count() !== 0) {
            throw new \InvalidArgumentException('Email address ' . $email . ' has already existed');
        }

        $eloquentUser = EloquentUser::createNew(
            $name,
            $email,
            $password,
            false,
            $isAdmin
        );

        return $this->constructUser($eloquentUser);
    }

    public function getByEmail(string $email): User
    {
        $eloquentUser = EloquentUser::where('email', $email)->first();

        if ($eloquentUser === null) {
            throw new UserNotFoundException();
        }

        return $this->constructUser($eloquentUser);
    }
}