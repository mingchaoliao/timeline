<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/21/19
 * Time: 12:40 AM
 */

namespace App\Timeline\Domain\Models;


use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Utils\Common;
use Carbon\Carbon;

class EventDate extends BaseModel
{
    /**
     * @var Carbon
     */
    private $date;
    /**
     * @var bool
     */
    private $hasMonth;
    /**
     * @var bool
     */
    private $hasDay;

    /**
     * EventDate constructor.
     * @param Carbon $date
     * @param bool $hasMonth
     * @param bool $hasDay
     * @param bool $isStartDate
     * @throws TimelineException
     */
    public function __construct(Carbon $date, bool $hasMonth = true, bool $hasDay = true, bool $isStartDate = true)
    {
        if (!$hasMonth && $hasDay) {
            throw TimelineException::ofMonthMustBeSetWhenDayIsPresent();
        }

        if ($isStartDate) {
            if (!$hasMonth && !$hasDay) {
                $date->firstOfYear();
            } elseif ($hasMonth && !$hasDay) {
                $date->firstOfMonth();
            }
            $date->setTime(0, 0, 0);
        } else {
            if (!$hasMonth && !$hasDay) {
                $date->lastOfYear();
            } elseif ($hasMonth && !$hasDay) {
                $date->lastOfMonth();
            }
            $date->setTime(23, 59, 59);
        }

        $this->date = $date;
        $this->hasMonth = $hasMonth;
        $this->hasDay = $hasDay;
    }

    /**
     * @param array $data
     * @param bool $isStartDate
     * @return EventDate
     * @throws TimelineException
     */
    public static function createFromArray(array $data, bool $isStartDate = true): self
    {
        $year = $data['year'] ?? null;
        $month = $data['month'] ?? null;
        $day = $data['day'] ?? null;

        if ($year === null || !Common::isPosInt($year)) {
            throw TimelineException::ofInvalidEventDate($year, $month, $day);
        }

        if ($month !== null && !Common::isPosInt($month)) {
            throw TimelineException::ofInvalidEventDate($year, $month, $day);
        }

        if ($day !== null && !Common::isPosInt($day)) {
            throw TimelineException::ofInvalidEventDate($year, $month, $day);
        }

        return static::create($year, $month, $day, $isStartDate);
    }

    /**
     * @param int $year
     * @param int|null $month
     * @param int|null $day
     * @param bool $isStartDate
     * @return EventDate
     * @throws TimelineException
     */
    public static function create(int $year, ?int $month, ?int $day, bool $isStartDate = true): self
    {
        self::validate($year, $month, $day);

        return new static(Carbon::create($year, $month, $day), $month !== null, $day !== null, $isStartDate);
    }

    /**
     * @return Carbon
     */
    public function getDate(): Carbon
    {
        return $this->date;
    }

    /**
     * @return bool
     */
    public function hasMonth(): bool
    {
        return $this->hasMonth;
    }

    /**
     * @return bool
     */
    public function hasDay(): bool
    {
        return $this->hasDay;
    }

    /**
     * @param int $year
     * @param int|null $month
     * @param int|null $day
     * @throws TimelineException
     */
    private static function validate(int $year, ?int $month, ?int $day): void
    {
        try {
            $date = Carbon::create($year, $month, $day);

            if ($month === null && $day !== null) {
                throw TimelineException::ofInvalidEventDate($year, $month, $day);
            }

            if ($month === null && $day === null) {
                if ($date->format('Y') !== strval($year)) {
                    throw TimelineException::ofInvalidEventDate($year, $month, $day);

                }
            }

            if ($month !== null && $day === null) {
                if ($date->format('Y-m') !== sprintf('%s-%02s', strval($year), strval($month))) {
                    throw TimelineException::ofInvalidEventDate($year, $month, $day);
                }
            }

            if ($month !== null && $day !== null) {
                if ($date->format('Y-m-d') !== sprintf('%s-%02s-%02s', strval($year), strval($month), strval($day))) {
                    throw TimelineException::ofInvalidEventDate($year, $month, $day);
                }
            }
        } catch (\InvalidArgumentException $e) {
            throw TimelineException::ofInvalidEventDate($year, $month, $day);
        }
    }

    public function toArray(): array
    {
        return [
            'date' => $this->getDate()->toIso8601String(),
            'hasMonth' => $this->hasMonth(),
            'hasDay' => $this->hasDay()
        ];
    }

    public function toDateArray(): array
    {
        $arr = ['year' => $this->date->year];

        if ($this->hasMonth) {
            $arr['month'] = $this->date->month;
        }

        if ($this->hasDay) {
            $arr['day'] = $this->date->day;
        }

        return $arr;
    }
}