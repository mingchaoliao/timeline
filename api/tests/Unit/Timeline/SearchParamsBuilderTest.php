<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/7/19
 * Time: 6:39 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Models\EventDate;
use App\Timeline\Domain\Requests\SearchEventRequest;
use App\Timeline\Infrastructure\Elasticsearch\SearchParamsBuilder;
use Carbon\Carbon;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Infrastructure\Elasticsearch\SearchParamsBuilder
 */
class SearchParamsBuilderTest extends TestCase
{
    /**
     * @var SearchParamsBuilder
     */
    private $builder;

    protected function setUp()
    {
        $this->builder = new SearchParamsBuilder();
    }

    public function testBuildParamsWithDate()
    {
        $searchRequest = $this->createRequest([
            'startDate' => [
                'from' => '2018-01-01',
                'to' => '2018-12-31',
            ],
            'endDate' => [
                'from' => '2019-10-01',
                'to' => '2019-10-31',
            ],
            'page' => 2,
            'pageSize' => 10
        ]);
        $params = $this->createParams(10, 10,
            [
                'bool' => [
                    'must' => [
                        [
                            'range' => [
                                'startDateFrom' => [
                                    'format' => 'yyyy-MM-dd',
                                    'gte' => '2018-01-01',
                                ]
                            ]
                        ],
                        [
                            'range' => [
                                'startDateTo' => [
                                    'format' => 'yyyy-MM-dd',
                                    'lte' => '2018-12-31',
                                ]
                            ]
                        ],
                        [
                            'range' => [
                                'endDateFrom' => [
                                    'format' => 'yyyy-MM-dd',
                                    'gte' => '2019-10-01',
                                ]
                            ]
                        ],
                        [
                            'range' => [
                                'endDateTo' => [
                                    'format' => 'yyyy-MM-dd',
                                    'lte' => '2019-10-31',
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'startDateFrom' => 'desc'
            ]
        );
        $this->assertSame(
            json_encode($params, JSON_PRETTY_PRINT),
            json_encode($this->builder->getParams($searchRequest), JSON_PRETTY_PRINT)
        );
    }

    public function testBuildParamsWithDateRange()
    {
        $searchRequest = $this->createRequest([
            'startDateFrom' => '2018-01-01',
            'startDateTo' => '2018-12-31',
            'endDateFrom' => '2019-10-01',
            'endDateTo' => '2019-10-31',
            'page' => 2,
            'pageSize' => 10
        ]);
        $params = $this->createParams(10, 10,
            [
                'bool' => [
                    'must' => [
                        [
                            'range' => [
                                'startDateFrom' => [
                                    'format' => 'yyyy-MM-dd',
                                    'gte' => '2018-01-01',
                                ]
                            ]
                        ],
                        [
                            'range' => [
                                'startDateTo' => [
                                    'format' => 'yyyy-MM-dd',
                                    'lte' => '2018-12-31',
                                ]
                            ]
                        ],
                        [
                            'range' => [
                                'endDateFrom' => [
                                    'format' => 'yyyy-MM-dd',
                                    'gte' => '2019-10-01',
                                ]
                            ]
                        ],
                        [
                            'range' => [
                                'endDateTo' => [
                                    'format' => 'yyyy-MM-dd',
                                    'lte' => '2019-10-31',
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            [
                'startDateFrom' => 'desc'
            ]
        );
        $this->assertSame(
            json_encode($params, JSON_PRETTY_PRINT),
            json_encode($this->builder->getParams($searchRequest), JSON_PRETTY_PRINT)
        );
    }

    public function testBuildParamsWithNoParameters()
    {
        $searchRequest = $this->createRequest([
            'page' => 2,
            'pageSize' => 10
        ]);
        $params = $this->createParams(10, 10,
            null,
            [
                'startDateFrom' => 'desc'
            ]
        );
        $this->assertSame(
            json_encode($params, JSON_PRETTY_PRINT),
            json_encode($this->builder->getParams($searchRequest), JSON_PRETTY_PRINT)
        );
    }

    public function testBuildParamsWithPeriod()
    {
        $searchRequest = $this->createRequest([
            'period' => 'period1',
            'page' => 2,
            'pageSize' => 10
        ]);
        $params = $this->createParams(10, 10,
            [
                'bool' => [
                    'must' => [
                        [
                            'term' => [
                                'period' => 'period1'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'startDateFrom' => 'desc'
            ]
        );
        $this->assertSame(
            json_encode($params, JSON_PRETTY_PRINT),
            json_encode($this->builder->getParams($searchRequest), JSON_PRETTY_PRINT)
        );
    }

    public function testBuildParamsWithCatalogs()
    {
        $searchRequest = $this->createRequest([
            'catalogs' => ['c1', 'c2'],
            'page' => 2,
            'pageSize' => 10
        ]);
        $params = $this->createParams(10, 10,
            [
                'bool' => [
                    'must' => [
                        [
                            'term' => [
                                'catalogs' => 'c1'
                            ]
                        ],
                        [
                            'term' => [
                                'catalogs' => 'c2'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'startDateFrom' => 'desc'
            ]
        );
        $this->assertSame(
            json_encode($params, JSON_PRETTY_PRINT),
            json_encode($this->builder->getParams($searchRequest), JSON_PRETTY_PRINT)
        );
    }

    public function testBuildParamsWithContent()
    {
        $searchRequest = $this->createRequest([
            'content' => 'abc',
            'page' => 2,
            'pageSize' => 10
        ]);
        $params = $this->createParams(10, 10,
            [
                'bool' => [
                    'must' => [
                        [
                            'match' => [
                                'content' => [
                                    'query' => 'abc',
                                    'fuzziness' => 'AUTO',
                                    'prefix_length' => 2
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            null
        );
        $this->assertSame(
            json_encode($params, JSON_PRETTY_PRINT),
            json_encode($this->builder->getParams($searchRequest), JSON_PRETTY_PRINT)
        );
    }

    private function createParams(int $offset, int $pageSize, array $query = null, array $sort = null): array
    {
        $params = [
            'index' => 'event',
            'type' => 'event',
            'from' => $offset,
            'size' => $pageSize,
            'body' => [
                'highlight' => [
                    'pre_tags' => '<em class="hl">',
                    'post_tags' => '</em>',
                    'fields' => [
                        'content' => new \stdClass
                    ]
                ],
                'aggregations' => [
                    'period' => [
                        'terms' => [
                            'field' => 'period'
                        ]
                    ],
                    'catalogs' => [
                        'terms' => [
                            'field' => 'catalogs'
                        ]
                    ],
                    'startDate' => [
                        'date_histogram' => [
                            'field' => 'startDateFrom',
                            'interval' => '1y',
                            'format' => 'yyyy'
                        ]
                    ]
                ]
            ]
        ];

        if ($query !== null) {
            $params['body']['query'] = $query;
        }

        if ($sort !== null) {
            $params['body']['sort'] = $sort;
        }

        return $params;
    }

    private function createRequest(array $data): MockObject
    {
        $request = $this->createMock(SearchEventRequest::class);

        if (isset($data['startDate'])) {
            $date = $this->createMock(EventDate::class);
            $date->method('getFrom')->willReturn(Carbon::createFromFormat('Y-m-d', $data['startDate']['from']));
            $date->method('getTo')->willReturn(Carbon::createFromFormat('Y-m-d', $data['startDate']['to']));
            $request->method('getStartDate')->willReturn($date);
        } else {
            $request->method('getStartDate')->willReturn(null);
        }

        if (isset($data['endDate'])) {
            $date = $this->createMock(EventDate::class);
            $date->method('getFrom')->willReturn(Carbon::createFromFormat('Y-m-d', $data['endDate']['from']));
            $date->method('getTo')->willReturn(Carbon::createFromFormat('Y-m-d', $data['endDate']['to']));
            $request->method('getEndDate')->willReturn($date);
        } else {
            $request->method('getEndDate')->willReturn(null);
        }

        $request->method('getContent')->willReturn($data['content'] ?? null);
        $request->method('getStartDateFrom')->willReturn(!isset($data['startDateFrom']) ? null : Carbon::createFromFormat('Y-m-d', $data['startDateFrom']));
        $request->method('getStartDateTo')->willReturn(!isset($data['startDateTo']) ? null : Carbon::createFromFormat('Y-m-d', $data['startDateTo']));
        $request->method('getEndDateFrom')->willReturn(!isset($data['endDateFrom']) ? null : Carbon::createFromFormat('Y-m-d', $data['endDateFrom']));
        $request->method('getEndDateTo')->willReturn(!isset($data['endDateTo']) ? null : Carbon::createFromFormat('Y-m-d', $data['endDateTo']));
        $request->method('getPeriod')->willReturn($data['period'] ?? null);
        $request->method('getCatalogs')->willReturn($data['catalogs'] ?? []);
        $request->method('getPage')->willReturn($data['page'] ?? 1);
        $request->method('getPageSize')->willReturn($data['pageSize'] ?? 10);

        return $request;
    }
}