<?php
/**
 * Author: liaom
 * Date: 6/22/18
 * Time: 3:01 PM
 */

namespace App\Timeline\Domain\Models;


use App\Timeline\Utils\JsonSerializable;
use Illuminate\Contracts\Support\Jsonable;

abstract class BaseModel implements JsonSerializable, Jsonable
{
    public function toJson($options = 0)
    {
        return json_encode($this->toValueArray(), $options);
    }
}