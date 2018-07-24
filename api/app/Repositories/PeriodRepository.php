<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 6/21/18
 * Time: 9:24 PM
 */

namespace App\Repositories;


use App\DomainModels\Collections\PeriodCollection;
use App\DomainModels\Period;
use App\EloquentModels\EloquentPeriod;
use App\Exceptions\PeriodNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class PeriodRepository extends BaseRepository
{
    public function getCollection(): PeriodCollection {
        $eloquentCollection = EloquentPeriod::all();
        return $this->constructPeriodCollection($eloquentCollection);
    }

    public function create(string $value, int $createUserId): Period {
        $eloquentPeriod = EloquentPeriod::createNew(
            $value,
            $createUserId
        );
        return $this->constructPeriod($eloquentPeriod);
    }

    public function bulkCreate(array $values, int $createUserId): PeriodCollection {
        $eloquentPeriods = EloquentPeriod::byValues($values)->get();

        $existingValues = $eloquentPeriods->map(function(EloquentPeriod $period) {
            return $period->getValue();
        })->toArray();

        $values = array_unique($values);
        $values = array_diff($values, $existingValues);

        $response = EloquentPeriod::bulkCreate($values, $createUserId);
        $eloquentPeriods = $eloquentPeriods->merge($response);

        return $this->constructPeriodCollection($eloquentPeriods);
    }

    public function doesValueExist($value): bool {
        try {
            $this->getByValue($value);
            return true;
        } catch(PeriodNotFoundException $e) {
            return false;
        }
    }

    public function getByValue(string $value): Period {
        $eloquentPeriod = EloquentPeriod::where('value', $value)->first();
        if($eloquentPeriod === null) {
            throw new PeriodNotFoundException();
        }
        return $this->constructPeriod($eloquentPeriod);
    }

    public function getById(int $id): Period {
        $eloquentPeriod = EloquentPeriod::find($id);
        if($eloquentPeriod === null) {
            throw new PeriodNotFoundException();
        }
        return $this->constructPeriod($eloquentPeriod);
    }
    
    public function constructPeriod(EloquentPeriod $eloquentPeriod): Period
    {
        return new Period(
            $eloquentPeriod->getId(),
            $eloquentPeriod->getValue(),
            $eloquentPeriod->getCreateUserId(),
            $eloquentPeriod->getUpdateUserId(),
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