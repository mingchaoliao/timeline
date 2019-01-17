<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/16/19
 * Time: 9:29 PM
 */

namespace App\Timeline\Domain\Services;


use App\Timeline\Domain\Collections\DateAttributeCollection;
use App\Timeline\Domain\Collections\TypeaheadCollection;
use App\Timeline\Domain\Models\DateAttribute;
use App\Timeline\Domain\Repositories\DateAttributeRepository;
use App\Timeline\Domain\Repositories\UserRepository;
use App\Timeline\Domain\ValueObjects\DateAttributeId;
use App\Timeline\Exceptions\TimelineException;

class DateAttributeService
{
    /**
     * @var DateAttributeRepository
     */
    private $dateAttributeRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * DateAttributeService constructor.
     * @param DateAttributeRepository $dateAttributeRepository
     * @param UserRepository $userRepository
     */
    public function __construct(DateAttributeRepository $dateAttributeRepository, UserRepository $userRepository)
    {
        $this->dateAttributeRepository = $dateAttributeRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @return TypeaheadCollection
     * @throws TimelineException
     */
    public function getTypeahead(): TypeaheadCollection
    {
        try {
            return $this->dateAttributeRepository->getTypeahead();
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToRetrieveDateAttributes();
        }
    }

    /**
     * @return DateAttributeCollection
     * @throws TimelineException
     */
    public function getAll(): DateAttributeCollection
    {
        try {
            return $this->dateAttributeRepository->getAll();
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToRetrieveDateAttributes();
        }
    }

    /**
     * @param string $value
     * @return DateAttribute
     * @throws TimelineException
     */
    public function create(string $value): DateAttribute
    {
        try {
            $currentUser = $this->userRepository->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToCreateDateAttribute();
            }

            return $this->dateAttributeRepository->create($value, $currentUser->getId());
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToCreateDateAttribute();
        }
    }

    /**
     * @param array $values
     * @return DateAttributeCollection
     * @throws TimelineException
     */
    public function bulkCreate(array $values): DateAttributeCollection
    {
        try {
            $currentUser = $this->userRepository->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToCreateDateAttribute();
            }

            return $this->dateAttributeRepository->bulkCreate($values, $currentUser->getId());
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToCreateDateAttribute();
        }
    }

    /**
     * @param DateAttributeId $id
     * @param string $value
     * @return DateAttribute
     * @throws TimelineException
     */
    public function update(DateAttributeId $id, string $value): DateAttribute
    {
        try {
            $currentUser = $this->userRepository->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToUpdateDateAttribute($id);
            }

            return $this->dateAttributeRepository->update($id, $value, $currentUser->getId());
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToUpdateDateAttribute($id);
        }
    }

    /**
     * @param DateAttributeId $id
     * @return bool
     * @throws TimelineException
     */
    public function delete(DateAttributeId $id): bool
    {
        try {
            $currentUser = $this->userRepository->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthorizedToUpdateDateAttribute();
            }

            return $this->dateAttributeRepository->delete($id);
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToDeleteDateAttribute($id);
        }
    }
}