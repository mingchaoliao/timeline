<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 9:15 PM
 */

namespace App\Timeline\Domain\Repositories;


use App\Timeline\Domain\Collections\DateFormatCollection;

interface DateFormatRepository
{
    public function getAll(): DateFormatCollection;
}