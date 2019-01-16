<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 6/21/18
 * Time: 9:24 PM
 */

namespace App\Timeline\Infrastructure\Persistence\Eloquent\Repositories;


use App\Timeline\Domain\Collections\PeriodCollection;
use App\Timeline\Domain\Collections\TypeaheadCollection;
use App\Timeline\Domain\Models\Period;
use App\Timeline\Domain\Models\Typeahead;
use App\Timeline\Domain\Repositories\PeriodRepository;
use App\Timeline\Domain\ValueObjects\PeriodId;
use App\Timeline\Domain\ValueObjects\UserId;
use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentPeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class EloquentPeriodRepository implements PeriodRepository
{
    /**
     * @return TypeaheadCollection
     * @throws TimelineException
     */
    public function getTypeahead(): TypeaheadCollection
    {
        try {
            $payload = app(EloquentPeriod::class)
                ->select(['id', 'value'])
                ->get()
                ->map(function (EloquentPeriod $period) {
                    return new Typeahead($period->getId(), $period->getValue());
                })
                ->toArray();
            return new TypeaheadCollection($payload);
        } catch (QueryException $e) {
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
            $eloquentCollection = app(EloquentPeriod::class)
                ->with(['create_user', 'update_user'])
                ->get();

            return $this->constructPeriodCollection($eloquentCollection);
        } catch (QueryException $e) {
            throw TimelineException::ofUnableToRetrievePeriods();
        }
    }

    /**
     * @param string $value
     * @param UserId $createUserId
     * @return Period
     * @throws TimelineException
     */
    public function create(string $value, UserId $createUserId): Period
    {
        try {
            return $this->constructPeriod(
                app(EloquentPeriod::class)->create([
                    'value' => $value,
                    'create_user_id' => $createUserId->getValue(),
                    'update_user_id' => $createUserId->getValue()
                ])
            );
        } catch (QueryException $e) {
            /** @var \PDOException $pdoException */
            $pdoException = $e->getPrevious();
            $errorInfo = $pdoException->errorInfo;

            if ($errorInfo['1'] === 1062) { // duplicated value
                throw TimelineException::ofDuplicatedPeriodValue($value);
            } elseif ($errorInfo['1'] === 1452) { // non-exist user id
                throw TimelineException::ofUserWithIdDoesNotExist($createUserId);
            } else {
                throw TimelineException::ofUnknownDatabaseError();
            }
        }
    }

    /**
     * @param array $values
     * @param UserId $createUserId
     * @return PeriodCollection
     * @throws TimelineException
     */
    public function bulkCreate(array $values, UserId $createUserId): PeriodCollection
    {
        $collection = new PeriodCollection();

        DB::transaction(function () use ($collection, $values, $createUserId) {
            foreach ($values as $value) {
                $collection->push($this->create($value, $createUserId));
            }
        });

        return $collection;
    }

    /**
     * @param PeriodId $id
     * @param string $value
     * @param UserId $updateUserId
     * @return Period
     * @throws TimelineException
     */
    public function update(PeriodId $id, string $value, UserId $updateUserId): Period
    {
        try {
            $period = app(EloquentPeriod::class)->find($id->getValue());

            if ($period === null) {
                throw TimelineException::ofPeriodWithIdDoesNotExist($id);
            }

            $period->update([
                'value' => $value,
                'update_user_id' => $updateUserId->getValue()
            ]);

            return $this->constructPeriod(app(EloquentPeriod::class)->find($id->getValue()));
        } catch (QueryException $e) {
            /** @var \PDOException $pdoException */
            $pdoException = $e->getPrevious();
            $errorInfo = $pdoException->errorInfo;

            if ($errorInfo['1'] === 1062) { // duplicated value
                throw TimelineException::ofDuplicatedPeriodValue($value);
            } elseif ($errorInfo['1'] === 1452) { // non-exist user id
                throw TimelineException::ofUserWithIdDoesNotExist($updateUserId);
            } else {
                throw TimelineException::ofUnknownDatabaseError();
            }
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
            $catalog = app(EloquentPeriod::class)->find($id->getValue());

            if ($catalog === null) {
                throw TimelineException::ofPeriodWithIdDoesNotExist($id);
            }

            return $catalog->delete();
        } catch (QueryException $e) {
            throw TimelineException::ofUnableToDeletePeriod($id);
        }
    }

    private function constructPeriodCollection(Collection $eloquentCollection): PeriodCollection
    {
        return new PeriodCollection(
            $eloquentCollection->map(function (EloquentPeriod $eloquentPeriod) {
                return $this->constructPeriod($eloquentPeriod);
            })->toArray()
        );
    }

    public function constructPeriod(EloquentPeriod $eloquentPeriod): Period
    {
        return new Period(
            new PeriodId($eloquentPeriod->getId()),
            $eloquentPeriod->getValue(),
            $eloquentPeriod->getNumberOfEvents(),
            new UserId($eloquentPeriod->getCreateUserId()),
            $eloquentPeriod->getCreateUser()->getName(),
            new UserId($eloquentPeriod->getUpdateUserId()),
            $eloquentPeriod->getUpdateUser()->getName(),
            $eloquentPeriod->getCreatedAt(),
            $eloquentPeriod->getUpdatedAt()
        );
    }
}