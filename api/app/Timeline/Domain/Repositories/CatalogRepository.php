<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 9:15 PM
 */

namespace App\Timeline\Domain\Repositories;


use App\Timeline\Domain\Collections\CatalogCollection;
use App\Timeline\Domain\Collections\CatalogIdCollection;
use App\Timeline\Domain\Collections\TypeaheadCollection;
use App\Timeline\Domain\Models\Catalog;
use App\Timeline\Domain\ValueObjects\CatalogId;
use App\Timeline\Domain\ValueObjects\UserId;

interface CatalogRepository
{
    public function getTypeahead(): TypeaheadCollection;

    public function getAll(): CatalogCollection;

    public function getByIds(CatalogIdCollection $ids): CatalogCollection;

    public function create(string $value, UserId $createUserId): Catalog;

    public function bulkCreate(array $values, UserId $createUserId): CatalogCollection;

    public function update(CatalogId $id, string $value, UserId $updateUserId): Catalog;

    public function delete(CatalogId $id): bool;
}