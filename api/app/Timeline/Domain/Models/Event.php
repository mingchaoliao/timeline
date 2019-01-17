<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:44 PM
 */

namespace App\Timeline\Domain\Models;


use App\Timeline\Domain\Collections\CatalogCollection;
use App\Timeline\Domain\Collections\ImageCollection;
use App\Timeline\Domain\ValueObjects\EventId;
use App\Timeline\Domain\ValueObjects\UserId;
use Carbon\Carbon;

class Event extends BaseModel
{
    /**
     * @var EventId
     */
    private $id;
    /**
     * @var Carbon
     */
    private $startDate;
    /**
     * @var Carbon|null
     */
    private $endDate;
    /**
     * @var DateAttribute|null
     */
    private $startDateAttribute;
    /**
     * @var DateAttribute|null
     */
    private $endDateAttribute;
    /**
     * @var DateFormat
     */
    private $startDateFormat;
    /**
     * @var DateFormat|null
     */
    private $endDateFormat;
    /**
     * @var Period|null
     */
    private $period;
    /**
     * @var CatalogCollection
     */
    private $catalogCollection;
    /**
     * @var string
     */
    private $content;
    /**
     * @var ImageCollection
     */
    private $imageCollection;
    /**
     * @var UserId
     */
    private $createUserId;
    /**
     * @var UserId
     */
    private $updateUserId;
    /**
     * @var Carbon
     */
    private $createdAt;
    /**
     * @var Carbon
     */
    private $updatedAt;

