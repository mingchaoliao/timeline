<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/16/19
 * Time: 6:27 PM
 */

namespace App\Timeline\Domain\Services;


use App\Timeline\Domain\Collections\UserCollection;
use App\Timeline\Domain\Models\User;
use App\Timeline\Domain\Models\UserToken;
use App\Timeline\Domain\Repositories\UserRepository;
use App\Timeline\Domain\ValueObjects\Email;
use App\Timeline\Domain\ValueObjects\UserId;
use App\Timeline\Exceptions\TimelineException;

class UserService
{
    /**
     * @var User
     */
    private static $currentUser = null;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $name
     * @param Email $email
     * @param string $password
     * @return UserToken
     * @throws TimelineException
     */
    public function register(string $name, Email $email, string $password): UserToken
    {
        try {
            $this->userRepository->create(
                $name,
                $email,
                $password,
                false,
                false
            );

            return $this->userRepository->login($email, $password);
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToRegister($e);
        }
    }

    /**
     * @param Email $email
     * @param string $password
     * @return UserToken
     * @throws TimelineException
     */
    public function login(Email $email, string $password): UserToken
    {
        try {
            return $this->userRepository->login($email, $password);
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToLogin($e);
        }
    }

    /**
     * @return User|null
     * @throws TimelineException
     */
    public function getCurrentUser(): ?User
    {
        try {
            if(static::$currentUser === null) {
                static::$currentUser = $this->userRepository->getCurrentUser();
            }

            return static::$currentUser;
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToRetrieveCurrentUser($e);
        }
    }

    /**
     * @return UserCollection
     * @throws TimelineException
     */
    public function getAll(): UserCollection
    {
        try {
            $currentUser = $this->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if(!$currentUser->isAdmin()) {
                throw TimelineException::ofUnAuthorizedToViewOtherUser();
            }

            return $this->userRepository->getAll();
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToRetrieveUsers($e);
        }
    }

    /**
     * @param UserId $id
     * @param null|string $name
     * @param null|string $oldPassword
     * @param null|string $newPassword
     * @param bool|null $isAdmin
     * @param bool|null $isEditor
     * @param bool|null $isActive
     * @return User
     * @throws TimelineException
     */
    public function update(
        UserId $id,
        ?string $name = null,
        ?string $oldPassword = null,
        ?string $newPassword = null,
        ?bool $isAdmin = null,
        ?bool $isEditor = null,
        ?bool $isActive = null
    ): User
    {
        try {
            $currentUser = $this->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin()) {
                if ($isAdmin !== null || $isEditor !== null || $isActive !== null) {
                    throw TimelineException::ofUnauthorizedToUpdateUserPrivilege();
                }

                if (!$currentUser->getId()->equalsWith($id)) {
                    throw TimelineException::ofUnauthorizedToUpdateUserProfile();
                }
            }

            if(!$currentUser->isAdmin() || $id->equalsWith($currentUser->getId())) {
                if(!$this->userRepository->validatePassword($id, $oldPassword)) {
                    throw TimelineException::ofOldPasswordIsNotCorrect();
                }
            }

            return $this->userRepository->update(
                $id,
                $name,
                $newPassword,
                $isAdmin,
                $isEditor,
                $isActive
            );
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToUpdateUserProfile($e);
        }
    }
}