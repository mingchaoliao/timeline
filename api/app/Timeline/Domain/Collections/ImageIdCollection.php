<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 9:27 PM
 */

namespace App\Timeline\Domain\Collections;


use App\Timeline\Domain\ValueObjects\ImageId;

class ImageIdCollection extends SingleValueModelCollection
{
    public static function fromValueArray(?array $ids): ?self {
        if($ids === null) {
            return null;
        }

        return new static(array_map(function(int $id) {
            return new ImageId($id);
        },$ids));
    }
}