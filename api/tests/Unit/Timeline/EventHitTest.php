<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/5/19
 * Time: 3:22 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Models\EventDate;
use App\Timeline\Domain\Models\EventHit;
use App\Timeline\Domain\ValueObjects\EventId;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\Models\EventHit
 */
class EventHitTest extends TestCase
{
    public function testConvertModelToArray()
    {
        $hit = new EventHit(
            new EventId(1),
            EventDate::createFromString('2018'),
            null,
            'attr1',
            null,
            'content'
        );
        $this->assertSame([
            'id' => 1,
            'startDate' => '2018',
            'endDate' => null,
            'startDateAttribute' => 'attr1',
            'endDateAttribute' => null,
            'content' => 'content'
        ], $hit->toArray());
    }
}