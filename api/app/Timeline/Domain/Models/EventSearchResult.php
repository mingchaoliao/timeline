<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/25/19
 * Time: 9:46 PM
 */

namespace App\Timeline\Domain\Models;


use App\Timeline\Domain\Collections\BucketCollection;
use App\Timeline\Domain\Collections\EventHitCollection;

class EventSearchResult extends BaseModel
{
    /**
     * @var EventHitCollection
     */
    private $hits;
    /**
     * @var BucketCollection
     */
    private $periodBuckets;
    /**
     * @var BucketCollection
     */
    private $catalogBuckets;
    /**
     * @var BucketCollection
     */
    private $dateBuckets;

    /**
     * EventSearchResult constructor.
     * @param EventHitCollection $hits
     * @param BucketCollection $periodBuckets
     * @param BucketCollection $catalogBuckets
     * @param BucketCollection $dateBuckets
     */
    public function __construct(EventHitCollection $hits, BucketCollection $periodBuckets, BucketCollection $catalogBuckets, BucketCollection $dateBuckets)
    {
        $this->hits = $hits;
        $this->periodBuckets = $periodBuckets;
        $this->catalogBuckets = $catalogBuckets;
        $this->dateBuckets = $dateBuckets;
    }

    /**
     * @return EventHitCollection
     */
    public function getHits(): EventHitCollection
    {
        return $this->hits;
    }

    /**
     * @return BucketCollection
     */
    public function getPeriodBuckets(): BucketCollection
    {
        return $this->periodBuckets;
    }

    /**
     * @return BucketCollection
     */
    public function getCatalogBuckets(): BucketCollection
    {
        return $this->catalogBuckets;
    }

    /**
     * @return BucketCollection
     */
    public function getDateBuckets(): BucketCollection
    {
        return $this->dateBuckets;
    }


    public function toArray(): array
    {
        return [
            'hits' => $this->getHits()->toValueArray(),
            'periods' => $this->getPeriodBuckets()->toValueArray(),
            'catalogs' => $this->getCatalogBuckets()->toValueArray(),
            'dates' => $this->getDateBuckets()->toValueArray()
        ];
    }
}