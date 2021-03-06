<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 9:26 PM
 */

namespace App\Timeline\Domain\ValueObjects;


use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Utils\Common;

final class CatalogId extends SingleInteger
{
    public static function createFromString(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        if (!Common::isPosInt($value)) {
            throw TimelineException::ofInvalidCatalogId($value);
        }

        return new static(intval($value));
    }
}