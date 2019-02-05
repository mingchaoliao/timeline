<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/5/19
 * Time: 3:28 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Collections\BucketCollection;
use App\Timeline\Domain\Collections\EventHitCollection;
use App\Timeline\Domain\Models\Bucket;
use App\Timeline\Domain\Models\EventHit;
use App\Timeline\Domain\Models\EventSearchResult;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\Models\EventSearchResult
 */
class EventSearchResultTest extends TestCase
{
    public function testConvertModelToArray() {
        $hit1 = $this->createMock(EventHit::class);
        $hit2 = $this->createMock(EventHit::class);
        $periodBucket1 = $this->createMock(Bucket::class);
        $periodBucket2 = $this->createMock(Bucket::class);
        $catalogBucket1 = $this->createMock(Bucket::class);
        $catalogBucket2 = $this->createMock(Bucket::class);
        $dateBucket1 = $this->createMock(Bucket::class);
        $dateBucket2 = $this->createMock(Bucket::class);

        $hit1->method('toArray')->willReturn([1]);
        $hit2->method('toArray')->willReturn([2]);
        $periodBucket1->method('toArray')->willReturn([3]);
        $periodBucket2->method('toArray')->willReturn([4]);
        $catalogBucket1->method('toArray')->willReturn([5]);
        $catalogBucket2->method('toArray')->willReturn([6]);
        $dateBucket1->method('toArray')->willReturn([7]);
        $dateBucket2->method('toArray')->willReturn([8]);

        $result = new EventSearchResult(
            new EventHitCollection([$hit1, $hit2]),
            new BucketCollection([$periodBucket1, $periodBucket2]),
            new BucketCollection([$catalogBucket1, $catalogBucket2]),
            new BucketCollection([$dateBucket1, $dateBucket2])
        );

        $this->assertSame([
            'hits' => [
                [1],
                [2]
            ],
            'periods' => [
                [3],
                [4]
            ],
            'catalogs' => [
                [5],
                [6]
            ],
            'dates' => [
                [7],
                [8]
            ],
        ], $result->toArray());
    }
}