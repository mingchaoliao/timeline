<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 11:05 PM
 */

namespace App\Timeline\Exceptions;


use App\Timeline\Domain\ValueObjects\CatalogId;
use App\Timeline\Domain\ValueObjects\DateAttributeId;
use App\Timeline\Domain\ValueObjects\PeriodId;
use App\Timeline\Domain\ValueObjects\UserId;

class TimelineException extends \Exception
{
    public static function ofDuplicatedCatalogValue(string $value): self
    {
        return new static(sprintf(
            'catalog "%s" have already existed',
            $value
        ));
    }

    public static function ofUnknownDatabaseError(): self
    {
        return new static('unknown database error');
    }

    public static function ofUserWithIdDoesNotExist(UserId $id): self
    {
        return new static(sprintf(
            'user with ID "%s" does not exist',
            (string)$id
        ));
    }

    public static function ofCatalogWithIdDoesNotExist(CatalogId $id): self
    {
        return new static(sprintf(
            'catalog with ID "%s" does not exist',
            (string)$id
        ));
    }

    public static function ofUnableToDeleteCatalog(CatalogId $id): self
    {
        return new static(sprintf(
            'unable to delete catalog with ID "%s"',
            (string)$id
        ));
    }

    public static function ofUnableToRetrieveCatalogs(): self
    {
        return new static('unable to retrieve catalogs');
    }

    public static function ofDuplicatedPeriodValue(string $value): self
    {
        return new static(sprintf(
            'period "%s" have already existed',
            $value
        ));
    }

    public static function ofPeriodWithIdDoesNotExist(PeriodId $id): self
    {
        return new static(sprintf(
            'period with ID "%s" does not exist',
            (string)$id
        ));
    }

    public static function ofUnableToDeletePeriod(PeriodId $id): self
    {
        return new static(sprintf(
            'unable to delete period with ID "%s"',
            (string)$id
        ));
    }

    public static function ofUnableToRetrievePeriods(): self
    {
        return new static('unable to retrieve periods');
    }

    public static function ofDuplicatedDateAttributeValue(string $value): self
    {
        return new static(sprintf(
            'date attribute "%s" have already existed',
            $value
        ));
    }

    public static function ofDateAttributeWithIdDoesNotExist(DateAttributeId $id): self
    {
        return new static(sprintf(
            'date attribute with ID "%s" does not exist',
            (string)$id
        ));
    }

    public static function ofUnableToDeleteDateAttribute(DateAttributeId $id): self
    {
        return new static(sprintf(
            'unable to delete date attribute with ID "%s"',
            (string)$id
        ));
    }

    public static function ofUnableToRetrieveDateAttributes(): self
    {
        return new static('unable to retrieve date attributes');
    }

    public static function ofUnableToRetrieveDateFormats(): self
    {
        return new static('unable to retrieve date formats');
    }
}