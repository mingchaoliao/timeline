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
     * EventSearchResult constructor.
     * @param EventId $id
     * @param EventDate $startDate
     * @param EventDate|null $endDate
     * @param null|string $startDateAttribute
     * @param null|string $endDateAttribute
     * @param string $content
     */
    public function __construct(EventId $id, EventDate $startDate, ?EventDate $endDate, ?string $startDateAttribute, ?string $endDateAttribute, string $content)
    {
        $this->id = $id;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->startDateAttribute = $startDateAttribute;
        $this->endDateAttribute = $endDateAttribute;
        $this->content = $content;
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

    public function toValueArray(): array
    {
        return [
            'id' => $this->getId()->getValue(),
            'startDate' => (string)$this->getStartDate(),
            'endDate' => $this->getEndDate() === null ? null : (string)$this->getEndDate(),
            'startDateAttribute' => $this->getStartDateAttribute(),
            'endDateAttribute' => $this->getEndDateAttribute(),
            'content' => $this->getContent()
        ];
    }
}