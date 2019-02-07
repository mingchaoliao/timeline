<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:46 PM
 */

namespace App\Timeline\Domain\Collections;


use App\Timeline\Utils\JsonSerializable;
use App\Timeline\Utils\Pageable;
use Illuminate\Support\Collection;

abstract class BaseCollection extends Collection implements JsonSerializable, Pageable
{
    /**
     * @var int|null
     */
    protected $totalCount = null;

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        if($this->totalCount === null) {
            return $this->count();
        }

        return $this->totalCount;
    }

    /**
     * @param int $totalCount
     */
    public function setTotalCount(int $totalCount): void
    {
        $this->totalCount = $totalCount;
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