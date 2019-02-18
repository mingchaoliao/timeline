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
use App\Timeline\Domain\ValueObjects\Email;
use App\Timeline\Domain\ValueObjects\EventId;
use App\Timeline\Domain\ValueObjects\ImageId;
use App\Timeline\Domain\ValueObjects\PeriodId;
use App\Timeline\Domain\ValueObjects\UserId;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class TimelineException extends \Exception implements HttpExceptionInterface
{
    public const DUPLICATED_CATALOG_VALUE = 10000;
    public const USER_WITH_ID_DOES_NOT_EXIST = 10002;
    public const CATALOG_WITH_ID_DOES_NOT_EXIST = 10004;
    public const UNABLE_TO_DELETE_CATALOG = 10005;
    public const UNABLE_TO_RETRIEVE_CATALOGS = 10006;
    public const DUPLICATED_PERIOD_VALUE = 10007;
    public const PERIOD_WITH_ID_DOES_NOT_EXIST = 10008;
    public const UNABLE_TO_DELETE_PERIOD = 10009;
    public const UNABLE_TO_RETRIEVE_PERIODS = 10010;
    public const DUPLICATED_DATE_ATTRIBUTE_VALUE = 10011;
    public const DATE_ATTRIBUTE_WITH_ID_DOES_NOT_EXIST = 10012;
    public const UNABLE_TO_DELETE_DATE_ATTRIBUTE = 10013;
    public const UNABLE_TO_RETRIEVE_DATE_ATTRIBUTES = 10014;
    public const INVALID_EMAIL = 10016;
    public const UNAUTHENTICATED = 10017;
    public const INVALID_CREDENTIALS = 10018;
    public const UNAUTHORIZED_TO_UPDATE_USER_PRIVILEGE = 10019;
    public const UNAUTHORIZED_TO_UPDATE_USER_PROFILE = 10020;
    public const UNABLE_TO_RETRIEVE_CURRENT_USER = 10021;
    public const UNABLE_TO_LOGIN = 10022;
    public const UNABLE_TO_RETRIEVE_USERS = 10023;
    public const UNABLE_TO_REGISTER = 10024;
    public const UNABLE_TO_UPDATE_USER_PROFILE = 10025;
    public const DUPLICATED_USER_EMAIL = 10026;
    public const UN_AUTHORIZED_TO_VIEW_OTHER_USER = 10027;
    public const UNAUTHORIZED_TO_CREATE_CATALOG = 10028;
    public const UNAUTHORIZED_TO_UPDATE_CATALOG = 10029;
    public const UNABLE_TO_CREATE_CATALOG = 10030;
    public const UNABLE_TO_UPDATE_CATALOG = 10031;
    public const UNAUTHORIZED_TO_CREATE_DATE_ATTRIBUTE = 10032;
    public const UNAUTHORIZED_TO_UPDATE_DATE_ATTRIBUTE = 10033;
    public const UNABLE_TO_CREATE_DATE_ATTRIBUTE = 10034;
    public const UNABLE_TO_UPDATE_DATE_ATTRIBUTE = 10035;
    public const UNAUTHORIZED_TO_CREATE_PERIOD = 10036;
    public const UNAUTHORIZED_TO_UPDATE_PERIOD = 10037;
    public const UNABLE_TO_CREATE_PERIOD = 10038;
    public const UNABLE_TO_UPDATE_PERIOD = 10039;
    public const PAGE_NUMBER_TOO_SMALL = 10040;
    public const PAGE_SIZE_TOO_SMALL = 10041;
    public const PAGE_SIZE_TOO_LARGE = 10042;
    public const UNAUTHORIZED_TO_DELETE_PERIOD = 10043;
    public const UNAUTHORIZED_TO_DELETE_CATALOG = 10044;
    public const UNAUTHORIZED_TO_DELETE_DATE_ATTRIBUTE = 10045;
    public const UNAUTHORIZED_TO_DELETE_EVENT = 10046;
    public const UNABLE_TO_DELETE_EVENT = 10047;
    public const UNAUTHORIZED_TO_CREATE_EVENT = 10048;
    public const UNAUTHORIZED_TO_UPDATE_EVENT = 10049;
    public const UNABLE_TO_CREATE_EVENT = 10050;
    public const UNABLE_TO_UPDATE_EVENT = 10051;
    public const EVENT_WITH_ID_DOES_NOT_EXIST = 10052;
    public const TEMPORARY_IMAGE_PATH_DOES_NOT_EXIST = 10053;
    public const DUPLICATED_TEMPORARY_IMAGE_PATH = 10054;
    public const UNABLE_TO_RETRIEVE_EVENT_BY_ID = 10055;
    public const UNABLE_TO_RETRIEVE_EVENTS = 10056;
    public const START_DATE_IS_REQUIRED = 10057;
    public const MONTH_MUST_BE_SET_WHEN_DAY_IS_PRESENT = 10059;
    public const START_DATE_ATTRIBUTE_SHOULD_NOT_BE_SET = 10060;
    public const END_DATE_ATTRIBUTE_SHOULD_NOT_BE_SET = 10061;
    public const INVALID_EVENT_ID = 10062;
    public const CATALOG_DOES_NOT_EXIST = 10063;
    public const CATALOG_IDS_MUST_BE_AN_ARRAY = 10064;
    public const INVALID_END_DATE_TO = 10065;
    public const INVALID_END_DATE_FROM = 10065;
    public const INVALID_START_DATE_TO = 10066;
    public const INVALID_START_DATE_FROM = 10067;
    public const UNABLE_TO_SEARCH_EVENTS = 10068;
    public const INVALID_PERIOD_ID = 10069;
    public const INVALID_CATALOG_ID = 10070;
    public const INVALID_DATE_ATTRIBUTE_ID = 10071;
    public const INVALID_USER_ID = 10072;
    public const IMAGE_WITH_ID_DOES_NOT_EXIST = 10073;
    public const INVALID_IMAGE_ID = 10074;
    public const INVALID_DATE_STRING = 10075;
    public const INVALID_INTEGER = 10076;
    public const OLD_PASSWORD_IS_NOT_CORRECT = 10077;
    public const USER_WITH_EMAIL_DOES_NOT_FOUND = 10078;
    public const USER_ACCOUNT_IS_LOCKED = 10079;
    public const FAILED_TO_BACKUP = 10080;
    public const BACKUP_FILE_WITH_NAME_DOES_NOT_EXIST = 10081;
    public const UNAUTHORIZED_TO_DOWNLOAD_BACKUP_FILE = 10082;

    /**
     * @var int
     */
    private $statusCode;
    /**
     * @var array
     */
    private $headers;

    public function __construct(string $message = "", int $code = 0, int $statusCode = 500, array $headers = [], Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        if (env('APP_DEBUG') === true && $previous !== null) {
            $this->message = sprintf(
                '%s (Reason: %s)',
                $message,
                $previous->getMessage()
            );
        }

        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Returns the status code.
     *
     * @return int An HTTP response status code
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Returns response headers.
     *
     * @return array Response headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    public static function ofDuplicatedCatalogValue(string $value, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'catalog "%s" have already existed',
            $value
        ), static::DUPLICATED_CATALOG_VALUE, 400, [], $previous);
    }

    public static function ofUserWithIdDoesNotExist(UserId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'user with ID "%s" does not exist',
            (string)$id
        ), static::USER_WITH_ID_DOES_NOT_EXIST, 404, [], $previous);
    }

    public static function ofCatalogWithIdDoesNotExist(CatalogId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'catalog with ID "%s" does not exist',
            (string)$id
        ), static::CATALOG_WITH_ID_DOES_NOT_EXIST, 404, [], $previous);
    }

    public static function ofUnableToDeleteCatalog(CatalogId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unable to delete catalog with ID "%s"',
            (string)$id
        ), static::UNABLE_TO_DELETE_CATALOG, 500, [], $previous);
    }

    public static function ofUnableToRetrieveCatalogs(\Throwable $previous = null): self
    {
        return new static(
            'unable to retrieve catalogs',
            static::UNABLE_TO_RETRIEVE_CATALOGS,
            500,
            [],
            $previous
        );
    }

    public static function ofDuplicatedPeriodValue(string $value, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'period "%s" have already existed',
            $value
        ), static::DUPLICATED_PERIOD_VALUE, 400, [], $previous);
    }

    public static function ofPeriodWithIdDoesNotExist(PeriodId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'period with ID "%s" does not exist',
            (string)$id
        ), static::PERIOD_WITH_ID_DOES_NOT_EXIST, 404, [], $previous);
    }

    public static function ofUnableToDeletePeriod(PeriodId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unable to delete period with ID "%s"',
            (string)$id
        ), static::UNABLE_TO_DELETE_PERIOD, 500, [], $previous);
    }

    public static function ofUnableToRetrievePeriods(\Throwable $previous = null): self
    {
        return new static('unable to retrieve periods', 500, [], self::UNABLE_TO_RETRIEVE_PERIODS, $previous);
    }

    public static function ofDuplicatedDateAttributeValue(string $value, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'date attribute "%s" have already existed',
            $value
        ), static::DUPLICATED_DATE_ATTRIBUTE_VALUE, 400, [], $previous);
    }

    public static function ofDateAttributeWithIdDoesNotExist(DateAttributeId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'date attribute with ID "%s" does not exist',
            (string)$id
        ), static::DATE_ATTRIBUTE_WITH_ID_DOES_NOT_EXIST, 404, [], $previous);
    }

    public static function ofUnableToDeleteDateAttribute(DateAttributeId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unable to delete date attribute with ID "%s"',
            (string)$id
        ), static::UNABLE_TO_DELETE_DATE_ATTRIBUTE, 500, [], $previous);
    }

    public static function ofUnableToRetrieveDateAttributes(\Throwable $previous = null): self
    {
        return new static('unable to retrieve date attributes', 500, [], self::UNABLE_TO_RETRIEVE_DATE_ATTRIBUTES, $previous);
    }

    public static function ofInvalidEmail(string $str, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'email "%s" is invalid',
            $str
        ), static::INVALID_EMAIL, 400, [], $previous);
    }

    public static function ofUnauthenticated(\Throwable $previous = null): self
    {
        return new static('you haven\'t signed in', static::UNAUTHENTICATED, 401, [], $previous);
    }

    public static function ofInvalidCredentials(\Throwable $previous = null): self
    {
        return new static('credentials are incorrect', static::INVALID_CREDENTIALS, 401, [], $previous);
    }

    public static function ofUnauthorizedToUpdateUserPrivilege(\Throwable $previous = null): self
    {
        return new static('unauthorized to update user privilege', self::UNAUTHORIZED_TO_UPDATE_USER_PRIVILEGE, 401, [], $previous);
    }

    public static function ofUnauthorizedToUpdateUserProfile(\Throwable $previous = null): self
    {
        return new static('unauthorized to update user profile', self::UNAUTHORIZED_TO_UPDATE_USER_PROFILE, 401, [], $previous);
    }

    public static function ofUnableToRetrieveCurrentUser(\Throwable $previous = null): self
    {
        return new static('unable to retrieve current user', static::UNABLE_TO_RETRIEVE_CURRENT_USER, 500, [], $previous);
    }

    public static function ofUnableToLogin(\Throwable $previous = null): self
    {
        return new static('unable to sign in', static::UNABLE_TO_LOGIN, 500, [], $previous);
    }

    public static function ofUnableToRetrieveUsers(\Throwable $previous = null): self
    {
        return new static('unable to retrieve users', self::UNABLE_TO_RETRIEVE_USERS, 500, [], $previous);
    }

    public static function ofUnableToRegister(\Throwable $previous = null): self
    {
        return new static('unable to register', self::UNABLE_TO_REGISTER, 500, [], $previous);
    }

    public static function ofUnableToUpdateUserProfile(\Throwable $previous = null): self
    {
        return new static('unable to update user profile', self::UNABLE_TO_UPDATE_USER_PROFILE, 500, [], $previous);
    }

    public static function ofDuplicatedUserEmail(Email $email, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'email "%s" has already been used',
            (string)$email
        ), self::DUPLICATED_USER_EMAIL, 400, [], $previous);
    }

    public static function ofUnAuthorizedToViewOtherUser(\Throwable $previous = null): self
    {
        return new static('unauthorized to view other users', self::UN_AUTHORIZED_TO_VIEW_OTHER_USER, 401, [], $previous);
    }

    public static function ofUnauthorizedToCreateCatalog(\Throwable $previous = null): self
    {
        return new static('unauthorized to create catalog', self::UNAUTHORIZED_TO_CREATE_CATALOG, 401, [], $previous);
    }

    public static function ofUnauthorizedToUpdateCatalog(CatalogId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unauthorized to update catalog with ID "%s"',
            $id
        ), self::UNAUTHORIZED_TO_UPDATE_CATALOG, 401, [], $previous);
    }

    public static function ofUnableToCreateCatalog(\Throwable $previous = null): self
    {
        return new static('unable to create catalog', self::UNABLE_TO_CREATE_CATALOG, 500, [], $previous);
    }

    public static function ofUnableToUpdateCatalog(CatalogId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unable to update catalog with ID "%s"',
            (string)$id
        ), self::UNABLE_TO_UPDATE_CATALOG, 500, [], $previous);
    }

    public static function ofUnauthorizedToCreateDateAttribute(\Throwable $previous = null): self
    {
        return new static('unauthorized to create date attribute', self::UNAUTHORIZED_TO_CREATE_DATE_ATTRIBUTE, 401, [], $previous);
    }

    public static function ofUnauthorizedToUpdateDateAttribute(DateAttributeId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unauthorized to update date attribute with ID "%s"',
            $id
        ), self::UNAUTHORIZED_TO_UPDATE_DATE_ATTRIBUTE, 401, [], $previous);
    }

    public static function ofUnableToCreateDateAttribute(\Throwable $previous = null): self
    {
        return new static('unable to create date attribute', self::UNABLE_TO_CREATE_DATE_ATTRIBUTE, 500, [], $previous);
    }

    public static function ofUnableToUpdateDateAttribute(DateAttributeId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unable to update date attribute with ID "%s"',
            (string)$id
        ), self::UNABLE_TO_UPDATE_DATE_ATTRIBUTE, 500, [], $previous);
    }

    public static function ofUnauthorizedToCreatePeriod(\Throwable $previous = null): self
    {
        return new static('unauthorized to create period', static::UNAUTHORIZED_TO_CREATE_PERIOD, 401, [], $previous);
    }

    public static function ofUnauthorizedToUpdatePeriod(PeriodId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unauthorized to update period with ID "%s"',
            (string)$id
        ), static::UNAUTHORIZED_TO_UPDATE_PERIOD, 401, [], $previous);
    }

    public static function ofUnableToCreatePeriod(\Throwable $previous = null): self
    {
        return new static('unable to create period', static::UNABLE_TO_CREATE_PERIOD, 500, [], $previous);
    }

    public static function ofUnableToUpdatePeriod(PeriodId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unable to update period with ID "%s"',
            (string)$id
        ), static::UNABLE_TO_UPDATE_PERIOD, 500, [], $previous);
    }

    public static function ofPageNumberTooSmall(int $page, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'page number "%d" is too small',
            $page
        ), static::PAGE_NUMBER_TOO_SMALL, 400, [], $previous);
    }

    public static function ofPageSizeTooSmall(int $pageSize, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'page size "%d" is too small',
            $pageSize
        ), static::PAGE_SIZE_TOO_SMALL, 400, [], $previous);
    }

    public static function ofPageSizeTooLarge(int $pageSize, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'page size "%s" is too large',
            (string)$pageSize
        ), static::PAGE_SIZE_TOO_LARGE, 400, [], $previous);
    }

    public static function ofUnauthorizedToDeletePeriod(PeriodId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unauthorized to delete period with ID "%s"',
            (string)$id
        ), static::UNAUTHORIZED_TO_DELETE_PERIOD, 401, [], $previous);
    }

    public static function ofUnauthorizedToDeleteCatalog(CatalogId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unauthorized to delete catalog with ID "%s"',
            (string)$id
        ), static::UNAUTHORIZED_TO_DELETE_CATALOG, 401, [], $previous);
    }

    public static function ofUnauthorizedToDeleteDateAttribute(DateAttributeId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unauthorized to delete date attribute with ID "%s"',
            (string)$id
        ), static::UNAUTHORIZED_TO_DELETE_DATE_ATTRIBUTE, 401, [], $previous);
    }

    public static function ofUnauthorizedToDeleteEvent(EventId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unauthorized to delete event with ID "%s"',
            (string)$id
        ), static::UNAUTHORIZED_TO_DELETE_EVENT, 401, [], $previous);
    }

    public static function ofUnableToDeleteEvent(EventId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unable to delete event with ID "%s"',
            (string)$id
        ), static::UNABLE_TO_DELETE_EVENT, 500, [], $previous);
    }

    public static function ofUnauthorizedToCreateEvent(\Throwable $previous = null): self
    {
        return new static('unauthorized to create event', static::UNAUTHORIZED_TO_CREATE_EVENT, 401, [], $previous);
    }

    public static function ofUnauthorizedToUpdateEvent(EventId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unauthorized to update event with ID "%s"',
            (string)$id
        ), self::UNAUTHORIZED_TO_UPDATE_EVENT, 401, [], $previous);
    }

    public static function ofUnableToCreateEvent(\Throwable $previous = null): self
    {
        return new static('unable to create event', static::UNABLE_TO_CREATE_EVENT, 500, [], $previous);
    }

    public static function ofUnableToUpdateEvent(EventId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unable to update event with ID "%s"',
            (string)$id
        ), static::UNABLE_TO_UPDATE_EVENT, 500, [], $previous);
    }

    public static function ofEventWithIdDoesNotExist(EventId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'event with ID "%s" does not exist',
            (string)$id
        ), static::EVENT_WITH_ID_DOES_NOT_EXIST, 404, [], $previous);
    }

    public static function ofTemporaryImagePathDoesNotExist(string $path, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'temporary image file "%s" does not exist',
            $path
        ), static::TEMPORARY_IMAGE_PATH_DOES_NOT_EXIST, 404, [], $previous);
    }

    public static function ofDuplicatedTemporaryImagePath(string $path, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'temporary image file "%s" have already existed',
            $path
        ), static::DUPLICATED_TEMPORARY_IMAGE_PATH, 404, [], $previous);
    }

    public static function ofUnableToRetrieveEventById(EventId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unable to retrieve event with ID "%s"',
            (string)$id
        ), self::UNABLE_TO_RETRIEVE_EVENT_BY_ID, 500, [], $previous);
    }

    public static function ofUnableToRetrieveEvents(\Throwable $previous = null): self
    {
        return new static('unable to retrieve events', static::UNABLE_TO_RETRIEVE_EVENTS, 500, [], $previous);
    }

    public static function ofStartDateAttributeShouldNotBeSet(\Throwable $previous = null): self
    {
        return new static('event start date attribute should not be set when month/day is present', static::START_DATE_ATTRIBUTE_SHOULD_NOT_BE_SET, 400, [], $previous);
    }

    public static function ofEndDateAttributeShouldNotBeSet(\Throwable $previous = null): self
    {
        return new static('event end date attribute should not be set when month/day is present', static::END_DATE_ATTRIBUTE_SHOULD_NOT_BE_SET, 400, [], $previous);
    }

    public static function ofInvalidEventId(string $value, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'invalid event id "%s", must be an positive integer',
            $value
        ), static::INVALID_EVENT_ID, 400, [], $previous);
    }

    public static function ofCatalogDoesNotExist(\Throwable $previous = null): self
    {
        return new static('catalog does not exist', self::CATALOG_DOES_NOT_EXIST, 404, [], $previous);
    }

    public static function ofCatalogIdsMustBeAnArray(\Throwable $previous = null): self
    {
        return new static(
            'request parameter "catalogIds" must be an array',
            static::CATALOG_IDS_MUST_BE_AN_ARRAY,
            400,
            [],
            $previous
        );
    }

    public static function ofInvalidEndDateTo(\Throwable $previous = null): self
    {
        return new static(
            'invalid request parameter "endDateTo"',
            static::INVALID_END_DATE_TO,
            400,
            [],
            $previous
        );
    }

    public static function ofInvalidEndDateFrom(\Throwable $previous = null): self
    {
        return new static(
            'invalid request parameter "endDateFrom"',
            static::INVALID_END_DATE_FROM,
            400,
            [],
            $previous
        );
    }

    public static function ofInvalidStartDateTo(\Throwable $previous = null): self
    {
        return new static(
            'invalid request parameter "startDateTo"',
            static::INVALID_START_DATE_TO,
            400,
            [],
            $previous
        );
    }

    public static function ofInvalidStartDateFrom(\Throwable $previous = null): self
    {
        return new static(
            'invalid request parameter "startDateFrom"',
            static::INVALID_START_DATE_FROM,
            400,
            [],
            $previous
        );
    }

    public static function ofUnableToSearchEvents(\Throwable $previous = null): self
    {
        return new static(
            'unable to search events',
            static::UNABLE_TO_SEARCH_EVENTS,
            500,
            [],
            $previous
        );
    }

    public static function ofInvalidPeriodId(string $value, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'invalid period id "%s", must be an positive integer',
            $value
        ), static::INVALID_PERIOD_ID, 400, [], $previous);
    }

    public static function ofInvalidDateAttributeId(string $value, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'invalid date attribute id "%s", must be an positive integer',
            $value
        ), static::INVALID_DATE_ATTRIBUTE_ID, 400, [], $previous);
    }

    public static function ofInvalidCatalogId(string $value, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'invalid catalog id "%s", must be an positive integer',
            $value
        ), static::INVALID_CATALOG_ID, 400, [], $previous);
    }

    public static function ofInvalidUserId(string $value, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'invalid user id "%s", must be an positive integer',
            $value
        ), static::INVALID_USER_ID, 400, [], $previous);
    }

    public static function ofInvalidImageId($value, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'invalid image id "%s", must be an positive integer',
            $value
        ), static::INVALID_IMAGE_ID, 400, [], $previous);
    }

    public static function ofImageWithIdDoesNotExist(ImageId $id, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'image with id "%s" does not exist',
            (string)$id
        ), static::IMAGE_WITH_ID_DOES_NOT_EXIST, 404, [], $previous);
    }

    public static function ofInvalidDateString(string $str, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'date string "%s" is invalid',
            $str
        ), static::INVALID_DATE_STRING, 400, [], $previous);
    }

    public static function ofInvalidInteger(\Throwable $previous = null): self
    {
        return new static('value must be an integer', static::INVALID_INTEGER, 400, [], $previous);
    }

    public static function ofOldPasswordIsNotCorrect(\Throwable $previous = null): self
    {
        return new static('old password does not match with our record', static::OLD_PASSWORD_IS_NOT_CORRECT, 400, [], $previous);
    }

    public static function ofUserWithEmailDoesNotFound(Email $email, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'user account does not found: "%s"',
            (string)$email
        ), static::USER_WITH_EMAIL_DOES_NOT_FOUND, 400, [], $previous);
    }

    public static function ofUserAccountIsLocked(\Throwable $previous = null): self
    {
        return new static(
            'user account has been inactivated, please contact system administrator.'
            , static::USER_ACCOUNT_IS_LOCKED, 400, [], $previous);
    }

    public static function ofFailedToBackup(\Throwable $previous = null): self
    {
        return new static(
            'failed to backup application.'
            , static::FAILED_TO_BACKUP, 500, [], $previous);
    }

    public static function ofBackupFileWithNameDoesNotExist(string $name, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'backup file with name "%s" does not exist',
            $name
        ), static::BACKUP_FILE_WITH_NAME_DOES_NOT_EXIST, 404, [], $previous);
    }

    public static function ofUnableToDeleteBackupWithName(string $name, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unable to delete bakcup file with name "%s"',
            $name
        ), static::BACKUP_FILE_WITH_NAME_DOES_NOT_EXIST, 404, [], $previous);
    }

    public static function ofUnauthorizedToDownloadBackupFile(string $name, \Throwable $previous = null): self
    {
        return new static(sprintf(
            'unauthorized to download backup file "%s"',
            $name
        ), static::UNAUTHORIZED_TO_DOWNLOAD_BACKUP_FILE, 403, [], $previous);
    }
}