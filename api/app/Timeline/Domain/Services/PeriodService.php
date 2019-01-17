<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/16/19
 * Time: 9:29 PM
 */

namespace App\Timeline\Domain\Services;


use App\Timeline\Domain\Collections\PeriodCollection;
use App\Timeline\Domain\Collections\TypeaheadCollection;
use App\Timeline\Domain\Models\Period;
use App\Timeline\Domain\Repositories\PeriodRepository;
use App\Timeline\Domain\Repositories\UserRepository;
use App\Timeline\Domain\ValueObjects\PeriodId;
use App\Timeline\Exceptions\TimelineException;

class PeriodService
{
    /**
     * @var PeriodRepository
     */
    private $periodRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * PeriodService constructor.
     * @param PeriodRepository $periodRepository
     * @param UserRepository $userRepository
     */
    public function __construct(PeriodRepository $periodRepository, UserRepository $userRepository)
    {
        $this->periodRepository = $periodRepository;
        $this->userRepository = $userRepository;
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
            throw TimelineException::ofUnableToRetrievePeriods();
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
            throw TimelineException::ofUnableToRetrievePeriods();
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
            $currentUser = $this->userRepository->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if(!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToCreatePeriod();
            }

            return $this->periodRepository->create($value, $currentUser->getId());
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToCreatePeriod();
        }
    }

    /**
     * @param array $values
     * @return PeriodCollection
     * @throws TimelineException
     */
    public function bulkCreate(array $values): PeriodCollection
    {
        try {
            $currentUser = $this->userRepository->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if(!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToCreatePeriod();
            }

            return $this->periodRepository->bulkCreate($values, $currentUser->getId());
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToCreatePeriod();
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
            $currentUser = $this->userRepository->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if(!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToUpdatePeriod($id);
            }

            return $this->periodRepository->update($id, $value, $currentUser->getId());
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToUpdatePeriod($id);
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
            $currentUser = $this->userRepository->getCurrentUser();

            if ($currentUser === null) {
                throw TimelineException::ofUnauthenticated();
            }

            if(!$currentUser->isAdmin() && !$currentUser->isEditor()) {
                throw TimelineException::ofUnauthorizedToDeletePeriod($id);
            }

            return $this->periodRepository->delete($id);
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToDeletePeriod($id);
        }
    }
}