<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:46 PM
 */

namespace App\Timeline\Domain\Collections;


use App\Timeline\Utils\JsonSerializable;
use Illuminate\Support\Collection;

abstract class BaseCollection extends Collection implements JsonSerializable
{
    /**
     * @var int
     */
    protected $count = 0;

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function toJson($options = 0)
    {
        return json_encode($this->toValueArray(), $options);
    }

    public function toValueArray(): array
    {
        return $this->map(function (JsonSerializable $obj) {
            return $obj->toValueArray();
        })->toArray();
    }
}