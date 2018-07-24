<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 6/21/18
 * Time: 9:24 PM
 */

namespace App\Repositories;


use App\DomainModels\Collections\DateAttributeCollection;
use App\DomainModels\DateAttribute;
use App\EloquentModels\EloquentDateAttribute;
use App\Exceptions\DateAttributeNotFoundException;
use Illuminate\Database\Eloquent\Collection;

class DateAttributeRepository extends BaseRepository
{
    public function getCollection(): DateAttributeCollection
    {
        $eloquentCollection = EloquentDateAttribute::all();

        return $this->constructDateAttributeCollection($eloquentCollection);
    }

    public function create(string $value, int $createUserId): DateAttribute
    {
        $eloquentDateAttribute = EloquentDateAttribute::createNew(
            $value,
            $createUserId
        );

        return $this->constructDateAttribute($eloquentDateAttribute);
    }

    public function bulkCreate(
        array $values,
        int $createUserId
    ): DateAttributeCollection {
        $eloquentDateAttributes = EloquentDateAttribute::byValues($values)->get();

        $existingValues = $eloquentDateAttributes->map(function (
            EloquentDateAttribute $attribute
        ) {
            return $attribute->getValue();
        })->toArray();

        $values = array_unique($values);
        $values = array_diff($values, $existingValues);

        $response = EloquentDateAttribute::bulkCreate($values, $createUserId);
        $eloquentDateAttributes = $eloquentDateAttributes->merge($response);

        return $this->constructDateAttributeCollection($eloquentDateAttributes);
    }

    public function doesValueExist($value): bool
    {
        try {
            $this->getByValue($value);

            return true;
        } catch (DateAttributeNotFoundException $e) {
            return false;
        }
    }

    public function getByValue(string $value): DateAttribute
    {
        $eloquentDateAttribute = EloquentDateAttribute::where('value',
            $value)->first();
        if ($eloquentDateAttribute === null) {
            throw new DateAttributeNotFoundException();
        }

        return $this->constructDateAttribute($eloquentDateAttribute);
    }

    public function getById(int $id): DateAttribute
    {
        $eloquentDateAttribute = EloquentDateAttribute::find($id);
        if ($eloquentDateAttribute === null) {
            throw new DateAttributeNotFoundException();
        }

        return $this->constructDateAttribute($eloquentDateAttribute);
    }

    public function constructDateAttribute(
        EloquentDateAttribute $eloquentDateAttribute
    ): DateAttribute {
        return new DateAttribute(
            $eloquentDateAttribute->getId(),
            $eloquentDateAttribute->getValue(),
            $eloquentDateAttribute->getCreateUserId(),
            $eloquentDateAttribute->getUpdateUserId(),
            $eloquentDateAttribute->getCreatedAt(),
            $eloquentDateAttribute->getUpdatedAt()
        );
    }

    public function constructDateAttributeCollection(Collection $collection
    ): DateAttributeCollection {
        $results = new DateAttributeCollection();
        foreach ($collection as $item) {
            $results->push($this->constructDateAttribute($item));
        }

        return $results;
    }
}