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
            return $this->userRepository->getCurrentUser();
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
                TimelineException::ofUnauthenticated();
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
     * @param null|string $password
     * @param bool|null $isAdmin
     * @param bool|null $isEditor
     * @return User
     * @throws TimelineException
     */
    public function update(
        UserId $id,
        ?string $name = null,
        ?string $password = null,
        ?bool $isAdmin = null,
        ?bool $isEditor = null
    ): User
    {
        try {
            $currentUser = $this->getCurrentUser();

            if ($currentUser === null) {
                TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin()) {
                if ($isAdmin !== null || $isEditor !== null) {
                    TimelineException::ofUnauthorizedToUpdateUserPrivilege();
                }

                if (!$currentUser->getId()->equalsWith($id)) {
                    TimelineException::ofUnauthorizedToUpdateUserProfile();
                }
            }

            return $this->userRepository->update(
                $id,
                $name,
                $password,
                $isAdmin,
                $isEditor
            );
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToUpdateUserProfile($e);
        }
    }
}