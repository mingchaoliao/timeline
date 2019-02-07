<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:46 PM
 */

namespace App\Timeline\Domain\Collections;

use App\Timeline\Domain\Requests\CreateEventRequest;

class CreateEventRequestCollection extends BaseCollection
{
    public static function createFromValueArray(?array $data): ?self
    {
        if ($data === null) {
            return null;
        }

        return new static(array_map(function (array $arr) {
            return CreateEventRequest::createFromValueArray($arr);
        }, $data));
    }
}