<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/7/19
 * Time: 11:39 AM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Collections\EventCollection;
use App\Timeline\Domain\Models\Event;
use App\Timeline\Domain\Requests\SearchEventRequest;
use App\Timeline\Domain\ValueObjects\EventId;
use App\Timeline\Infrastructure\Elasticsearch\SearchEventRepository;
use App\Timeline\Infrastructure\Elasticsearch\SearchParamsBuilder;
use Elasticsearch\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Infrastructure\Elasticsearch\SearchEventRepository
 */
class ESSearchEventRepositoryTest extends TestCase
{
    /**
     * @var SearchEventRepository
     */
    private $searchEventRepo;
    /**
     * @var MockObject
     */
    private $paramsBuilder;
    /**
     * @var MockObject
     */
    private $es;

    protected function setUp()
    {
        $this->es = $this->createMock(Client::class);
        $this->paramsBuilder = $this->createMock(SearchParamsBuilder::class);
        $this->searchEventRepo = new SearchEventRepository($this->es, $this->paramsBuilder);
    }

    public function testDeleteDoc()
    {
        $eventId = new EventId(3);
        $this->es->method('delete')
            ->with($this->equalTo([
                'index' => 'timelines',
                'type' => 'event',
                'id' => 3
            ]))
            ->willReturn(null);
        $this->searchEventRepo->deleteDocument($eventId);
        $this->assertTrue(true);
    }

    public function testIndexDoc()
    {
        $event = $this->createMock(Event::class);
        $eventId = new EventId(1);
        $event->method('toEsBody')->willReturn(['attr1' => 1]);
        $event->method('getId')->willReturn($eventId);
        $this->es->method('index')
            ->with($this->equalTo([
                'body' => [
                    'attr1' => 1
                ],
                'index' => 'timelines',
                'type' => 'event',
                'id' => 1,
            ]))
            ->willReturn(null);
        $this->searchEventRepo->index($event);
        $this->assertTrue(true);
    }

    public function testBulkIndexDocs()
    {
        $event1 = $this->createMock(Event::class);
        $eventId1 = new EventId(1);
        $event1->method('toEsBody')->willReturn(['attr1' => 1]);
        $event1->method('getId')->willReturn($eventId1);

        $event2 = $this->createMock(Event::class);
        $eventId2 = new EventId(2);
        $event2->method('toEsBody')->willReturn(['attr1' => 2]);
        $event2->method('getId')->willReturn($eventId2);

        $this->es->method('index')
            ->with($this->equalTo([
                'body' => [
                    [
                        'index' => [
                            '_index' => 'timelines',
                            '_type' => 'event',
                            '_id' => 1
                        ]
                    ],
                    [
                        'attr1' => 1
                    ],
                    [
                        'index' => [
                            '_index' => 'timelines',
                            '_type' => 'event',
                            '_id' => 2
                        ]
                    ],
                    [
                        'attr1' => 2
                    ]
                ]
            ]))
            ->willReturn(null);
        $this->searchEventRepo->bulkIndex(new EventCollection([$event1, $event2]));
        $this->searchEventRepo->bulkIndex(new EventCollection([]));

        $this->assertTrue(true);
    }

