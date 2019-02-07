<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/7/19
 * Time: 10:20 AM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Collections\EventCollection;
use App\Timeline\Domain\Models\Event;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\Collections\EventCollection
 * @covers \App\Timeline\Domain\Collections\BaseCollection
 */
class EventCollectionTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $event1;
    /**
     * @var MockObject
     */
    private $event2;

    protected function setUp()
    {
        $this->event1 = $this->createMock(Event::class);
        $this->event2 = $this->createMock(Event::class);

        $this->event1->method('toValueArray')->willReturn([
            'attr1' => 1,
            'attr2' => 2
        ]);

        $this->event1->method('toTimelineArray')->willReturn([
            'attr1' => '1',
            'attr2' => '2'
        ]);

        $this->event2->method('toValueArray')->willReturn([
            'attr1' => 3,
            'attr2' => 4
        ]);

        $this->event2->method('toTimelineArray')->willReturn([
            'attr1' => '3',
            'attr2' => '4'
        ]);
    }

    public function testGetValueArray()
    {
        $collection = new EventCollection([$this->event1, $this->event2]);
        $this->assertSame(2, count($collection));
        $this->assertSame(2, $collection->getTotalCount());
        $this->assertSame([
            [
                'attr1' => 1,
                'attr2' => 2
            ],
            [
                'attr1' => 3,
                'attr2' => 4
            ]
        ], $collection->toValueArray());
        $this->assertSame('[{"attr1":1,"attr2":2},{"attr1":3,"attr2":4}]', $collection->toJson());
    }

    public function testPageableTotalCount()
    {
        $collection = new EventCollection([$this->event1, $this->event2]);
        $collection->setTotalCount(10);
        $this->assertSame(2, count($collection));
        $this->assertSame(10, $collection->getTotalCount());
    }

    public function testGetTimelineArray()
    {
        $collection = new EventCollection([$this->event1, $this->event2]);
        $this->assertSame([
            [
                'attr1' => '1',
                'attr2' => '2'
            ],
            [
                'attr1' => '3',
                'attr2' => '4'
            ]
        ], $collection->toTimelineArray());
    }
}