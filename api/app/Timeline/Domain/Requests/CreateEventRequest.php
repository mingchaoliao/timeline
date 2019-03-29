<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/16/19
 * Time: 10:34 PM
 */

namespace App\Timeline\Domain\Requests;

use App\Timeline\App\Validators\ValidatorFactory;
use App\Timeline\Domain\Collections\CatalogIdCollection;
use App\Timeline\Domain\Collections\ImageIdCollection;
use App\Timeline\Domain\Models\EventDate;
use App\Timeline\Domain\ValueObjects\DateAttributeId;
use App\Timeline\Domain\ValueObjects\PeriodId;
use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Utils\JsonSerializable;

class CreateEventRequest implements JsonSerializable
{
    /**
     * @var EventDate
     */
    private $startDate;
    /**
     * @var DateAttributeId|null
     */
    private $startDateAttributeId;
    /**
     * @var EventDate|null
     */
    private $endDate;
    /**
     * @var DateAttributeId|null
     */
    private $endDateAttributeId;
    /**
     * @var string
     */
    private $content;
    /**
     * @var PeriodId|null
     */
    private $periodId;
    /**
     * @var CatalogIdCollection
     */
    private $catalogIds;
    /**
     * @var ImageIdCollection
     */
    private $imageIds;

    /**
     * CreateEventRequest constructor.
     * @param EventDate $startDate
     * @param DateAttributeId|null $startDateAttributeId
     * @param EventDate|null $endDate
     * @param DateAttributeId|null $endDateAttributeId
     * @param string $content
     * @param PeriodId|null $periodId
     * @param CatalogIdCollection $catalogIds
     * @param ImageIdCollection $imageIds
     */
    public function __construct(EventDate $startDate, ?DateAttributeId $startDateAttributeId, ?EventDate $endDate, ?DateAttributeId $endDateAttributeId, string $content, ?PeriodId $periodId, CatalogIdCollection $catalogIds, ImageIdCollection $imageIds)
    {
        $this->startDate = $startDate;
        $this->startDateAttributeId = $startDateAttributeId;
        $this->endDate = $endDate;
        $this->endDateAttributeId = $endDateAttributeId;
        $this->content = $content;
        $this->periodId = $periodId;
        $this->catalogIds = $catalogIds;
        $this->imageIds = $imageIds;
    }

    /**
     * @param array $data
     * @return CreateEventRequest
     * @throws TimelineException
     */
    public static function createFromValueArray(?array $data): ?self
    {
        if($data === null) {
            return null;
        }

        resolve(ValidatorFactory::class)->validate($data, [
            'startDate' => 'required|event_date',
            'startDateAttributeId' => 'nullable|date_attribute:startDate|id',
            'endDate' => 'nullable|event_date|after:startDate',
            'endDateAttributeId' => 'nullable|date_attribute:endDate|id',
            'content' => 'required|string',
            'periodId' => 'nullable|id',
            'catalogIds' => 'nullable|array',
            'catalogIds.*' => 'id',
            'imageIds' => 'nullable|array',
            'imageIds.*' => 'id',
        ]);

        $startDate = EventDate::createFromString($data['startDate']);
        $startDateAttributeId = DateAttributeId::createFromString($data['startDateAttributeId'] ?? null);
        $endDate = EventDate::createFromString($data['endDate'] ?? null);
        $endDateAttributeId = DateAttributeId::createFromString($data['endDateAttributeId'] ?? null);
        $periodId = PeriodId::createFromString($data['periodId'] ?? null);
        $catalogIds = CatalogIdCollection::createFromValueArray(
            $data['catalogIds'] ?? []
        );
        $content = $data['content'];
        $imageIds = ImageIdCollection::createFromArray(
            $data['imageIds'] ?? []
        );

        return new static(
            $startDate,
            $startDateAttributeId,
            $endDate,
            $endDateAttributeId,
            $content,
            $periodId,
            $catalogIds,
            $imageIds
        );
    }

    /**
     * @return EventDate
     */
    public function getStartDate(): EventDate
    {
        return $this->startDate;
    }

    /**
     * @return DateAttributeId|null
     */
    public function getStartDateAttributeId(): ?DateAttributeId
    {
        return $this->startDateAttributeId;
    }

    /**
     * @return EventDate|null
     */
    public function getEndDate(): ?EventDate
    {
        return $this->endDate;
    }

    /**
     * @return DateAttributeId|null
     */
    public function getEndDateAttributeId(): ?DateAttributeId
    {
        return $this->endDateAttributeId;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
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
     * @return ImageIdCollection
     */
    public function getImageIds(): ImageIdCollection
    {
        return $this->imageIds;
    }

    public function toValueArray(): array
    {
        return [
            'startDate' => $this->getStartDate()->getDate(),
            'startDateAttributeId' => $this->getStartDateAttributeId() === null ? null : $this->getStartDateAttributeId()->getValue(),
            'endDate' => $this->getEndDate() === null ? null : $this->getEndDate()->getDate(),
            'endDateAttributeId' => $this->getEndDateAttributeId() === null ? null : $this->getEndDateAttributeId()->getValue(),
            'content' => $this->getContent(),
            'periodId' => $this->getPeriodId() === null ? null : $this->getPeriodId()->getValue(),
            'catalogIds' => $this->getCatalogIds()->toValueArray(),
            'imageIds' => $this->getImageIds()->toValueArray(),
        ];
    }
}