    public function testSearchEventWithoutContent()
    {
        $searchRequest = $this->createMock(SearchEventRequest::class);
        $this->paramsBuilder->method('getParams')->willReturn([]);
        $this->es->method('search')->willReturn([
            'hits' => [
                'total' => 10,
                'hits' => [
                    [
                        '_id' => 1,
                        '_source' => [
                            'id' => 1,
                            'startDateStr' => '2018-01',
                            'startDateFrom' => '2018-01-01',
                            'startDateTo' => '2018-01-31',
                            'startDateAttribute' => null,
                            'endDateStr' => '2019',
                            'endDateFrom' => '2019-01-01',
                            'endDateTo' => '2019-12-31',
                            'endDateAttribute' => 'attr1',
                            'period' => 'period1',
                            'catalogs' => [],
                            'content' => '12345678',
                        ]
                    ],
                    [
                        '_id' => 2,
                        '_source' => [
                            'id' => 2,
                            'startDateStr' => '2018-01',
                            'startDateFrom' => '2018-01-01',
                            'startDateTo' => '2018-01-31',
                            'startDateAttribute' => null,
                            'endDateStr' => '2019',
                            'endDateFrom' => '2019-01-01',
                            'endDateTo' => '2019-12-31',
                            'endDateAttribute' => 'attr2',
                            'period' => 'period2',
                            'catalogs' => ['catalogs1', 'catalogs2'],
                            'content' => '123456789',
                        ]
                    ]
                ]
            ],
            'aggregations' => [
                'period' => [
                    'buckets' => [
                        [
                            'key' => 'period1',
                            'doc_count' => 1
                        ],
                        [
                            'key' => 'period2',
                            'doc_count' => 1
                        ]
                    ]
                ],
                'catalogs' => [
                    'buckets' => [
                        [
                            'key' => 'catalog1',
                            'doc_count' => 3
                        ]
                    ]
                ],
                'startDate' => [
                    'buckets' => [
                        [
                            'key_as_string' => '2018',
                            'doc_count' => 3
                        ],
                        [
                            'key_as_string' => '2018',
                            'doc_count' => 0
                        ]
                    ]
                ]
            ]
        ]);

        $result = $this->searchEventRepo->search($searchRequest);
        $this->assertSame(10, $result->getHits()->getTotalCount());
        $this->assertSame([
            'hits' => [
                [
                    'id' => 1,
                    'startDate' => '2018-01',
                    'endDate' => '2019',
                    'startDateAttribute' => null,
                    'endDateAttribute' => 'attr1',
                    'content' => '1234 ...'
                ],
                [
                    'id' => 2,
                    'startDate' => '2018-01',
                    'endDate' => '2019',
                    'startDateAttribute' => null,
                    'endDateAttribute' => 'attr2',
                    'content' => '1234 ...'
                ]
            ],
            'periods' => [
                [
                    'value' => 'period1',
                    'count' => 1
                ],
                [
                    'value' => 'period2',
                    'count' => 1
                ]
            ],
            'catalogs' => [
                [
                    'value' => 'catalog1',
                    'count' => 3
                ]
            ],
            'dates' => [
                [
                    'value' => '2018',
                    'count' => 3
                ]
            ]
        ], $result->toValueArray());
    }

    public function testSearchEventWithContent()
    {
        $searchRequest = $this->createMock(SearchEventRequest::class);
        $this->paramsBuilder->method('getParams')->willReturn([]);
        $this->es->method('search')->willReturn([
            'hits' => [
                'total' => 10,
                'hits' => [
                    [
                        '_id' => 1,
                        '_source' => [
                            'id' => 1,
                            'startDateStr' => '2018-01',
                            'startDateFrom' => '2018-01-01',
                            'startDateTo' => '2018-01-31',
                            'startDateAttribute' => null,
                            'endDateStr' => '2019',
                            'endDateFrom' => '2019-01-01',
                            'endDateTo' => '2019-12-31',
                            'endDateAttribute' => 'attr1',
                            'period' => 'period1',
                            'catalogs' => [],
                            'content' => '12345678',
                        ],
                        'highlight' => [
                            'content' => [
                                '<em>1234</em>5',
                                '<em>8</em>'
                            ]
                        ]
                    ]
                ]
            ],
            'aggregations' => [
                'period' => [
                    'buckets' => []
                ],
                'catalogs' => [
                    'buckets' => []
                ],
                'startDate' => [
                    'buckets' => []
                ]
            ]
        ]);

        $result = $this->searchEventRepo->search($searchRequest);
        $this->assertSame([
            'hits' => [
                [
                    'id' => 1,
                    'startDate' => '2018-01',
                    'endDate' => '2019',
                    'startDateAttribute' => null,
                    'endDateAttribute' => 'attr1',
                    'content' => '<em>1234</em>5 ... <em>8</em> ...'
                ]
            ],
            'periods' => [],
            'catalogs' => [],
            'dates' => []
        ], $result->toValueArray());
    }
}