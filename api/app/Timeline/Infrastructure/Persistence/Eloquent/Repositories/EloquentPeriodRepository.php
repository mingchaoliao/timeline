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
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentPeriod;
use App\Timeline\Exceptions\PeriodNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class EloquentPeriodRepository extends EloquentBaseRepository
{
    public function getTypeahead(): TypeaheadCollection
    {
        $payload = EloquentPeriod::select(['id', 'value'])
            ->get()
            ->map(function (EloquentPeriod $period) {
                return new Typeahead($period->getId(), $period->getValue());
            })
            ->toArray();
        return new TypeaheadCollection($payload);
    }

    public function getCollection(): PeriodCollection
    {
        $eloquentCollection = EloquentPeriod::with(['create_user', 'update_user'])->get();
        return $this->constructPeriodCollection($eloquentCollection);
    }

    public function create(string $value, int $createUserId): Period
    {
        $eloquentPeriod = EloquentPeriod::createNew(
            $value,
            $createUserId
        );
        return $this->constructPeriod($eloquentPeriod);
    }

    public function update(int $id, string $value): Period
    {
        $period = EloquentPeriod::where('id', '=', $id)->first();
        if ($period === null) {
            throw new \InvalidArgumentException(sprintf(
                'Period with id "%s" not found',
                $id
            ));
        }

        $period->update([
            'value' => $value,
            'update_user_id' => Auth::id()
        ]);

        return $this->getById($id);
    }

    public function delete(int $id): bool
    {
        $period = EloquentPeriod::where('id', '=', $id)->first();
        if ($period === null) {
            throw new \InvalidArgumentException(sprintf(
                'Period with id "%s" not found',
                $id
            ));
        }

        return $period->delete();
    }

    public function bulkCreate(array $values, int $createUserId): PeriodCollection
    {
        $eloquentPeriods = EloquentPeriod::byValues($values)->get();

        $existingValues = $eloquentPeriods->map(function (EloquentPeriod $period) {
            return $period->getValue();
        })->toArray();

        $values = array_unique($values);
        $values = array_diff($values, $existingValues);

        $response = EloquentPeriod::bulkCreate($values, $createUserId);
        $eloquentPeriods = $eloquentPeriods->merge($response);

        return $this->constructPeriodCollection($eloquentPeriods);
    }

    public function doesValueExist($value): bool
    {
        try {
            $this->getByValue($value);
            return true;
        } catch (PeriodNotFoundException $e) {
            return false;
        }
    }

    public function getByValue(string $value): Period
    {
        $eloquentPeriod = EloquentPeriod::where('value', $value)->first();
        if ($eloquentPeriod === null) {
            throw new PeriodNotFoundException();
        }
        return $this->constructPeriod($eloquentPeriod);
    }

    public function getById(int $id): Period
    {
        $eloquentPeriod = EloquentPeriod::find($id);
        if ($eloquentPeriod === null) {
            throw new PeriodNotFoundException();
        }
        return $this->constructPeriod($eloquentPeriod);
    }

    public function constructPeriod(EloquentPeriod $eloquentPeriod): Period
    {
        return new Period(
            $eloquentPeriod->getId(),
            $eloquentPeriod->getValue(),
            $eloquentPeriod->getNumberOfEvents(),
            $eloquentPeriod->getCreateUserId(),
            $eloquentPeriod->getCreateUser()->getName(),
            $eloquentPeriod->getUpdateUserId(),
            $eloquentPeriod->getUpdateUser() === null ? null : $eloquentPeriod->getUpdateUser()->getName(),
            $eloquentPeriod->getCreatedAt(),
            $eloquentPeriod->getUpdatedAt()
        );
    }

    public function constructPeriodCollection(Collection $collection): PeriodCollection
    {
        $results = new PeriodCollection();
        foreach ($collection as $item) {
            $results->push($this->constructPeriod($item));
        }
        return $results;
    }
}