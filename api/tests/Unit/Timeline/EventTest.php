<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/5/19
 * Time: 1:28 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Collections\CatalogCollection;
use App\Timeline\Domain\Collections\ImageCollection;
use App\Timeline\Domain\Models\Catalog;
use App\Timeline\Domain\Models\DateAttribute;
use App\Timeline\Domain\Models\Event;
use App\Timeline\Domain\Models\EventDate;
use App\Timeline\Domain\Models\Image;
use App\Timeline\Domain\Models\Period;
use App\Timeline\Domain\ValueObjects\CatalogId;
use App\Timeline\Domain\ValueObjects\DateAttributeId;
use App\Timeline\Domain\ValueObjects\EventId;
use App\Timeline\Domain\ValueObjects\ImageId;
use App\Timeline\Domain\ValueObjects\PeriodId;
use App\Timeline\Domain\ValueObjects\UserId;
use Carbon\Carbon;
use Tests\TestCase;

/**
 * @covers \App\Timeline\Domain\Models\Event
 */
class EventTest extends TestCase
{
    /**
     * @var Event
     */
    private $event1;
    /**
     * @var Carbon
     */
    private $date;

    protected function setUp()
    {
        parent::setUp();
        $this->date = Carbon::create(2018, 1, 1, 0, 0, 0);
        $this->event1 = new Event(
            new EventId(1),
            EventDate::createFromString('2018'),
            EventDate::createFromString('2018-03'),
            new DateAttribute(
                new DateAttributeId(1),
                'attr1',
                new UserId(1),
                'user1',
                new UserId(1),
                'user1',
                $this->date,
                $this->date
            ),
            new DateAttribute(
                new DateAttributeId(2),
                'attr2',
                new UserId(1),
                'user1',
                new UserId(1),
                'user1',
                $this->date,
                $this->date
            ),
            new Period(
                new PeriodId(1),
                'period1',
                1,
                new UserId(1),
                'user1',
                new UserId(1),
                'user1',
                $this->date,
                $this->date
            ),
            new CatalogCollection([
                new Catalog(
                    new CatalogId(1),
                    'catalog1',
                    1,
                    new UserId(1),
                    'user1',
                    new UserId(1),
                    'user1',
                    $this->date,
                    $this->date
                ),
                new Catalog(
                    new CatalogId(2),
                    'catalog2',
                    1,
                    new UserId(1),
                    'user1',
                    new UserId(1),
                    'user1',
                    $this->date,
                    $this->date
                )
            ]),
            'content',
            new ImageCollection([
                new Image(
                    new ImageId(1),
                    '20190120-asdf.jpg',
                    'desc1',
                    'img1.jpg',
                    new EventId(1),
                    new UserId(1),
                    new UserId(1),
                    $this->date,
                    $this->date
                )
            ]),
            new UserId(1),
            new UserId(1),
            $this->date,
            $this->date
        );
    }

    public function testConvertModelToArray()
    {
        $this->assertSame([
            'id' => 1,
            'startDate' => '2018',
            'endDate' => '2018-03',
            'startDateAttribute' => [
                'id' => 1,
                'value' => 'attr1',
                'createUserId' => 1,
                'createUserName' => 'user1',
                'updateUserId' => 1,
                'updateUserName' => 'user1',
                'createdAt' => $this->date->toIso8601String(),
                'updatedAt' => $this->date->toIso8601String()
            ],
            'endDateAttribute' => [
                'id' => 2,
                'value' => 'attr2',
                'createUserId' => 1,
                'createUserName' => 'user1',
                'updateUserId' => 1,
                'updateUserName' => 'user1',
                'createdAt' => $this->date->toIso8601String(),
                'updatedAt' => $this->date->toIso8601String()
            ],
            'period' => [
                'id' => 1,
                'value' => 'period1',
                'numberOfEvents' => 1,
                'createUserId' => 1,
                'createUserName' => 'user1',
                'updateUserId' => 1,
                'updateUserName' => 'user1',
                'createdAt' => $this->date->toIso8601String(),
                'updatedAt' => $this->date->toIso8601String()
            ],
            'catalogCollection' => [
                [
                    'id' => 1,
                    'value' => 'catalog1',
                    'numberOfEvents' => 1,
                    'createUserId' => 1,
                    'createUserName' => 'user1',
                    'updateUserId' => 1,
                    'updateUserName' => 'user1',
                    'createdAt' => $this->date->toIso8601String(),
                    'updatedAt' => $this->date->toIso8601String()
                ],
                [
                    'id' => 2,
                    'value' => 'catalog2',
                    'numberOfEvents' => 1,
                    'createUserId' => 1,
                    'createUserName' => 'user1',
                    'updateUserId' => 1,
                    'updateUserName' => 'user1',
                    'createdAt' => $this->date->toIso8601String(),
                    'updatedAt' => $this->date->toIso8601String()
                ]
            ],
            'content' => 'content',
            'imageCollection' => [
                [
                    'id' => 1,
                    'path' => '20190120-asdf.jpg',
                    'description' => 'desc1',
                    'originalName' => 'img1.jpg',
                    'eventId' => 1,
                    'createUserId' => 1,
                    'updateUserId' => 1,
                    'createdAt' => $this->date->toIso8601String(),
                    'updatedAt' => $this->date->toIso8601String()
                ]
            ],
            'createUserId' => 1,
            'updateUserId' => 1,
            'createdAt' => $this->date->toIso8601String(),
            'updatedAt' => $this->date->toIso8601String(),
        ], $this->event1->toValueArray());
    }

    public function testGetDataForIndex()
    {
        $this->assertSame([
            'id' => 1,
            'startDateStr' => '2018',
            'startDateFrom' => '2018-01-01',
            'startDateTo' => '2018-12-31',
            'startDateAttribute' => 'attr1',
            'endDateStr' => '2018-03',
            'endDateFrom' => '2018-03-01',
            'endDateTo' => '2018-03-31',
            'endDateAttribute' => 'attr2',
            'period' => 'period1',
            'catalogs' => [
                'catalog1',
                'catalog2'
            ],
            'content' => 'content'
        ], $this->event1->toEsBody());
    }

    public function testGetTimelineData()
    {
        $this->assertSame([
            'start_date' => [
                'year' => 2018
            ],
            'unique_id' => 1,
            'end_date' => [
                'year' => 2018,
                'month' => 3
            ],
            'text' => [
                'text' => 'content'
            ],
            'media' => [
                'url' => 'https://test/storage/images/20190120-asdf.jpg'
            ],
            'group' => 'period1'
        ], $this->event1->toTimelineArray());
    }
}