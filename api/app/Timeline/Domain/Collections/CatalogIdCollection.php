<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 9:27 PM
 */

namespace App\Timeline\Domain\Collections;


use App\Timeline\Domain\ValueObjects\CatalogId;

class CatalogIdCollection extends BaseSingleValueCollection
{
    public static function createFromValueArray(?array $ids): ?self
    {
        if ($ids === null) {
            return null;
        }

        return new static(array_map(function (string $id) {
            return CatalogId::createFromString($id);
        }, $ids));
    }
}