<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/6/19
 * Time: 2:45 PM
 */

namespace App\Timeline\Utils;


interface JsonSerializable
{
    public function toValueArray(): array;
}