<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/6/19
 * Time: 4:10 PM
 */

namespace App\Timeline\Domain\ValueObjects;

abstract class SingleValue
{
   public abstract static function createFromString(?string $value);
   public abstract function getValue();
}