<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/21/19
 * Time: 6:58 AM
 */

namespace App\Timeline\Domain\Requests;


use App\Timeline\Domain\Collections\CatalogIdCollection;
use App\Timeline\Domain\ValueObjects\PeriodId;
use App\Timeline\Exceptions\TimelineException;
use Carbon\Carbon;

class SearchEventRequest
{
    private const FORMAT = 'Y-m-d';

    /**
     * @var string|null
     */
    private $content;
    /**
     * @var bool
     */
    private $isFuzzy;
    /**
     * @var Carbon|null
     */
    private $startDateFrom;
    /**
     * @var Carbon|null
     */
    private $startDateTo;
    /**
     * @var Carbon|null
     */
    private $endDateFrom;
    /**
     * @var Carbon|null
     */
    private $endDateTo;
    /**
     * @var string|null
     */
    private $period;
    /**
     * @var array
     */
    private $catalogs;
    /**
     * @var int
     */
    private $page;
    /**
     * @var int
     */
    private $pageSize;

    /**
     * SearchEventRequest constructor.
     * @param null|string $content
     * @param bool $isFuzzy
     * @param Carbon|null $startDateFrom
     * @param Carbon|null $startDateTo
     * @param Carbon|null $endDateFrom
     * @param Carbon|null $endDateTo
     * @param string|null $period
     * @param array $catalogs
     * @param int $page
     * @param int $pageSize
     */
    public function __construct(?string $content, bool $isFuzzy, ?Carbon $startDateFrom, ?Carbon $startDateTo, ?Carbon $endDateFrom, ?Carbon $endDateTo, ?string $period, array $catalogs, int $page, int $pageSize)
    {
        $this->content = $content;
        $this->isFuzzy = $isFuzzy;
        $this->startDateFrom = $startDateFrom;
        $this->startDateTo = $startDateTo;
        $this->endDateFrom = $endDateFrom;
        $this->endDateTo = $endDateTo;
        $this->period = $period;
        $this->catalogs = $catalogs;
        $this->page = $page;
        $this->pageSize = $pageSize;
    }

    public static function createFromArray(array $data): self
    {
        $content = $data['content'] ?? null;
        $isFuzzy = $data['isFuzzy'] ?? true;
        $startDateFrom = $data['startDateFrom'] ?? null;
        $startDateTo = $data['startDateTo'] ?? null;
        $endDateFrom = $data['endDateFrom'] ?? null;
        $endDateTo = $data['endDateTo'] ?? null;
        $period = $data['period'] ?? null;
        $catalogs = $data['catalogs'] ?? [];
        $page = $data['page'] ?? 1;
        $pageSize = $data['pageSize'] ?? 10;

        if ($isFuzzy === 'true') {
            $isFuzzy = true;
        } elseif ($isFuzzy === 'false') {
            $isFuzzy = false;
        }

        try {
            if ($startDateFrom !== null) {
                $startDateFrom = Carbon::createFromFormat(static::FORMAT, $startDateFrom);
            }
        } catch (\InvalidArgumentException $e) {
            throw TimelineException::ofInvalidStartDateFrom($e);
        }

        try {
            if ($startDateTo !== null) {
                $startDateTo = Carbon::createFromFormat(static::FORMAT, $startDateTo);
            }
        } catch (\InvalidArgumentException $e) {
            throw TimelineException::ofInvalidStartDateTo($e);
        }

        try {
            if ($endDateFrom !== null) {
                $endDateFrom = Carbon::createFromFormat(static::FORMAT, $endDateFrom);
            }
        } catch (\InvalidArgumentException $e) {
            throw TimelineException::ofInvalidEndDateFrom($e);
        }

        try {
            if ($endDateTo !== null) {
                $endDateTo = Carbon::createFromFormat(static::FORMAT, $endDateTo);
            }
        } catch (\InvalidArgumentException $e) {
            throw TimelineException::ofInvalidEndDateTo($e);
        }

        return new static(
            $content,
            $isFuzzy,
            $startDateFrom,
            $startDateTo,
            $endDateFrom,
            $endDateTo,
            $period,
            $catalogs,
            $page,
            $pageSize
        );
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function isFuzzy(): bool
    {
        return $this->isFuzzy;
    }

    /**
     * @return Carbon|null
     */
    public function getStartDateFrom(): ?Carbon
    {
        return $this->startDateFrom;
    }

    /**
     * @return Carbon|null
     */
    public function getStartDateTo(): ?Carbon
    {
        return $this->startDateTo;
    }

    /**
     * @return Carbon|null
     */
    public function getEndDateFrom(): ?Carbon
    {
        return $this->endDateFrom;
    }

    /**
     * @return Carbon|null
     */
    public function getEndDateTo(): ?Carbon
    {
        return $this->endDateTo;
    }

    /**
     * @return string|null
     */
    public function getPeriod(): ?string
    {
        return $this->period;
    }

    /**
     * @return array
     */
    public function getCatalogs(): array
    {
        return $this->catalogs;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getPageSize(): int
    {
        return $this->pageSize;
    }
}