<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/25/19
 * Time: 9:37 PM
 */

namespace App\Timeline\Domain\Models;


use App\Timeline\Domain\ValueObjects\EventId;

class EventHit extends BaseModel
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
     * @var string|null
     */
    private $startDateAttribute;
    /**
     * @var string|null
     */
    private $endDateAttribute;
    /**
     * @var string
     */
    private $content;
    /**
     * @var string|null
     */
    private $highlight;
    /**
     * @var string|null
     */
    private $period;
    /**
     * @var array
     */
    private $catalogs;
    /**
     * @var array
     */
    private $images;

    /**
     * EventHit constructor.
     * @param EventId $id
     * @param EventDate $startDate
     * @param EventDate|null $endDate
     * @param string|null $startDateAttribute
     * @param string|null $endDateAttribute
     * @param string $content
     * @param string|null $highlight
     * @param string|null $period
     * @param array $catalogs
     * @param array $images
     */
    public function __construct(EventId $id, EventDate $startDate, ?EventDate $endDate, ?string $startDateAttribute, ?string $endDateAttribute, string $content, ?string $highlight, ?string $period, array $catalogs, array $images)
    {
        $this->id = $id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->startDateAttribute = $startDateAttribute;
        $this->endDateAttribute = $endDateAttribute;
        $this->content = $content;
        $this->highlight = $highlight;
        $this->period = $period;
        $this->catalogs = $catalogs;
        $this->images = $images;
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
     * @return null|string
     */
    public function getStartDateAttribute(): ?string
    {
        return $this->startDateAttribute;
    }

    /**
     * @return null|string
     */
    public function getEndDateAttribute(): ?string
    {
        return $this->endDateAttribute;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getHighlight(): ?string
    {
        return $this->highlight;
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
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    public function toValueArray(): array
    {
        return [
            'id' => $this->getId()->getValue(),
            'startDate' => (string)$this->getStartDate(),
            'endDate' => $this->getEndDate() === null ? null : (string)$this->getEndDate(),
            'startDateAttribute' => $this->getStartDateAttribute(),
            'endDateAttribute' => $this->getEndDateAttribute(),
            'highlight' => $this->getHighlight(),
            'content' => $this->getContent(),
            'period' => $this->getPeriod(),
            'catalogs' => $this->getCatalogs(),
            'images' => $this->getImages()
        ];
    }
}