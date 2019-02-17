<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/21/19
 * Time: 2:59 PM
 */

namespace App\Timeline\Infrastructure\Elasticsearch;


use App\Timeline\Domain\Collections\BucketCollection;
use App\Timeline\Domain\Collections\EventCollection;
use App\Timeline\Domain\Collections\EventHitCollection;
use App\Timeline\Domain\Models\Bucket;
use App\Timeline\Domain\Models\Event;
use App\Timeline\Domain\Models\EventDate;
use App\Timeline\Domain\Models\EventHit;
use App\Timeline\Domain\Models\EventSearchResult;
use App\Timeline\Domain\Repositories\SearchEventRepository as SearchEventRepositoryInterface;
use App\Timeline\Domain\Requests\SearchEventRequest;
use App\Timeline\Domain\ValueObjects\EventId;
use Illuminate\Support\Facades\Log;

class SearchEventRepository implements SearchEventRepositoryInterface
{
    /**
     * @var \Elasticsearch\Client
     */
    private $es;
    /**
     * @var SearchParamsBuilder
     */
    private $searchParamsBuilder;

    /**
     * SearchEventRepository constructor.
     * @param \Elasticsearch\Client $es
     * @param SearchParamsBuilder $searchParamsBuilder
     */
    public function __construct(\Elasticsearch\Client $es, SearchParamsBuilder $searchParamsBuilder)
    {
        $this->es = $es;
        $this->searchParamsBuilder = $searchParamsBuilder;
    }

    public function search(SearchEventRequest $request): EventSearchResult
    {
        $params = $this->searchParamsBuilder->getParams($request);

        $result = $this->es->search($params);

        $total = $result['hits']['total'];
        $hits = $result['hits']['hits'];
        $periodBuckets = $result['aggregations']['period']['buckets'];
        $catalogBuckets = $result['aggregations']['catalogs']['buckets'];
        $dateBuckets = $result['aggregations']['startDate']['buckets'];

        $eventHits = $this->constructEventHits($hits);
        $eventHits->setTotalCount($total);

        $filterBucket = function (array $bucket) {
            return $bucket['doc_count'] > 0;
        };

        return new EventSearchResult(
            $eventHits,
            $this->constructBuckets($periodBuckets),
            $this->constructBuckets($catalogBuckets),
            $this->constructDateBuckets(
                array_values(array_filter($dateBuckets, $filterBucket))
            )
        );
    }

    private function constructBuckets(array $buckets): BucketCollection
    {
        return new BucketCollection(array_map(function (array $bucket) {
            return new Bucket(
                $bucket['key'],
                $bucket['doc_count']
            );
        }, $buckets));
    }

    private function constructDateBuckets(array $buckets): BucketCollection
    {
        return new BucketCollection(array_map(function (array $bucket) {
            return new Bucket(
                $bucket['key_as_string'],
                $bucket['doc_count']
            );
        }, $buckets));
    }

    private function constructEventHits(array $hits): EventHitCollection
    {
        return new EventHitCollection(array_map(function (array $hit) {
            $highlight = $hit['highlight'] ?? null;
            $hit = $hit['_source'];
            return new EventHit(
                new EventId($hit['id']),
                new EventDate($hit['startDateStr']),
                $hit['endDateStr'] === null ? null : new EventDate($hit['endDateStr']),
                $hit['startDateAttribute'],
                $hit['endDateAttribute'],
                !$highlight ? $this->truncateContent($hit['content'], 0.5) : implode(' ... ', $highlight['content']) . ' ...'
            );
        }, $hits));
    }

    private function truncateContent(string $content, float $ratio): string
    {
        $len = mb_strlen($content, 'UTF-8');
        return mb_substr($content, 0, floor($len * $ratio), 'UTF-8') . ' ...';
    }

    public function index(Event $event): void
    {
        $response = $this->es->index([
            'body' => $event->toEsBody(),
            'index' => 'event',
            'type' => 'event',
            'id' => $event->getId()->getValue(),
        ]);

        Log::info('index document completed', [
            'index' => 'event',
            'event_id' => $event->getId()->getValue(),
            'elasticsearch_response' => $response
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
                    '_index' => 'event',
                    '_type' => 'event',
                    '_id' => $event->getId()->getValue()
                ]
            ];

            $params['body'][] = $event->toEsBody();
        }

        $this->es->bulk($params);

        Log::info('bulk index documents completed', [
            'index' => 'event',
            'num_of_events' => count($events)
        ]);
    }

    public function deleteDocument(EventId $id): void
    {
        $response = $this->es->delete([
            'index' => 'event',
            'type' => 'event',
            'id' => $id->getValue()
        ]);

        Log::info('document deleted', [
            'index' => 'event',
            'event_id' => $id->getValue(),
            'elasticsearch_response' => $response
        ]);
    }

    public function createEventIndex(): void
    {
        $response = $this->es->indices()->create([
            'index' => 'event',
            'body' => [
                'mappings' => [
                    'event' => [
                        'properties' => [
                            'id' => [
                                'type' => 'long',
                            ],
                            'startDateFrom' => [
                                'type' => 'date',
                                'format' => 'yyyy-MM-dd'
                            ],
                            'startDateTo' => [
                                'type' => 'date',
                                'format' => 'yyyy-MM-dd'
                            ],
                            'startDateStr' => [
                                'type' => 'keyword',
                            ],
                            'startDateAttribute' => [
                                'type' => 'keyword',
                            ],
                            'endDateFrom' => [
                                'type' => 'date',
                                'format' => 'yyyy-MM-dd'
                            ],
                            'endDateTo' => [
                                'type' => 'date',
                                'format' => 'yyyy-MM-dd'
                            ],
                            'endDateStr' => [
                                'type' => 'keyword',
                            ],
                            'endDateAttribute' => [
                                'type' => 'keyword',
                            ],
                            'period' => [
                                'type' => 'keyword'
                            ],
                            'catalogs' => [
                                'type' => 'keyword'
                            ],
                            'content' => [
                                'type' => 'text',
                                'analyzer' => 'ik_max_word',
                                'search_analyzer' => 'ik_smart'
                            ],
                        ]
                    ]
                ]
            ]
        ]);

        Log::notice('index "event" created', ['elasticsearch_response' => $response]);
    }

    public function hasEventIndex(): bool
    {
        return $this->es->indices()->exists([
            'index' => 'event'
        ]);
    }

    public function deleteEventIndex(): void
    {
        $response = $this->es->indices()->delete([
            'index' => 'event'
        ]);

        Log::notice('index "event" deleted', [
            'elasticsearch_response' => $response
        ]);
    }
}