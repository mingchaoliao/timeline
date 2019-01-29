<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/25/19
 * Time: 10:03 PM
 */

namespace App\Timeline\Infrastructure\Elasticsearch;


use App\Timeline\Domain\Requests\SearchEventRequest;

class SearchParamsBuilder
{
    /**
     * @var array
     */
    private $params;

    /**
     * SearchRequestBuilder constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public static function createFromRequest(SearchEventRequest $request): self
    {
        $startDate = $request->getStartDate();
        $startDateFrom = $request->getStartDateFrom();
        $startDateTo = $request->getStartDateTo();
        $endDate = $request->getEndDate();
        $endDateFrom = $request->getEndDateFrom();
        $endDateTo = $request->getEndDateTo();
        $period = $request->getPeriod();
        $catalogs = $request->getCatalogs();
        $content = $request->getContent();
        $page = $request->getPage();
        $pageSize = $request->getPageSize();
        $offset = ($page - 1) * $pageSize;

        $query = [];

        if($startDate !== null) {
            $config = [
                'range' => [
                    'startDate' => [
                        'format' => 'yyyy-MM-dd'
                    ]
                ]
            ];
            $config['range']['startDate']['gte'] = $startDate->toStartDate()->format('Y-m-d');
            $config['range']['startDate']['lte'] = $startDate->toEndDate()->format('Y-m-d');

            $query['bool']['must'][] = $config;
        } elseif ($startDateFrom !== null || $startDateTo !== null) {
            $config = [
                'range' => [
                    'startDate' => [
                        'format' => 'yyyy-MM-dd'
                    ]
                ]
            ];
            if ($startDateFrom !== null) {
                $config['range']['startDate']['gte'] = $startDateFrom->format('Y-m-d');
            }
            if ($startDateTo !== null) {
                $config['range']['startDate']['lte'] = $startDateTo->format('Y-m-d');
            }
            $query['bool']['must'][] = $config;
        }

        if($endDate !== null) {
            $config = [
                'range' => [
                    'endDate' => [
                        'format' => 'yyyy-MM-dd'
                    ]
                ]
            ];
            $config['range']['endDate']['gte'] = $endDate->toStartDate()->format('Y-m-d');
            $config['range']['endDate']['lte'] = $endDate->toEndDate()->format('Y-m-d');

            $query['bool']['must'][] = $config;
        } elseif ($endDateFrom !== null || $endDateTo !== null) {
            $config = [
                'range' => [
                    'endDate' => [
                        'format' => 'yyyy-MM-dd'
                    ]
                ]
            ];
            if ($endDateFrom !== null) {
                $config['range']['endDate']['gte'] = $endDateFrom->format('Y-m-d');
            }
            if ($endDateTo !== null) {
                $config['range']['endDate']['lte'] = $endDateTo->format('Y-m-d');
            }
            $query['bool']['must'][] = $config;
        }

        if ($period !== null) {
            $query['bool']['must'][] = [
                'term' => [
                    'period' => $period
                ]
            ];
        }

        if (count($catalogs) !== 0) {
            foreach ($catalogs as $catalog) {
                $query['bool']['must'][] = [
                    [
                        'term' => [
                            'catalogs' => $catalog
                        ]
                    ]
                ];
            }
        }

        if ($content !== null) {
            $query['bool']['must']['match']['content'] = [
                'query' => $content,
                'fuzziness' => 'AUTO',
                'prefix_length' => 2
            ];
        }

        $params = [
            'index' => 'timelines',
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
                            'field' => 'startDate',
                            'interval' => '1y',
                            'format' => 'yyyy'
                        ]
                    ]
                ]
            ]
        ];

        if (!empty($query)) {
            $params['body']['query'] = $query;
        }

        if ($content === null) {
            $params['body']['sort'] = [
                'startDate' => 'desc'
            ];
        }

        return new static($params);
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}