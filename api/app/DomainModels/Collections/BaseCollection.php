<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:46 PM
 */

namespace App\DomainModels\Collections;


use Illuminate\Support\Collection;

class BaseCollection extends Collection
{
    public function toJson($options = 0)
    {
        return json_encode($this->toValueArray(), $options);
    }

    public function toValueArray(): array {
        try {
            $arr = [];
            foreach ($this->items as $item) {
                $arr[] = $item->toArray();
            }
            return $arr;
        } catch (\Exception $e) {
            return [];
        }
    }
}