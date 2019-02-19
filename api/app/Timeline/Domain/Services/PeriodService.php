<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/16/19
 * Time: 9:29 PM
 */

namespace App\Timeline\Domain\Services;


use App\Events\TimelinePeriodDeleted;
use App\Events\TimelinePeriodUpdated;
use App\Timeline\Domain\Collections\PeriodCollection;
use App\Timeline\Domain\Collections\TypeaheadCollection;
use App\Timeline\Domain\Models\Period;
use App\Timeline\Domain\Repositories\PeriodRepository;
use App\Timeline\Domain\ValueObjects\PeriodId;
use App\Timeline\Exceptions\TimelineException;

class PeriodService
{
    /**
     * @var PeriodRepository
     */
    private $periodRepository;
    /**
     * @var UserService
     */
    private $userService;

    /**
     * PeriodService constructor.
     * @param PeriodRepository $periodRepository
     * @param UserService $userService
     */
    public function __construct(PeriodRepository $periodRepository, UserService $userService)
    {
        $this->periodRepository = $periodRepository;
        $this->userService = $userService;
    }

    /**
     * @return TypeaheadCollection
     * @throws TimelineException
     */
    public function getTypeahead(): TypeaheadCollection
    {
        try {
            return $this->periodRepository->getTypeahead();
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToRetrievePeriods($e);
        }
    }

    /**
     * @return PeriodCollection
     * @throws TimelineException
     */
    public function getAll(): PeriodCollection
    {
        try {
            return $this->periodRepository->getAll();
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToRetrievePeriods($e);
        }
    }

    /**
     * @param string $value
     * @return Period
     * @throws TimelineException
     */
    public function create(string $value): Period
    {
        try {
            $currentUser = $this->userService->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToCreatePeriod();
            }

            return $this->periodRepository->create($value, $currentUser->getId());
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToCreatePeriod($e);
        }
    }

    /**
     * @param array $values
     * @return PeriodCollection
     * @throws TimelineException
     */
    public function bulkCreate(array $values): PeriodCollection
    {
        if (empty($values)) {
            return new PeriodCollection();
        }

        try {
            $currentUser = $this->userService->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToCreatePeriod();
            }

            return $this->periodRepository->bulkCreate($values, $currentUser->getId());
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToCreatePeriod($e);
        }
    }

    /**
     * @param PeriodId $id
     * @param string $value
     * @return Period
     * @throws TimelineException
     */
    public function update(PeriodId $id, string $value): Period
    {
        try {
            $currentUser = $this->userService->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToUpdatePeriod($id);
            }

            $period = $this->periodRepository->update($id, $value, $currentUser->getId());

            TimelinePeriodUpdated::dispatch($id);

            return $period;
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToUpdatePeriod($id, $e);
        }
    }

    /**
     * @param PeriodId $id
     * @return bool
     * @throws TimelineException
     */
    public function delete(PeriodId $id): bool
    {
        try {
            $currentUser = $this->userService->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if (!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToDeletePeriod($id);
            }

            $success = $this->periodRepository->delete($id);

            TimelinePeriodDeleted::dispatch();

            return $success;
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::AofUnableToDeletePeriod($id, $e);
        }
    }
}