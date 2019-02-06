<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 9:27 PM
 */

namespace App\Timeline\Domain\Collections;


use App\Timeline\Domain\ValueObjects\ImageId;

class ImageIdCollection extends BaseSingleValueCollection
{
    public static function createFromArray(?array $ids): ?self {
        if($ids === null) {
            return null;
        }

        return new static(array_map(function(string $id) {
            return ImageId::createFromString($id);
        },$ids));
    }
}