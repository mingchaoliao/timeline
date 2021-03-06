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
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class EloquentPeriodRepository implements PeriodRepository
{
    /**
     * @var EloquentPeriod
     */
    private $periodModel;

    /**
     * EloquentPeriodRepository constructor.
     * @param EloquentPeriod $periodModel
     */
    public function __construct(EloquentPeriod $periodModel)
    {
        $this->periodModel = $periodModel;
    }

    /**
     * @return TypeaheadCollection
     */
    public function getTypeahead(): TypeaheadCollection
    {
        $payload = $this->periodModel
            ->select(['id', 'value'])
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(function (EloquentPeriod $period) {
                return new Typeahead($period->getId(), $period->getValue());
            })
            ->toArray();
        return new TypeaheadCollection($payload);
    }

    /**
     * @return PeriodCollection
     */
    public function getAll(): PeriodCollection
    {
        $eloquentCollection = $this->periodModel
            ->with(['create_user', 'update_user'])
            ->orderBy('start_date', 'asc')
            ->get();

        return $this->constructPeriodCollection($eloquentCollection);
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
                $this->periodModel->create([
                    'value' => $value,
                    'create_user_id' => $createUserId->getValue(),
                    'update_user_id' => $createUserId->getValue()
                ])
            );
        } catch (QueryException $e) {
            $errorInfo = $e->errorInfo;

            if ($errorInfo['1'] === 1062) { // duplicated value
                throw TimelineException::ofDuplicatedPeriodValue($value, $e);
            } elseif ($errorInfo['1'] === 1452) { // non-exist user id
                throw TimelineException::ofUserWithIdDoesNotExist($createUserId, $e);
            }

            throw $e;
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
        $values = array_unique($values);

        $existingPeriods = $this->periodModel
            ->whereIn('value', $values)
            ->get();

        $existingValues = $existingPeriods->map(function (EloquentPeriod $model) {
            return $model->getValue();
        })
            ->toArray();
        $values = array_diff($values, $existingValues);

        $collection = new PeriodCollection();

        DB::transaction(function () use ($collection, $values, $createUserId) {
            foreach ($values as $value) {
                $collection->push($this->create($value, $createUserId));
            }
        });

        return $this->constructPeriodCollection($existingPeriods)->merge($collection);
    }

    /**
     * @param PeriodId $id
     * @param string $value
     * @param Carbon|null $startDate
     * @param UserId $updateUserId
     * @return Period
     * @throws TimelineException
     */
    public function update(PeriodId $id, string $value, ?Carbon $startDate, UserId $updateUserId): Period
    {
        try {
            $period = $this->periodModel->find($id->getValue());

            if ($period === null) {
                throw TimelineException::ofPeriodWithIdDoesNotExist($id);
            }

            $period->update([
                'value' => $value,
                'update_user_id' => $updateUserId->getValue(),
                'start_date' => $startDate
            ]);

            return $this->constructPeriod($this->periodModel->find($id->getValue()));
        } catch (QueryException $e) {
            $errorInfo = $e->errorInfo;

            if ($errorInfo['1'] === 1062) { // duplicated value
                throw TimelineException::ofDuplicatedPeriodValue($value);
            } elseif ($errorInfo['1'] === 1452) { // non-exist user id
                throw TimelineException::ofUserWithIdDoesNotExist($updateUserId);
            }

            throw $e;
        }
    }

    /**
     * @param PeriodId $id
     * @return bool
     * @throws TimelineException
     */
    public function delete(PeriodId $id): bool
    {
        $catalog = $this->periodModel->find($id->getValue());

        if ($catalog === null) {
            throw TimelineException::ofPeriodWithIdDoesNotExist($id);
        }

        return $catalog->delete();
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
            $eloquentPeriod->getStartDate(),
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