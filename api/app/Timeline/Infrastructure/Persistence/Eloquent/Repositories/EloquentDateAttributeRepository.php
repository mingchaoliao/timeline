<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 6/21/18
 * Time: 9:24 PM
 */

namespace App\Timeline\Infrastructure\Persistence\Eloquent\Repositories;

use App\Timeline\Domain\Collections\DateAttributeCollection;
use App\Timeline\Domain\Collections\TypeaheadCollection;
use App\Timeline\Domain\Models\DateAttribute;
use App\Timeline\Domain\Models\Typeahead;
use App\Timeline\Domain\Repositories\DateAttributeRepository;
use App\Timeline\Domain\ValueObjects\DateAttributeId;
use App\Timeline\Domain\ValueObjects\UserId;
use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentDateAttribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class EloquentDateAttributeRepository implements DateAttributeRepository
{
    /**
     * @return TypeaheadCollection
     * @throws TimelineException
     */
    public function getTypeahead(): TypeaheadCollection
    {
        try {
            $payload = app(EloquentDateAttribute::class)
                ->select(['id', 'value'])
                ->get()
                ->map(function (EloquentDateAttribute $dateAttribute) {
                    return new Typeahead($dateAttribute->getId(), $dateAttribute->getValue());
                })
                ->toArray();
            return new TypeaheadCollection($payload);
        } catch (QueryException $e) {
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
            $eloquentCollection = app(EloquentDateAttribute::class)
                ->with(['create_user', 'update_user'])
                ->get();

            return $this->constructDateAttributeCollection($eloquentCollection);
        } catch (QueryException $e) {
            throw TimelineException::ofUnableToRetrieveDateAttributes();
        }
    }

    /**
     * @param string $value
     * @param UserId $createUserId
     * @return DateAttribute
     * @throws TimelineException
     */
    public function create(string $value, UserId $createUserId): DateAttribute
    {
        try {
            return $this->constructDateAttribute(
                app(EloquentDateAttribute::class)->create([
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
                throw TimelineException::ofDuplicatedDateAttributeValue($value);
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
     * @return DateAttributeCollection
     * @throws TimelineException
     */
    public function bulkCreate(array $values, UserId $createUserId): DateAttributeCollection
    {
        $collection = new DateAttributeCollection();

        DB::transaction(function () use ($collection, $values, $createUserId) {
            foreach ($values as $value) {
                $collection->push($this->create($value, $createUserId));
            }
        });

        return $collection;
    }

    /**
     * @param DateAttributeId $id
     * @param string $value
     * @param UserId $updateUserId
     * @return DateAttribute
     * @throws TimelineException
     */
    public function update(DateAttributeId $id, string $value, UserId $updateUserId): DateAttribute
    {
        try {
            $dateAttribute = app(EloquentDateAttribute::class)->find($id->getValue());

            if ($dateAttribute === null) {
                throw TimelineException::ofDateAttributeWithIdDoesNotExist($id);
            }

            $dateAttribute->update([
                'value' => $value,
                'update_user_id' => $updateUserId->getValue()
            ]);

            return $this->constructDateAttribute(app(EloquentDateAttribute::class)->find($id->getValue()));
        } catch (QueryException $e) {
            /** @var \PDOException $pdoException */
            $pdoException = $e->getPrevious();
            $errorInfo = $pdoException->errorInfo;

            if ($errorInfo['1'] === 1062) { // duplicated value
                throw TimelineException::ofDuplicatedDateAttributeValue($value);
            } elseif ($errorInfo['1'] === 1452) { // non-exist user id
                throw TimelineException::ofUserWithIdDoesNotExist($updateUserId);
            } else {
                throw TimelineException::ofUnknownDatabaseError();
            }
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
            $catalog = app(EloquentDateAttribute::class)->find($id->getValue());

            if ($catalog === null) {
                throw TimelineException::ofDateAttributeWithIdDoesNotExist($id);
            }

            return $catalog->delete();
        } catch (QueryException $e) {
            throw TimelineException::ofUnableToDeleteDateAttribute($id);
        }
    }

    private function constructDateAttributeCollection(Collection $eloquentCollection): DateAttributeCollection
    {
        return new DateAttributeCollection(
            $eloquentCollection->map(function (EloquentDateAttribute $eloquentDateAttribute) {
                return $this->constructDateAttribute($eloquentDateAttribute);
            })->toArray()
        );
    }

    public function constructDateAttribute(EloquentDateAttribute $eloquentDateAttribute): DateAttribute
    {
        return new DateAttribute(
            new DateAttributeId($eloquentDateAttribute->getId()),
            $eloquentDateAttribute->getValue(),
            new UserId($eloquentDateAttribute->getCreateUserId()),
            $eloquentDateAttribute->getCreateUser()->getName(),
            new UserId($eloquentDateAttribute->getUpdateUserId()),
            $eloquentDateAttribute->getUpdateUser()->getName(),
            $eloquentDateAttribute->getCreatedAt(),
            $eloquentDateAttribute->getUpdatedAt()
        );
    }
}