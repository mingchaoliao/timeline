<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 6/21/18
 * Time: 9:24 PM
 */

namespace App\Repositories;


use App\DomainModels\Catalog;
use App\DomainModels\Collections\CatalogCollection;
use App\EloquentModels\EloquentCatalog;
use App\Exceptions\CatalogNotFoundException;
use Illuminate\Database\Eloquent\Collection;

class CatalogRepository extends BaseRepository
{
    public function getCollection(): CatalogCollection {
        $eloquentCatalogCollection = EloquentCatalog::all();
        return $this->constructCatalogCollection($eloquentCatalogCollection);
    }

    public function bulkCreate(array $values, int $createUserId): CatalogCollection {
        $eloquentCatalogs = EloquentCatalog::byValues($values)->get();

        $existingValues = $eloquentCatalogs->map(function(EloquentCatalog $catalog) {
            return $catalog->getValue();
        })->toArray();

        $values = array_unique($values);
        $values = array_diff($values, $existingValues);

        $response = EloquentCatalog::bulkCreate($values, $createUserId);

        $eloquentCatalogs = $eloquentCatalogs->merge($response);

        return $this->constructCatalogCollection($eloquentCatalogs);
    }

    public function create(string $value, int $createUserId): Catalog {
        $eloquentCatalog = EloquentCatalog::createNew(
            $value,
            $createUserId
        );
        return $this->constructCatalog($eloquentCatalog);
    }

    public function doesValueExist($value): bool {
        try {
            $this->getByValue($value);
            return true;
        } catch(CatalogNotFoundException $e) {
            return false;
        }
    }

    public function getByValue(string $value): Catalog {
        $eloquentCatalog = EloquentCatalog::where('value', $value)->first();
        if($eloquentCatalog === null) {
            throw new CatalogNotFoundException();
        }
        return $this->constructCatalog($eloquentCatalog);
    }
    
    public function getCollectionByIds(array $ids): CatalogCollection {
        $eloquentCatalogCollection = EloquentCatalog::findMany($ids);
        return $this->constructCatalogCollection($eloquentCatalogCollection);
    }

    public function constructCatalog(EloquentCatalog $eloquentCatalog): Catalog
    {
        return new Catalog(
            $eloquentCatalog->getId(),
            $eloquentCatalog->getValue(),
            $eloquentCatalog->getCreateUserId(),
            $eloquentCatalog->getUpdateUserId(),
            $eloquentCatalog->getCreatedAt(),
            $eloquentCatalog->getUpdatedAt()
        );
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