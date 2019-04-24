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
     * @var EventDate
     */
    private $startDate;
    /**
     * @var EventDate|null
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
     * @param EventDate $startDate
     * @param EventDate|null $endDate
     * @param DateAttribute|null $startDateAttribute
     * @param DateAttribute|null $endDateAttribute
     * @param Period|null $period
     * @param CatalogCollection $catalogCollection
     * @param string $content
     * @param ImageCollection $imageCollection
     * @param UserId $createUserId
     * @param UserId $updateUserId
     * @param Carbon $createdAt
     * @param Carbon $updatedAt
     */
    public function __construct(EventId $id, EventDate $startDate, ?EventDate $endDate, ?DateAttribute $startDateAttribute, ?DateAttribute $endDateAttribute, ?Period $period, CatalogCollection $catalogCollection, string $content, ImageCollection $imageCollection, UserId $createUserId, UserId $updateUserId, Carbon $createdAt, Carbon $updatedAt)
    {
        $this->id = $id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->startDateAttribute = $startDateAttribute;
        $this->endDateAttribute = $endDateAttribute;
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
     * @return EventDate
     */
    public function getStartDate(): EventDate
    {
        return $this->startDate;
    }

    /**
     * @return EventDate|null
     */
    public function getEndDate(): ?EventDate
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

    public function toValueArray(): array
    {
        $startDateAttribute = $this->getStartDateAttribute();
        if ($startDateAttribute !== null) {
            $startDateAttribute = $startDateAttribute->toValueArray();
        }

        $endDateAttribute = $this->getEndDateAttribute();
        if ($endDateAttribute !== null) {
            $endDateAttribute = $endDateAttribute->toValueArray();
        }

        $period = $this->getPeriod();
        if ($period !== null) {
            $period = $period->toValueArray();
        }

        return [
            'id' => $this->getId()->getValue(),
            'startDate' => (string)$this->getStartDate(),
            'endDate' => $this->getEndDate() === null ? null : (string)$this->getEndDate(),
            'startDateAttribute' => $startDateAttribute,
            'endDateAttribute' => $endDateAttribute,
            'period' => $period,
            'catalogCollection' => $this->getCatalogCollection()->toValueArray(),
            'content' => $this->getContent(),
            'imageCollection' => $this->getImageCollection()->toValueArray(),
            'createUserId' => $this->getCreateUserId()->getValue(),
            'updateUserId' => $this->getUpdateUserId()->getValue(),
            'createdAt' => $this->getCreatedAt()->toIso8601String(),
            'updatedAt' => $this->getUpdatedAt()->toIso8601String(),
        ];
    }

    public function toEsBody(): array
    {
        $body = [
            'id' => $this->getId()->getValue(),
            'startDateStr' => $this->getStartDate()->getDate(),
            'startDateFrom' => $this->getStartDate()->getFrom()->format('Y-m-d'),
            'startDateTo' => $this->getStartDate()->getTo()->format('Y-m-d'),
            'startDateAttribute' => $this->getStartDateAttribute() === null ? null : $this->getStartDateAttribute()->getValue(),
            'endDateStr' => $this->getEndDate() === null ? null : $this->getEndDate()->getDate(),
            'endDateFrom' => $this->getEndDate() === null ? null : $this->getEndDate()->getFrom()->format('Y-m-d'),
            'endDateTo' => $this->getEndDate() === null ? null : $this->getEndDate()->getTo()->format('Y-m-d'),
            'endDateAttribute' => $this->getEndDateAttribute() === null ? null : $this->getEndDateAttribute()->getValue(),
            'period' => $this->getPeriod() === null ? null : $this->getPeriod()->getValue(),
            'catalogs' => $this->getCatalogCollection()
                ->map(function (Catalog $catalog) {
                    return $catalog->getValue();
                })->toArray(),
            'content' => $this->getContent(),
            'images' => $this->getImageCollection()->map(function (Image $image) {
                return $image->getPath();
            })->toArray(),
            'imageDescriptions' => $this->getImageCollection()->map(function (Image $image) {
                return $image->getDescription();
            })->toArray()
        ];

        return $body;
    }

    public function toTimelineArray(): array
    {
        $eventsConfig = [
            'start_date' => $this->getStartDate()->toDateArray(),
            'unique_id' => (string)$this->getId()
        ];

        if ($this->getStartDateAttribute() !== null) {
            $eventsConfig['start_date']['attribute'] = $this->getStartDateAttribute()->getValue();
        }

        if ($this->getEndDate() !== null) {
            $eventsConfig['end_date'] = $this->getEndDate()->toDateArray();

            if ($this->getEndDateAttribute() !== null) {
                $eventsConfig['end_date']['attribute'] = $this->getEndDateAttribute()->getValue();
            }
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
                'url' => url('/') . '/storage/images/' . $images->get(0)->getPath()
            ];
        }

        $period = $this->getPeriod();

//        if ($period !== null) {
//            $eventsConfig['group'] = $period->getValue();
//        }

        return $eventsConfig;
    }
}