<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/16/19
 * Time: 10:34 PM
 */

namespace App\Timeline\Domain\Requests;

use App\Timeline\Domain\Collections\CatalogIdCollection;
use App\Timeline\Domain\Collections\ImageIdCollection;
use App\Timeline\Domain\ValueObjects\DateAttributeId;
use App\Timeline\Domain\ValueObjects\DateFormatId;
use App\Timeline\Domain\ValueObjects\PeriodId;
use Carbon\Carbon;

class UpdateEventRequest
{
    /**
     * @var Carbon
     */
    private $startDate;
    /**
     * @var DateFormatId
     */
    private $startDateFormatId;
    /**
     * @var DateAttributeId|null
     */
    private $startDateAttributeId;
    /**
     * @var Carbon|null
     */
    private $endDate;
    /**
     * @var DateFormatId|null
     */
    private $endDateFormatId;
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
     * @param Carbon $startDate
     * @param DateFormatId $startDateFormatId
     * @param DateAttributeId|null $startDateAttributeId
     * @param Carbon|null $endDate
     * @param DateFormatId|null $endDateFormatId
     * @param DateAttributeId|null $endDateAttributeId
     * @param string $content
     * @param PeriodId|null $periodId
     * @param CatalogIdCollection $catalogIds
     * @param ImageIdCollection $imageIds
     */
    public function __construct(Carbon $startDate, DateFormatId $startDateFormatId, ?DateAttributeId $startDateAttributeId, ?Carbon $endDate, ?DateFormatId $endDateFormatId, ?DateAttributeId $endDateAttributeId, string $content, ?PeriodId $periodId, CatalogIdCollection $catalogIds, ImageIdCollection $imageIds)
    {
        $this->startDate = $startDate;
        $this->startDateFormatId = $startDateFormatId;
        $this->startDateAttributeId = $startDateAttributeId;
        $this->endDate = $endDate;
        $this->endDateFormatId = $endDateFormatId;
        $this->endDateAttributeId = $endDateAttributeId;
        $this->content = $content;
        $this->periodId = $periodId;
        $this->catalogIds = $catalogIds;
        $this->imageIds = $imageIds;
    }

    /**
     * @return Carbon
     */
    public function getStartDate(): Carbon
    {
        return $this->startDate;
    }

    /**
     * @return DateFormatId
     */
    public function getStartDateFormatId(): DateFormatId
    {
        return $this->startDateFormatId;
    }

    /**
     * @return DateAttributeId|null
     */
    public function getStartDateAttributeId(): ?DateAttributeId
    {
        return $this->startDateAttributeId;
    }

    /**
     * @return Carbon|null
     */
    public function getEndDate(): ?Carbon
    {
        return $this->endDate;
    }

    /**
     * @return DateFormatId|null
     */
    public function getEndDateFormatId(): ?DateFormatId
    {
        return $this->endDateFormatId;
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
}