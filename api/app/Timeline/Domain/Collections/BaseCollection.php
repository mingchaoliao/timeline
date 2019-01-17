<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:46 PM
 */

namespace App\Timeline\Domain\Collections;


use Illuminate\Support\Collection;

class BaseCollection extends Collection
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