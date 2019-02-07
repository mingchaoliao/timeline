<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/21/19
 * Time: 6:58 AM
 */

namespace App\Timeline\Domain\Requests;


use App\Timeline\App\Validators\ValidatorFactory;
use App\Timeline\Domain\Models\EventDate;
use App\Timeline\Utils\Common;
use App\Timeline\Utils\JsonSerializable;
use Carbon\Carbon;

class SearchEventRequest implements JsonSerializable
{
    /**
     * @var string|null
     */
    private $content;
    /**
     * @var EventDate|null
     */
    private $startDate;
    /**
     * @var Carbon|null
     */
    private $startDateFrom;
    /**
     * @var Carbon|null
     */
    private $startDateTo;
    /**
     * @var EventDate|null
     */
    private $endDate;
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
     * @param EventDate|null $startDate
     * @param Carbon|null $startDateFrom
     * @param Carbon|null $startDateTo
     * @param EventDate|null $endDate
     * @param Carbon|null $endDateFrom
     * @param Carbon|null $endDateTo
     * @param null|string $period
     * @param array $catalogs
     * @param int $page
     * @param int $pageSize
     */
    public function __construct(?string $content, ?EventDate $startDate, ?Carbon $startDateFrom, ?Carbon $startDateTo, ?EventDate $endDate, ?Carbon $endDateFrom, ?Carbon $endDateTo, ?string $period, array $catalogs, int $page = 1, int $pageSize = 10)
    {
        $this->content = $content;
        $this->startDate = $startDate;
        $this->startDateFrom = $startDateFrom;
        $this->startDateTo = $startDateTo;
        $this->endDate = $endDate;
        $this->endDateFrom = $endDateFrom;
        $this->endDateTo = $endDateTo;
        $this->period = $period;
        $this->catalogs = $catalogs;
        $this->page = $page;
        $this->pageSize = $pageSize;
    }

    /**
     * @param array $data
     * @return SearchEventRequest
     * @throws \App\Timeline\Exceptions\TimelineException
     */
    public static function createFromValueArray(?array $data): ?self
    {
        if ($data === null) {
            return null;
        }

        resolve(ValidatorFactory::class)->validate($data, [
            'content' => 'nullable|string',
            'startDate' => 'nullable|event_date',
            'startDateFrom' => 'nullable|iso_date',
            'startDateTo' => 'nullable|iso_date',
            'endDate' => 'nullable|event_date',
            'endDateFrom' => 'nullable|iso_date',
            'endDateTo' => 'nullable|iso_date',
            'period' => 'nullable|string',
            'catalogs' => 'nullable|string',
            'page' => 'nullable|integer|gt:0',
            'pageSize' => 'nullable|integer|gt:0',

        ]);

        $content = $data['content'] ?? null;
        $startDate = $data['startDate'] ?? null;
        $startDateFrom = Common::createDateFromISOString($data['startDateFrom'] ?? null);
        $startDateTo = Common::createDateFromISOString($data['startDateTo'] ?? null);
        $endDate = $data['endDate'] ?? null;
        $endDateFrom = Common::createDateFromISOString($data['endDateFrom'] ?? null);
        $endDateTo = Common::createDateFromISOString($data['endDateTo'] ?? null);
        $period = $data['period'] ?? null;
        $catalogs = Common::splitByComma($data['catalogs'] ?? null, []);
        $page = $data['page'] ?? 1;
        $pageSize = $data['pageSize'] ?? 10;

        return new static(
            $content,
            EventDate::createFromString($startDate),
            $startDateFrom,
            $startDateTo,
            EventDate::createFromString($endDate),
            $endDateFrom,
            $endDateTo,
            $period,
            $catalogs,
            $page,
            $pageSize
        );
    }

    /**
     * @return null|string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return EventDate|null
     */
    public function getStartDate(): ?EventDate
    {
        return $this->startDate;
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
     * @return EventDate|null
     */
    public function getEndDate(): ?EventDate
    {
        return $this->endDate;
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
     * @return null|string
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

    public function toValueArray(): array
    {
        return [
            'content' => $this->getContent(),
            'startDate' => $this->getStartDate() === null ? null : $this->getStartDate()->getDate(),
            'startDateFrom' => $this->getStartDateFrom() === null ? null : $this->getStartDateFrom()->format('Y-m-d'),
            'startDateTo' => $this->getStartDateTo() === null ? null : $this->getStartDateTo()->format('Y-m-d'),
            'endDate' => $this->getEndDate() === null ? null : $this->getEndDate()->getDate(),
            'endDateFrom' => $this->getEndDateFrom() === null ? null : $this->getEndDateFrom()->format('Y-m-d'),
            'endDateTo' => $this->getEndDateTo() === null ? null : $this->getEndDateTo()->format('Y-m-d'),
            'period' => $this->getPeriod(),
            'catalogs' => $this->getCatalogs(),
            'page' => $this->getPage(),
            'pageSize' => $this->getPageSize(),
        ];
    }
}