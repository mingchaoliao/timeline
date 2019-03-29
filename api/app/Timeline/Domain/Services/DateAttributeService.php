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
use App\Timeline\Domain\ValueObjects\DateAttributeId;
use App\Timeline\Exceptions\TimelineException;

class DateAttributeService
{
    /**
     * @var DateAttributeRepository
     */
    private $dateAttributeRepository;
    /**
     * @var UserService
     */
    private $userService;

    /**
     * DateAttributeService constructor.
     * @param DateAttributeRepository $dateAttributeRepository
     * @param UserService $userService
     */
    public function __construct(DateAttributeRepository $dateAttributeRepository, UserService $userService)
    {
        $this->dateAttributeRepository = $dateAttributeRepository;
        $this->userService = $userService;
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
            throw TimelineException::ofUnableToRetrieveDateAttributes($e);
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
            throw TimelineException::ofUnableToRetrieveDateAttributes($e);
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
            $currentUser = $this->userService->getCurrentUser();

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
            throw TimelineException::ofUnableToCreateDateAttribute($e);
        }
    }

    /**
     * @param array $values
     * @return DateAttributeCollection
     * @throws TimelineException
     */
    public function bulkCreate(array $values): DateAttributeCollection
    {
        if (empty($values)) {
            return new DateAttributeCollection();
        }

        try {
            $currentUser = $this->userService->getCurrentUser();

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
            throw TimelineException::ofUnableToCreateDateAttribute($e);
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
            $currentUser = $this->userService->getCurrentUser();

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
            throw TimelineException::ofUnableToUpdateDateAttribute($id, $e);
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
            $currentUser = $this->userService->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthorizedToUpdateDateAttribute($id);
            }

            return $this->dateAttributeRepository->delete($id);
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToDeleteDateAttribute($id, $e);
        }
    }
}