<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 6/21/18
 * Time: 9:24 PM
 */

namespace App\Timeline\Infrastructure\Persistence\Eloquent\Repositories;


use App\Timeline\Domain\Models\Catalog;
use App\Timeline\Domain\Collections\CatalogCollection;
use App\Timeline\Domain\Collections\TypeaheadCollection;
use App\Timeline\Domain\Models\Typeahead;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentCatalog;
use App\Timeline\Exceptions\CatalogNotFoundException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class EloquentCatalogRepository extends EloquentBaseRepository
{
    public function getTypeahead(): TypeaheadCollection
    {
        $payload = EloquentCatalog::select(['id', 'value'])
            ->get()
            ->map(function (EloquentCatalog $period) {
                return new Typeahead($period->getId(), $period->getValue());
            })
            ->toArray();
        return new TypeaheadCollection($payload);
    }

    public function getCollection(): CatalogCollection
    {
        $eloquentCollection = EloquentCatalog::with(['create_user', 'update_user'])->get();
        return $this->constructCatalogCollection($eloquentCollection);
    }

    public function bulkCreate(array $values, int $createUserId): CatalogCollection
    {
        $eloquentCatalogs = EloquentCatalog::byValues($values)->get();

        $existingValues = $eloquentCatalogs->map(function (EloquentCatalog $catalog) {
            return $catalog->getValue();
        })->toArray();

        $values = array_unique($values);
        $values = array_diff($values, $existingValues);

        $response = EloquentCatalog::bulkCreate($values, $createUserId);

        $eloquentCatalogs = $eloquentCatalogs->merge($response);

        return $this->constructCatalogCollection($eloquentCatalogs);
    }

    public function update(int $id, string $value): Catalog
    {
        $catalog = EloquentCatalog::where('id', '=', $id)->first();
        if ($catalog === null) {
            throw new \InvalidArgumentException(sprintf(
                'Catalog with id "%s" not found',
                $id
            ));
        }

        $catalog->update([
            'value' => $value,
            'update_user_id' => Auth::id()
        ]);

        return $this->getById($id);
    }

    public function delete(int $id): bool
    {
        $catalog = EloquentCatalog::where('id', '=', $id)->first();
        if ($catalog === null) {
            throw new \InvalidArgumentException(sprintf(
                'Catalog with id "%s" not found',
                $id
            ));
        }

        return $catalog->delete();
    }

    public function create(string $value, int $createUserId): Catalog
    {
        $eloquentCatalog = EloquentCatalog::createNew(
            $value,
            $createUserId
        );
        return $this->constructCatalog($eloquentCatalog);
    }

    public function doesValueExist($value): bool
    {
        try {
            $this->getByValue($value);
            return true;
        } catch (CatalogNotFoundException $e) {
            return false;
        }
    }

    public function getByValue(string $value): Catalog
    {
        $eloquentCatalog = EloquentCatalog::where('value', $value)->first();
        if ($eloquentCatalog === null) {
            throw new CatalogNotFoundException();
        }
        return $this->constructCatalog($eloquentCatalog);
    }

    public function getCollectionByIds(array $ids): CatalogCollection
    {
        $eloquentCatalogCollection = EloquentCatalog::findMany($ids);
        return $this->constructCatalogCollection($eloquentCatalogCollection);
    }

    public function constructCatalog(EloquentCatalog $eloquentCatalog): Catalog
    {
        return new Catalog(
            $eloquentCatalog->getId(),
            $eloquentCatalog->getValue(),
            $eloquentCatalog->getNumberOfEvents(),
            $eloquentCatalog->getCreateUserId(),
            $eloquentCatalog->getCreateUser()->getName(),
            $eloquentCatalog->getUpdateUserId(),
            $eloquentCatalog->getUpdateUser() === null ? null : $eloquentCatalog->getUpdateUser()->getName(),
            $eloquentCatalog->getCreatedAt(),
            $eloquentCatalog->getUpdatedAt()
        );
    }

    public function getById(int $id): Catalog
    {
        $catalog = EloquentCatalog::find($id);
        if ($catalog === null) {
            throw new CatalogNotFoundException();
        }
        return $this->constructCatalog($catalog);
    }

    public function constructCatalogCollection(Collection $collection): CatalogCollection
    {
        $results = new CatalogCollection();
        foreach ($collection as $item) {
            $results->push($this->constructCatalog($item));
        }
        return $results;
    }
}