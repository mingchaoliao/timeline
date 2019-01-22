<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/21/19
 * Time: 3:46 AM
 */

namespace App\Timeline\Utils;


class Common
{
    public static function isInt($var): bool
    {
        if (is_int($var)) {
            return true;
        }

        if (is_string($var)) {
            return strval(intval($var)) === $var;
        }

        return false;
    }

    public static function isPosInt($var): bool
    {
        return self::isInt($var) && intval($var) > 0;
    }
}