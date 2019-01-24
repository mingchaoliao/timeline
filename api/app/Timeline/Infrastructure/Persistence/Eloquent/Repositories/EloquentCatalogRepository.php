<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 6/21/18
 * Time: 9:24 PM
 */

namespace App\Timeline\Infrastructure\Persistence\Eloquent\Repositories;


use App\Timeline\Domain\Collections\CatalogCollection;
use App\Timeline\Domain\Collections\CatalogIdCollection;
use App\Timeline\Domain\Collections\TypeaheadCollection;
use App\Timeline\Domain\Models\Catalog;
use App\Timeline\Domain\Models\Typeahead;
use App\Timeline\Domain\Repositories\CatalogRepository;
use App\Timeline\Domain\ValueObjects\CatalogId;
use App\Timeline\Domain\ValueObjects\UserId;
use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentCatalog;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class EloquentCatalogRepository implements CatalogRepository
{
    /**
     * @var EloquentCatalog
     */
    private $catalogModel;

    /**
     * EloquentCatalogRepository constructor.
     * @param EloquentCatalog $catalogModel
     */
    public function __construct(EloquentCatalog $catalogModel)
    {
        $this->catalogModel = $catalogModel;
    }

    /**
     * @return TypeaheadCollection
     */
    public function getTypeahead(): TypeaheadCollection
    {
        $payload = $this->catalogModel
            ->select(['id', 'value'])
            ->get()
            ->map(function (EloquentCatalog $period) {
                return new Typeahead($period->getId(), $period->getValue());
            })
            ->toArray();
        return new TypeaheadCollection($payload);
    }

    /**
     * @return CatalogCollection
     */
    public function getAll(): CatalogCollection
    {
        $eloquentCollection = $this->catalogModel
            ->with(['create_user', 'update_user'])
            ->get();

        return $this->constructCatalogCollection($eloquentCollection);
    }

    /**
     * @param CatalogIdCollection $ids
     * @return CatalogCollection
     */
    public function getByIds(CatalogIdCollection $ids): CatalogCollection
    {
        return $this->constructCatalogCollection(
            $this->catalogModel->findMany($ids->toValueArray())
        );
    }

    /**
     * @param string $value
     * @param UserId $createUserId
     * @return Catalog
     * @throws TimelineException
     */
    public function create(string $value, UserId $createUserId): Catalog
    {
        try {
            return $this->constructCatalog(
                $this->catalogModel->create([
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
                throw TimelineException::ofDuplicatedCatalogValue($value, $e);
            } elseif ($errorInfo['1'] === 1452) { // non-exist user id
                throw TimelineException::ofUserWithIdDoesNotExist($createUserId, $e);
            }

            throw $e;
        }
    }

    /**
     * @param array $values
     * @param UserId $createUserId
     * @return CatalogCollection
     * @throws TimelineException
     */
    public function bulkCreate(array $values, UserId $createUserId): CatalogCollection
    {
        $values = array_unique($values);

        $existingCatalogs = $this->catalogModel
            ->whereIn('value', $values)
            ->get();

        $existingValues = $existingCatalogs->map(function (EloquentCatalog $model) {
            return $model->getValue();
        })
            ->toArray();
        $values = array_diff($values, $existingValues);

        $collection = new CatalogCollection();

        DB::transaction(function () use ($collection, $values, $createUserId) {
            foreach ($values as $value) {
                $collection->push($this->create($value, $createUserId));
            }
        });

        return $this->constructCatalogCollection($existingCatalogs)->merge($collection);
    }

    /**
     * @param CatalogId $id
     * @param string $value
     * @param UserId $updateUserId
     * @return Catalog
     * @throws TimelineException
     */
    public function update(CatalogId $id, string $value, UserId $updateUserId): Catalog
    {
        try {
            $catalog = $this->catalogModel->find($id->getValue());

            if ($catalog === null) {
                throw TimelineException::ofCatalogWithIdDoesNotExist($id);
            }

            $catalog->update([
                'value' => $value,
                'update_user_id' => $updateUserId->getValue()
            ]);

            return $this->constructCatalog($this->catalogModel->find($id->getValue()));
        } catch (QueryException $e) {
            /** @var \PDOException $pdoException */
            $pdoException = $e->getPrevious();
            $errorInfo = $pdoException->errorInfo;

            if ($errorInfo['1'] === 1062) { // duplicated value
                throw TimelineException::ofDuplicatedCatalogValue($value, $e);
            } elseif ($errorInfo['1'] === 1452) { // non-exist user id
                throw TimelineException::ofUserWithIdDoesNotExist($updateUserId, $e);
            }

            throw $e;
        }
    }

    /**
     * @param CatalogId $id
     * @return bool
     * @throws TimelineException
     */
    public function delete(CatalogId $id): bool
    {
        $catalog = $this->catalogModel->find($id->getValue());

        if ($catalog === null) {
            throw TimelineException::ofCatalogWithIdDoesNotExist($id);
        }

        return $catalog->delete();
    }

    public function constructCatalogCollection(Collection $eloquentCollection): CatalogCollection
    {
        return new CatalogCollection(
            $eloquentCollection->map(function (EloquentCatalog $eloquentCatalog) {
                return $this->constructCatalog($eloquentCatalog);
            })->toArray()
        );
    }

    public function constructCatalog(EloquentCatalog $eloquentCatalog): Catalog
    {
        return new Catalog(
            new CatalogId($eloquentCatalog->getId()),
            $eloquentCatalog->getValue(),
            $eloquentCatalog->getNumberOfEvents(),
            new UserId($eloquentCatalog->getCreateUserId()),
            $eloquentCatalog->getCreateUser()->getName(),
            new UserId($eloquentCatalog->getUpdateUserId()),
            $eloquentCatalog->getUpdateUser()->getName(),
            $eloquentCatalog->getCreatedAt(),
            $eloquentCatalog->getUpdatedAt()
        );
    }
}