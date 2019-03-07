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
    public function getParams(SearchEventRequest $request): array
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

        if ($startDate !== null) {
            $query['bool']['must'][] = [
                'range' => [
                    'startDateFrom' => [
                        'format' => 'yyyy-MM-dd',
                        'gte' => $startDate->getFrom()->format('Y-m-d'),
                    ]
                ]
            ];

            $query['bool']['must'][] = [
                'range' => [
                    'startDateTo' => [
                        'format' => 'yyyy-MM-dd',
                        'lte' => $startDate->getTo()->format('Y-m-d'),
                    ]
                ]
            ];
        } elseif ($startDateFrom !== null || $startDateTo !== null) {
            if ($startDateFrom !== null) {
                $query['bool']['must'][] = [
                    'range' => [
                        'startDateFrom' => [
                            'format' => 'yyyy-MM-dd',
                            'gte' => $startDateFrom->format('Y-m-d'),
                        ]
                    ]
                ];
            }
            if ($startDateTo !== null) {
                $query['bool']['must'][] = [
                    'range' => [
                        'startDateTo' => [
                            'format' => 'yyyy-MM-dd',
                            'lte' => $startDateTo->format('Y-m-d'),
                        ]
                    ]
                ];
            }
        }

        if ($endDate !== null) {
            $query['bool']['must'][] = [
                'range' => [
                    'endDateFrom' => [
                        'format' => 'yyyy-MM-dd',
                        'gte' => $endDate->getFrom()->format('Y-m-d'),
                    ]
                ]
            ];

            $query['bool']['must'][] = [
                'range' => [
                    'endDateTo' => [
                        'format' => 'yyyy-MM-dd',
                        'lte' => $endDate->getTo()->format('Y-m-d'),
                    ]
                ]
            ];
        } elseif ($endDateFrom !== null || $endDateTo !== null) {
            if ($endDateFrom !== null) {
                $query['bool']['must'][] = [
                    'range' => [
                        'endDateFrom' => [
                            'format' => 'yyyy-MM-dd',
                            'gte' => $endDateFrom->format('Y-m-d'),
                        ]
                    ]
                ];
            }
            if ($endDateTo !== null) {
                $query['bool']['must'][] = [
                    'range' => [
                        'endDateTo' => [
                            'format' => 'yyyy-MM-dd',
                            'lte' => $endDateTo->format('Y-m-d'),
                        ]
                    ]
                ];
            }
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
                    'term' => [
                        'catalogs' => $catalog
                    ]
                ];
            }
        }

        if ($content !== null) {
            $query['bool']['must'][] = [
                'match' => [
                    'content' => [
                        'query' => $content,
                        'fuzziness' => 'AUTO',
                        'prefix_length' => 2
                    ]
                ]
            ];
        }

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
                            'field' => 'period',
                            'size' => '30' // TODO: refactor to use pagination in the future
                        ]
                    ],
                    'catalogs' => [
                        'terms' => [
                            'field' => 'catalogs',
                            'size' => '200' // TODO: refactor to use pagination in the future
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

        if (!empty($query)) {
            $params['body']['query'] = $query;
        }

        if ($content === null) {
            $params['body']['sort'] = [
                'startDateFrom' => 'desc'
            ];
        }

        return $params;
    }
}