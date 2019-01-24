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
     * @var EloquentDateAttribute
     */
    private $dateAttributeModel;

    /**
     * EloquentDateAttributeRepository constructor.
     * @param EloquentDateAttribute $dateAttributeModel
     */
    public function __construct(EloquentDateAttribute $dateAttributeModel)
    {
        $this->dateAttributeModel = $dateAttributeModel;
    }

    /**
     * @return TypeaheadCollection
     */
    public function getTypeahead(): TypeaheadCollection
    {
        $payload = $this->dateAttributeModel
            ->select(['id', 'value'])
            ->get()
            ->map(function (EloquentDateAttribute $dateAttribute) {
                return new Typeahead($dateAttribute->getId(), $dateAttribute->getValue());
            })
            ->toArray();
        return new TypeaheadCollection($payload);
    }

    /**
     * @return DateAttributeCollection
     */
    public function getAll(): DateAttributeCollection
    {
        $eloquentCollection = $this->dateAttributeModel
            ->with(['create_user', 'update_user'])
            ->get();

        return $this->constructDateAttributeCollection($eloquentCollection);
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
                $this->dateAttributeModel->create([
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
                throw TimelineException::ofDuplicatedDateAttributeValue($value, $e);
            } elseif ($errorInfo['1'] === 1452) { // non-exist user id
                throw TimelineException::ofUserWithIdDoesNotExist($createUserId, $e);
            }

            throw $e;
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
        $values = array_unique($values);

        $existingDateAttributes = $this->dateAttributeModel
            ->whereIn('value', $values)
            ->get();

        $existingValues = $existingDateAttributes->map(function (EloquentDateAttribute $model) {
            return $model->getValue();
        })
            ->toArray();
        $values = array_diff($values, $existingValues);

        $collection = new DateAttributeCollection();

        DB::transaction(function () use ($collection, $values, $createUserId) {
            foreach ($values as $value) {
                $collection->push($this->create($value, $createUserId));
            }
        });

        return $this->constructDateAttributeCollection($existingDateAttributes)->merge($collection);
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
            $dateAttribute = $this->dateAttributeModel->find($id->getValue());

            if ($dateAttribute === null) {
                throw TimelineException::ofDateAttributeWithIdDoesNotExist($id);
            }

            $dateAttribute->update([
                'value' => $value,
                'update_user_id' => $updateUserId->getValue()
            ]);

            return $this->constructDateAttribute($this->dateAttributeModel->find($id->getValue()));
        } catch (QueryException $e) {
            /** @var \PDOException $pdoException */
            $pdoException = $e->getPrevious();
            $errorInfo = $pdoException->errorInfo;

            if ($errorInfo['1'] === 1062) { // duplicated value
                throw TimelineException::ofDuplicatedDateAttributeValue($value, $e);
            } elseif ($errorInfo['1'] === 1452) { // non-exist user id
                throw TimelineException::ofUserWithIdDoesNotExist($updateUserId, $e);
            }

            throw $e;
        }
    }

    /**
     * @param DateAttributeId $id
     * @return bool
     * @throws TimelineException
     */
    public function delete(DateAttributeId $id): bool
    {
        $catalog = $this->dateAttributeModel->find($id->getValue());

        if ($catalog === null) {
            throw TimelineException::ofDateAttributeWithIdDoesNotExist($id);
        }

        return $catalog->delete();
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