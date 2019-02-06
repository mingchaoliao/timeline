<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/6/19
 * Time: 4:04 PM
 */

namespace App\Timeline\Domain\Collections;


use App\Timeline\Domain\ValueObjects\SingleValue;

abstract class BaseSingleValueCollection extends BaseCollection
{
    public function toValueArray(): array
    {
        return $this->map(function (SingleValue $obj) {
            return $obj->getValue();
        })->toArray();
    }
}