<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/21/19
 * Time: 3:46 AM
 */

namespace App\Timeline\Utils;


use Carbon\Carbon;

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

    public static function splitByComma(?string $str): ?array
    {
        if ($str === null) {
            return null;
        }

        return explode(',', $str);
    }

    public static function createDateFromISOString(?string $str): ?Carbon
    {
        if ($str === null) {
            return null;
        }

        return Carbon::createFromFormat('Y-m-d', $str);
    }
}