<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/7/19
 * Time: 10:29 AM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Collections\CreateEventRequestCollection;
use Tests\TestCase;

/**
 * @covers \App\Timeline\Domain\Collections\CreateEventRequestCollection
 * @covers \App\Timeline\Domain\Collections\BaseCollection
 */
class CreateEventRequestCollectionTest extends TestCase
{
    public function testCreateFromArray()
    {
        $data1 = [
            'startDate' => '2018-01',
            'startDateAttributeId' => null,
            'endDate' => '2019',
            'endDateAttributeId' => 1,
            'content' => 'content',
            'periodId' => 1,
            'catalogIds' => [1, 2],
            'imageIds' => [3, 4]
        ];

        $data2 = [
            'startDate' => '2018',
            'startDateAttributeId' => 1,
            'endDate' => '2019',
            'endDateAttributeId' => 2,
            'content' => 'content2',
            'periodId' => 3,
            'catalogIds' => null,
            'imageIds' => null
        ];

        $collection = CreateEventRequestCollection::createFromValueArray([$data1, $data2]);

        $this->assertSame(2, count($collection));
        $this->assertSame([$data1, [
            'startDate' => '2018',
            'startDateAttributeId' => 1,
            'endDate' => '2019',
            'endDateAttributeId' => 2,
            'content' => 'content2',
            'periodId' => 3,
            'catalogIds' => [],
            'imageIds' => []
        ]], $collection->toValueArray());
    }

    public function testCreateFromNull()
    {
        $this->assertSame(null, CreateEventRequestCollection::createFromValueArray(null));
    }
}