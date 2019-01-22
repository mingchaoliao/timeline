<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 11:46 PM
 */

namespace App\Timeline\Domain\ValueObjects;


use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Utils\Common;

final class PeriodId extends SingleInteger
{
    /**
     * @param string $value
     * @return PeriodId
     * @throws TimelineException
     */
    public static function createFromString(string $value): self {
        if(!Common::isPosInt($value)) {
            throw TimelineException::ofInvalidPeriodId($value);
        }

        return new static(intval($value));
    }
}