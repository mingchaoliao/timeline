<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 6/21/18
 * Time: 9:24 PM
 */

namespace App\Repositories;


use App\DomainModels\Collections\DateAttributeCollection;
use App\DomainModels\Collections\TypeaheadCollection;
use App\DomainModels\DateAttribute;
use App\DomainModels\Typeahead;
use App\EloquentModels\EloquentDateAttribute;
use App\Exceptions\DateAttributeNotFoundException;
use Illuminate\Database\Eloquent\Collection;

class DateAttributeRepository extends BaseRepository
{
    public function getTypeahead(): TypeaheadCollection
    {
        $payload = EloquentDateAttribute::select(['id', 'value'])
            ->get()
            ->map(function (EloquentDateAttribute $dateAttribute) {
                return new Typeahead($dateAttribute->getId(), $dateAttribute->getValue());
            })
            ->toArray();
        return new TypeaheadCollection($payload);
    }

    public function getCollection(): DateAttributeCollection
    {
        $eloquentCollection = EloquentDateAttribute::with(['create_user', 'update_user'])->get();
        return $this->constructDateAttributeCollection($eloquentCollection);
    }

    public function update(int $id, string $value): DateAttribute
    {
        $dateAttribute = EloquentDateAttribute::where('id', '=', $id)->first();
        if ($dateAttribute === null) {
            throw new \InvalidArgumentException(sprintf(
                'DateAttribute with id "%s" not found',
                $id
            ));
        }

        $dateAttribute->update([
            'value' => $value,
            'update_user_id' => Auth::id()
        ]);

        return $this->getById($id);
    }

    public function delete(int $id): bool
    {
        $dateAttribute = EloquentDateAttribute::where('id', '=', $id)->first();
        if ($dateAttribute === null) {
            throw new \InvalidArgumentException(sprintf(
                'DateAttribute with id "%s" not found',
                $id
            ));
        }

        return $dateAttribute->delete();
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
            $eloquentDateAttribute->getCreateUser()->getName(),
            $eloquentDateAttribute->getUpdateUserId(),
            $eloquentDateAttribute->getUpdateUser() === null ? null : $eloquentDateAttribute->getUpdateUser()->getName(),
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