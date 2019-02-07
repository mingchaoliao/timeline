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
    private $es;

    protected function setUp()
    {
        $this->es = $this->createMock(Client::class);
        $this->searchEventRepo = new SearchEventRepository($this->es);
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

    public function testSearchEventUsingES() {

    }

    private function createRequest(array $data): MockObject
    {
        $request = $this->createMock(SearchEventRequest::class);

        $request->method('getContent')->willReturn($data['content'] ?? null);
        $request->method('getStartDate')->willReturn($data['startDate'] ?? null);
        $request->method('getStartDateFrom')->willReturn($data['startDateFrom'] ?? null);
        $request->method('getStartDateTo')->willReturn($data['startDateTo'] ?? null);
        $request->method('getEndDate')->willReturn($data['endDate'] ?? null);
        $request->method('getEndDateFrom')->willReturn($data['endDateFrom'] ?? null);
        $request->method('getEndDateTo')->willReturn($data['endDateTo'] ?? null);
        $request->method('getPeriod')->willReturn($data['period'] ?? null);
        $request->method('getCatalogs')->willReturn($data['catalogs'] ?? []);
        $request->method('getPage')->willReturn($data['page'] ?? 1);
        $request->method('getPageSize')->willReturn($data['pageSize'] ?? 10);

        return $request;
    }
}