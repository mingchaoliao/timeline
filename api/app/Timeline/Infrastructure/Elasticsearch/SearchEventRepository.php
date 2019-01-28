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
use Carbon\Carbon;

class SearchEventRepository implements SearchEventRepositoryInterface
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

    public function search(SearchEventRequest $request): EventSearchResult
    {
        $builder = SearchParamsBuilder::createFromRequest($request);

        $result = $this->es->search($builder->getParams());

        $total = $result['hits']['total'];
        $hits = $result['hits']['hits'];
        $periodBuckets = $result['aggregations']['period']['buckets'];
        $catalogBuckets = $result['aggregations']['catalogs']['buckets'];
        $dateBuckets = $result['aggregations']['startDate']['buckets'];

        $eventHits = $this->constructEventHits($hits);
        $eventHits->setCount($total);

        $filterBucket = function (array $bucket) {
            return $bucket['doc_count'] > 0;
        };

        return new EventSearchResult(
            $eventHits,
            $this->constructBuckets($periodBuckets),
            $this->constructBuckets(
                array_filter($catalogBuckets, $filterBucket)
            ),
            $this->constructDateBuckets(
                array_filter($dateBuckets, $filterBucket)
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
                Carbon::createFromFormat('Y-m-d', $bucket['key_as_string'])
                    ->format('Y'),
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
                !$highlight ? $hit['content'] : implode(' ... ', $highlight['content'])
            );
        }, $hits));
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

    private function constructSearchResult(array $result): EventSearchResult
    {
    }
}