    /**
     * Event constructor.
     * @param EventId $id
     * @param Carbon $startDate
     * @param Carbon|null $endDate
     * @param DateAttribute|null $startDateAttribute
     * @param DateAttribute|null $endDateAttribute
     * @param DateFormat $startDateFormat
     * @param DateFormat|null $endDateFormat
     * @param Period|null $period
     * @param CatalogCollection $catalogCollection
     * @param string $content
     * @param ImageCollection $imageCollection
     * @param UserId $createUserId
     * @param UserId $updateUserId
     * @param Carbon $createdAt
     * @param Carbon $updatedAt
     */
    public function __construct(EventId $id, Carbon $startDate, ?Carbon $endDate, ?DateAttribute $startDateAttribute, ?DateAttribute $endDateAttribute, DateFormat $startDateFormat, ?DateFormat $endDateFormat, ?Period $period, CatalogCollection $catalogCollection, string $content, ImageCollection $imageCollection, UserId $createUserId, UserId $updateUserId, Carbon $createdAt, Carbon $updatedAt)
    {
        $this->id = $id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->startDateAttribute = $startDateAttribute;
        $this->endDateAttribute = $endDateAttribute;
        $this->startDateFormat = $startDateFormat;
        $this->endDateFormat = $endDateFormat;
        $this->period = $period;
        $this->catalogCollection = $catalogCollection;
        $this->content = $content;
        $this->imageCollection = $imageCollection;
        $this->createUserId = $createUserId;
        $this->updateUserId = $updateUserId;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return EventId
     */
    public function getId(): EventId
    {
        return $this->id;
    }

    /**
     * @return Carbon
     */
    public function getStartDate(): Carbon
    {
        return $this->startDate;
    }

    /**
     * @return Carbon|null
     */
    public function getEndDate(): ?Carbon
    {
        return $this->endDate;
    }

    /**
     * @return DateAttribute|null
     */
    public function getStartDateAttribute(): ?DateAttribute
    {
        return $this->startDateAttribute;
    }

    /**
     * @return DateAttribute|null
     */
    public function getEndDateAttribute(): ?DateAttribute
    {
        return $this->endDateAttribute;
    }

    /**
     * @return DateFormat
     */
    public function getStartDateFormat(): DateFormat
    {
        return $this->startDateFormat;
    }

    /**
     * @return DateFormat|null
     */
    public function getEndDateFormat(): ?DateFormat
    {
        return $this->endDateFormat;
    }

    /**
     * @return Period|null
     */
    public function getPeriod(): ?Period
    {
        return $this->period;
    }

    /**
     * @return CatalogCollection
     */
    public function getCatalogCollection(): CatalogCollection
    {
        return $this->catalogCollection;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return ImageCollection
     */
    public function getImageCollection(): ImageCollection
    {
        return $this->imageCollection;
    }

    /**
     * @return UserId
     */
    public function getCreateUserId(): UserId
    {
        return $this->createUserId;
    }

    /**
     * @return UserId
     */
    public function getUpdateUserId(): UserId
    {
        return $this->updateUserId;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    /**
     * @return Carbon
     */
    public function getUpdatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        $startDateFormat = $this->getStartDateFormat();
        $endDate = $this->getEndDate();
        $endDateFormat = $this->getEndDateFormat();
        if ($endDate !== null) {
            $endDate = $endDate->format($endDateFormat->getPhpFormat());
        }

        $startDateAttribute = $this->getStartDateAttribute();
        if ($startDateAttribute !== null) {
            $startDateAttribute = $startDateAttribute->toArray();
        }

        $endDateAttribute = $this->getEndDateAttribute();
        if ($endDateAttribute !== null) {
            $endDateAttribute = $endDateAttribute->toArray();
        }

        $period = $this->getPeriod();
        if ($period !== null) {
            $period = $period->toArray();
        }

        return [
            'id' => $this->getId()->getValue(),
            'startDate' => $this->getStartDate()->format($startDateFormat->getPhpFormat()),
            'endDate' => $endDate,
            'startDateAttribute' => $startDateAttribute,
            'endDateAttribute' => $endDateAttribute,
            'startDateFormat' => $startDateFormat->toArray(),
            'endDateFormat' => $endDateFormat === null ? null : $endDateFormat->toArray(),
            'period' => $period,
            'catalogCollection' => $this->getCatalogCollection()->toValueArray(),
            'content' => $this->getContent(),
            'imageCollection' => $this->getImageCollection()->toValueArray(),
            'createUserId' => $this->getCreateUserId()->getValue(),
            'updateUserId' => $this->getUpdateUserId()->getValue(),
            'createdAt' => $this->getCreatedAt()->format(DATE_ISO8601),
            'updatedAt' => $this->getUpdatedAt()->format(DATE_ISO8601),
        ];
    }

    public function toEsArray(): array
    {
        $period = $this->getPeriod();
        if ($period !== null) {
            $period = $period->getId();
        }

        $catalogs = $this->getCatalogCollection()
            ->map(function (Catalog $catalog) {
                return $catalog->getId();
            })->toArray();

        $body = [
            'id' => $this->getId()->getValue(),
            'startDate' => $this->getStartDate()->format('Y-m-d'),
            'period' => $period,
            'catalogs' => $catalogs,
            'content' => $this->getContent()
        ];

        return [
            'body' => $body,
            'index' => 'timelines',
            'type' => 'event',
            'id' => $this->getId()->getValue(),
        ];
    }

    public function toTimelineArray(): array
    {
        $startDate = [];
        if ($this->getStartDateFormat()->hasYear()) {
            $startDate['year'] = $this->getStartDate()->year;
        }
        if ($this->getStartDateFormat()->hasMonth()) {
            $startDate['month'] = $this->getStartDate()->month;
        }
        if ($this->getStartDateFormat()->hasDay()) {
            $startDate['day'] = $this->getStartDate()->day;
        }

        $eventsConfig = [
            'start_date' => $startDate,
            'unique_id' => $this->getId()
        ];

        if ($this->getEndDate() !== null) {
            $endDate = [];
            if ($this->getEndDateFormat()->hasYear()) {
                $endDate['year'] = $this->getEndDate()->year;
            }
            if ($this->getEndDateFormat()->hasMonth()) {
                $endDate['month'] = $this->getEndDate()->month;
            }
            if ($this->getEndDateFormat()->hasDay()) {
                $endDate['day'] = $this->getEndDate()->day;
            }
            $eventsConfig['end_date'] = $endDate;
        }

        $text = $this->getContent();
        if ($text !== null) {
//            $len = str_word_count($text);
//            $text = mb_substr($text, 0, min($len, 150), 'UTF-8') . ' ... [Read More]';
            $eventsConfig['text']['text'] = $text;
        }

        $images = $this->getImageCollection();
        if (count($images) !== 0) {
            $eventsConfig['media'] = [
                'url' => url('/') . '/image/' . $images->get(0)->getPath()
            ];
        }

        $period = $this->getPeriod();
        if ($period !== null) {
            $eventsConfig['group'] = $period->getValue();
        }

        return $eventsConfig;
    }
}