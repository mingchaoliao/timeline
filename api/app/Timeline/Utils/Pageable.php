<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/7/19
 * Time: 10:13 AM
 */

namespace App\Timeline\Utils;


interface Pageable
{
    public function getTotalCount(): int;
}