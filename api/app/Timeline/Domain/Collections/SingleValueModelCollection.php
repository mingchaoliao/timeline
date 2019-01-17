<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/16/19
 * Time: 11:06 PM
 */

namespace App\Timeline\Domain\Collections;

class SingleValueModelCollection extends BaseCollection
{
    public function toValueArray(): array
    {
        return $this->map(function($obj) {
            return $obj->getValue();
        })->toArray();
    }
}