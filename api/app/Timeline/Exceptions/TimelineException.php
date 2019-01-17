<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 11:05 PM
 */

namespace App\Timeline\Exceptions;


use App\Timeline\Domain\Collections\EventIdCollection;
use App\Timeline\Domain\ValueObjects\CatalogId;
use App\Timeline\Domain\ValueObjects\DateAttributeId;
use App\Timeline\Domain\ValueObjects\Email;
use App\Timeline\Domain\ValueObjects\EventId;
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

    public static function ofOauthClientWithNameDoesNotExist(string $name): self
    {
        return new static(sprintf(
            'oauth client with name "%s" does not exist',
            $name
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

    public static function ofInvalidEmail(string $str): self
    {
        return new static(sprintf(
            'email "%s" is invalid',
            $str
        ));
    }

    public static function ofUnauthenticated(): self
    {
        return new static('you haven\'t signed in');
    }

    public static function ofInvalidCredentials(): self
    {
        return new static('credentials are incorrect');
    }

    public static function ofUnauthorizedToUpdateUserPrivilege(): self
    {
        return new static('unauthorized to update user privilege');
    }

    public static function ofUnauthorizedToUpdateUserProfile(): self
    {
        return new static('unauthorized to update user profile');
    }

    public static function ofUnableToRetrieveCurrentUser(): self
    {
        return new static('unable to retrieve current user');
    }

    public static function ofUnableToLogin(): self
    {
        return new static('unable to sign in');
    }

    public static function ofUnableToRetrieveUsers(): self
    {
        return new static('unable to retrieve users');
    }

    public static function ofUnableToRegister(): self
    {
        return new static('unable to register');
    }

    public static function ofUnableToUpdateUserProfile(): self
    {
        return new static('unable to update user profile');
    }

    public static function ofDuplicatedUserEmail(Email $email): self
    {
        return new static(sprintf(
            'email "%s" has already been used',
            (string)$email
        ));
    }

    public static function ofUnAuthorizedToViewOtherUser(): self
    {
        return new static('unauthorized to view other users');
    }

    public static function ofUnauthorizedToCreateCatalog(): self
    {
        return new static('unauthorized to create catalog');
    }

    public static function ofUnauthorizedToUpdateCatalog(CatalogId $id): self
    {
        return new static(sprintf(
            'unauthorized to update catalog with ID "%s"',
            $id
        ));
    }

    public static function ofUnableToCreateCatalog(): self
    {
        return new static('unable to create catalog');
    }

    public static function ofUnableToUpdateCatalog(CatalogId $id): self
    {
        return new static(sprintf(
            'unable to update catalog with ID "%s"',
            (string)$id
        ));
    }

    public static function ofUnauthorizedToCreateDateAttribute(): self
    {
        return new static('unauthorized to create date attribute');
    }

    public static function ofUnauthorizedToUpdateDateAttribute(DateAttributeId $id): self
    {
        return new static(sprintf(
            'unauthorized to update date attribute with ID "%s"',
            $id
        ));
    }

    public static function ofUnableToCreateDateAttribute(): self
    {
        return new static('unable to create date attribute');
    }

    public static function ofUnableToUpdateDateAttribute(DateAttributeId $id): self
    {
        return new static(sprintf(
            'unable to update date attribute with ID "%s"',
            (string)$id
        ));
    }

    public static function ofUnauthorizedToCreatePeriod(): self
    {
        return new static('unauthorized to create period');
    }

    public static function ofUnauthorizedToUpdatePeriod(PeriodId $id): self
    {
        return new static(sprintf(
            'unauthorized to update period with ID "%s"',
            (string)$id
        ));
    }

    public static function ofUnableToCreatePeriod(): self
    {
        return new static('unable to create period');
    }

    public static function ofUnableToUpdatePeriod(PeriodId $id): self
    {
        return new static(sprintf(
            'unable to update period with ID "%s"',
            (string)$id
        ));
    }

    public static function ofInvalidPageNumber(int $page): self
    {
        return new static(sprintf(
            'page number "%s" is invalid',
            (string)$page
        ));
    }

    public static function ofInvalidPageSize(int $pageSize): self
    {
        return new static(sprintf(
            'page size "%s" is invalid',
            (string)$pageSize
        ));
    }

    public static function ofPageSizeTooLarge(int $pageSize): self
    {
        return new static(sprintf(
            'page size "%s" is too large',
            (string)$pageSize
        ));
    }

    public static function ofUnauthorizedToDeletePeriod(PeriodId $id): self
    {
        return new static(sprintf(
            'unauthorized to delete period with ID "%s"',
            (string)$id
        ));
    }

    public static function ofUnauthorizedToDeleteCatalog(CatalogId $id): self
    {
        return new static(sprintf(
            'unauthorized to delete catalog with ID "%s"',
            (string)$id
        ));
    }

    public static function ofUnauthorizedToDeleteDateAttribute(DateAttributeId $id): self
    {
        return new static(sprintf(
            'unauthorized to delete date attribute with ID "%s"',
            (string)$id
        ));
    }

    public static function ofUnauthorizedToDeleteEvent(EventId $id): self
    {
        return new static(sprintf(
            'unauthorized to delete event with ID "%s"',
            (string)$id
        ));
    }

    public static function ofUnableToDeleteEvent(EventId $id): self
    {
        return new static(sprintf(
            'unable to delete event with ID "%s"',
            (string)$id
        ));
    }

    public static function ofUnauthorizedToCreateEvent(): self
    {
        return new static('unauthorized to create event');
    }

    public static function ofUnauthorizedToUpdateEvent(EventId $id): self
    {
        return new static(sprintf(
            'unauthorized to update event with ID "%s"',
            (string)$id
        ));
    }

    public static function ofUnableToCreateEvent(): self
    {
        return new static('unable to create event');
    }

    public static function ofUnableToUpdateEvent(EventId $id): self
    {
        return new static(sprintf(
            'unable to update event with ID "%s"',
            (string)$id
        ));
    }

    public static function ofEventWithIdDoesNotExist(EventId $id): self
    {
        return new static(sprintf(
            'event with ID "%s" does not exist',
            (string)$id
        ));
    }

    public static function ofTemporaryImagePathDoesNotExist(string $path): self
    {
        return new static(sprintf(
            'temporary image file "%s" does not exist',
            $path
        ));
    }

    public static function ofDuplicatedTemporaryImagePath(string $path): self
    {
        return new static(sprintf(
            'temporary image file "%s" have already existed',
            $path
        ));
    }

    public static function ofUnableToRetrieveEventById(EventId $id): self
    {
        return new static(sprintf(
            'unable to retrieve event with ID "%s"',
            (string)$id
        ));
    }

    public static function ofUnableToRetrieveEvents(): self
    {
        return new static('unable to retrieve events');
    }

    public static function ofStartDateIsRequired()
    {
    }

    public static function ofStartDateFormatIdIsRequired()
    {
    }
}