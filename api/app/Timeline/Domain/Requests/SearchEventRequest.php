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
     * @var PeriodId|null
     */
    private $periodId;
    /**
     * @var CatalogIdCollection
     */
    private $catalogIds;
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
     * @param string|null $content
     * @param Carbon|null $startDateFrom
     * @param Carbon|null $startDateTo
     * @param Carbon|null $endDateFrom
     * @param Carbon|null $endDateTo
     * @param PeriodId|null $periodId
     * @param CatalogIdCollection $catalogIds
     * @param int $page
     * @param int $pageSize
     */
    public function __construct(?string $content, ?Carbon $startDateFrom, ?Carbon $startDateTo, ?Carbon $endDateFrom, ?Carbon $endDateTo, ?PeriodId $periodId, CatalogIdCollection $catalogIds, int $page = 1, int $pageSize = 10)
    {
        $this->content = $content;
        $this->startDateFrom = $startDateFrom;
        $this->startDateTo = $startDateTo;
        $this->endDateFrom = $endDateFrom;
        $this->endDateTo = $endDateTo;
        $this->periodId = $periodId;
        $this->catalogIds = $catalogIds;
        $this->page = $page;
        $this->pageSize = $pageSize;
    }

    public static function createFromArray(array $data): self
    {
        $content = $data['content'] ?? null;
        $startDateFrom = $data['startDateFrom'] ?? null;
        $startDateTo = $data['startDateTo'] ?? null;
        $endDateFrom = $data['endDateFrom'] ?? null;
        $endDateTo = $data['endDateTo'] ?? null;
        $periodId = $data['periodId'] ?? null;
        $catalogIds = $data['catalogIds'] ?? null;
        $page = $data['page'] ?? null;
        $pageSize = $data['pageSize'] ?? null;

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

        if ($periodId !== null) {
            $periodId = new PeriodId($periodId);
        }

        if ($catalogIds !== null) {
            if (!is_array($catalogIds)) {
                throw TimelineException::ofCatalogIdsMustBeAnArray();
            }
            $catalogIds = CatalogIdCollection::fromValueArray($catalogIds);
        }

        return new static(
            $content,
            $startDateFrom,
            $startDateTo,
            $endDateFrom,
            $endDateTo,
            $periodId,
            $catalogIds
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
     * @return PeriodId|null
     */
    public function getPeriodId(): ?PeriodId
    {
        return $this->periodId;
    }

    /**
     * @return CatalogIdCollection
     */
    public function getCatalogIds(): CatalogIdCollection
    {
        return $this->catalogIds;
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