<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/21/19
 * Time: 12:40 AM
 */

namespace App\Timeline\Domain\Models;

use App\Timeline\Exceptions\TimelineException;
use Carbon\Carbon;

class EventDate
{
    private const FORMAT_YEAR = 'Y';
    private const FORMAT_YEAR_MONTH = 'Y-m';
    private const FORMAT_YEAR_MONTH_DAY = 'Y-m-d';
    private const REGEX_YEAR = '/^([0-9]{4})$/';
    private const REGEX_YEAR_MONTH = '/^([0-9]{4})-(0[1-9]|1[0-2])$/';
    private const REGEX_YEAR_MONTH_DAY = '/^([0-9]{4})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/';

    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $format;

    /**
     * EventDate constructor.
     * @param string $date
     * @throws TimelineException
     */
    public function __construct(string $date)
    {
        $this->validate($date);
        $this->date = $date;
    }

    /**
     * @param null|string $str
     * @return EventDate|null
     * @throws TimelineException
     */
    public static function createFromString(?string $str): ?self
    {
        if ($str === null) {
            return null;
        }

        return new static($str);
    }

    public function toStartDate(): Carbon
    {
        $date = Carbon::createFromFormat($this->format, $this->date);
        switch ($this->format) {
            case self::FORMAT_YEAR:
                $date->startOfYear();
                break;
            case self::FORMAT_YEAR_MONTH:
                $date->startOfMonth();
                break;
            case self::FORMAT_YEAR_MONTH_DAY:
                $date->startOfDay();
                break;
        }
        return $date;
    }

    public function toEndDate(): Carbon
    {
        $date = Carbon::createFromFormat($this->format, $this->date);
        switch ($this->format) {
            case self::FORMAT_YEAR:
                $date->endOfYear();
                break;
            case self::FORMAT_YEAR_MONTH:
                $date->endOfMonth();
                break;
            case self::FORMAT_YEAR_MONTH_DAY:
                $date->endOfMonth();
                break;
        }
        return $date;
    }

    /**
     * @param string $date
     * @throws TimelineException
     */
    private function validate(string $date): void
    {
        if (preg_match(self::REGEX_YEAR, $date)) {
            $this->format = self::FORMAT_YEAR;
        } elseif (preg_match(self::REGEX_YEAR_MONTH, $date)) {
            $this->format = self::FORMAT_YEAR_MONTH;
        } elseif (preg_match(self::REGEX_YEAR_MONTH_DAY, $date)) {
            $this->format = self::FORMAT_YEAR_MONTH_DAY;
        }

        throw TimelineException::ofInvalidDateString($date);
    }

    /**
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    public function __toString()
    {
        return $this->getDate();
    }

    public function toDateArray(): array
    {
        $date = Carbon::createFromFormat($this->format, $this->date);

        $arr = [
            'year' => $date->year
        ];

        if ($this->format === self::FORMAT_YEAR_MONTH_DAY) {
            $arr['month'] = $date->month;
            $arr['day'] = $date->day;
        } elseif ($this->format === self::FORMAT_YEAR_MONTH) {
            $arr['month'] = $date->month;
        }

        return $arr;
    }

    public function isAttributeAllowed(): bool
    {
        switch ($this->format) {
            case self::FORMAT_YEAR:
                return true;
            case self::FORMAT_YEAR_MONTH:
                return false;
            case self::FORMAT_YEAR_MONTH_DAY:
                return false;
        }
        return false;
    }
}