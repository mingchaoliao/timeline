<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/21/19
 * Time: 2:59 PM
 */

namespace App\Timeline\Infrastructure\SearchEngine;


use App\Timeline\Domain\Collections\EventCollection;
use App\Timeline\Domain\Collections\EventIdCollection;
use App\Timeline\Domain\Models\Event;
use App\Timeline\Domain\Repositories\SearchEventRepository;
use App\Timeline\Domain\Requests\SearchEventRequest;
use App\Timeline\Domain\ValueObjects\EventId;

class ESSearchEventRepository implements SearchEventRepository
{
    /**
     * @var \Elasticsearch\Client
     */
    private $es;

    /**
     * ESSearchEventRepository constructor.
     * @param \Elasticsearch\Client $es
     */
    public function __construct(\Elasticsearch\Client $es)
    {
        $this->es = $es;
    }

    public function search(SearchEventRequest $request): EventIdCollection
    {
        $startDateFrom = $request->getStartDateFrom();
        $startDateTo = $request->getStartDateTo();
        $endDateFrom = $request->getEndDateFrom();
        $endDateTo = $request->getEndDateTo();
        $periodId = $request->getPeriodId();
        $catalogIds = $request->getCatalogIds();
        $content = $request->getContent();
        $page = $request->getPage();
        $pageSize = $request->getPageSize();
        $offset = ($page - 1) * $pageSize;

        $query = ['bool' => ['must' => []]];

        if ($startDateFrom !== null || $startDateTo !== null) {
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
            array_push($query['bool']['must'], $config);
        }

        if ($endDateFrom !== null || $endDateTo !== null) {
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
            array_push($query['bool']['must'], $config);
        }

        if ($periodId !== null) {
            array_push($query['bool']['must'], [
                'constant_score' => [
                    'filter' => [
                        'term' => [
                            'period' => $periodId->getValue()
                        ]
                    ]
                ]
            ]);
        }

        if ($catalogIds->count() !== 0) {
            $catalogIdStr = explode(',', $catalogIds);
            array_push($query['bool']['must'], [
                'constant_score' => [
                    'filter' => [
                        'terms' => [
                            'catalogs' => $catalogIdStr
                        ]
                    ]
                ]
            ]);
        }

        $sort = [];

        if ($content !== null) {
            array_push($query['bool']['must'], [
                'match' => [
                    'content' => [
                        'query' => $content
                    ]
                ]
            ]);
        } else {
            $sort = [
                'startDate' => 'asc'
            ];
        }

        $elasticSearch = [
            'index' => 'timelines',
            'type' => 'event',
            'from' => $offset,
            'size' => $pageSize,
        ];

        if (!empty($query['bool'])) {
            $elasticSearch['body']['query'] = $query;
        }

        if (!empty($sort)) {
            $elasticSearch['body']['sort'] = $sort;
        }

        $result = $this->es->search($elasticSearch);
        $total = $result['hits']['total'];

        $eventIds = array_map(function (array $eventData) {
            return new EventId($eventData['_id']);
        }, $result['hits']['hits']);

        $eventIdCollection = new EventIdCollection($eventIds);
        $eventIdCollection->setCount($total);

        return $eventIdCollection;
    }

    public function index(Event $event): void
    {
        $this->es->index([
            'body' => $event->toEsBody(),
            'index' => 'timelines',
            'type' => 'event',
            'id' => $event->getId()->getValue(),
        ]);
    }

    public function bulkIndex(EventCollection $events): void
    {
        if (count($events) === 0) {
            return;
        }

        $params = ['body' => []];

        /** @var Event $event */
        foreach ($events as $event) {
            $params['body'][] = [
                'index' => [
                    '_index' => 'timelines',
                    '_type' => 'event',
                    '_id' => $event->getId()->getValue()
                ]
            ];

            $params['body'][] = $event->toEsBody();
        }

        $this->es->bulk($params);
    }

    public function deleteDocument(EventId $id): void
    {
        $this->es->delete([
            'index' => 'timelines',
            'type' => 'event',
            'id' => $id->getValue()
        ]);
    }
}