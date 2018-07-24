<?php
/**
 * Author: liaom
 * Date: 6/22/18
 * Time: 3:01 PM
 */

namespace App\DomainModels;


use Illuminate\Contracts\Support\Jsonable;

abstract class AbstractBase implements Jsonable
{
    abstract public function toArray(): array;

    public function toJson($options = 0)
    {
        return json_encode($this->toArray(),$options);
    }
}