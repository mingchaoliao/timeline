<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 5:01 PM
 */

namespace App\Timeline\Infrastructure\Persistence\Eloquent\Repositories;

use App\Jobs\CleanUnlinkedImages;
use App\Jobs\GenerateTimeline;
use App\Jobs\LinkImages;
use App\Timeline\Domain\Collections\CreateEventRequestCollection;
use App\Timeline\Domain\Collections\EventCollection;
use App\Timeline\Domain\Collections\EventIdCollection;
use App\Timeline\Domain\Collections\ImageCollection;
use App\Timeline\Domain\Collections\ImageIdCollection;
use App\Timeline\Domain\Models\Event;
use App\Timeline\Domain\Repositories\EventRepository;
use App\Timeline\Domain\Requests\CreateEventRequest;
use App\Timeline\Domain\Requests\PageableRequest;
use App\Timeline\Domain\Requests\UpdateEventRequest;
use App\Timeline\Domain\ValueObjects\EventId;
use App\Timeline\Domain\ValueObjects\UserId;
use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentEvent;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentImage;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentEventRepository implements EventRepository
{
    /**
     * @var EloquentEvent
     */
    private $eventModel;
    /**
     * @var EloquentImageRepository
     */
    private $imageRepository;
    /**
     * @var EloquentCatalogRepository
     */
    private $catalogRepository;
    /**
     * @var EloquentDateAttributeRepository
     */
    private $dateAttributeRepository;
    /**
     * @var EloquentDateFormatRepository
     */
    private $dateFormatRepository;
    /**
     * @var EloquentPeriodRepository
     */
    private $periodRepository;
    /**
     * @var ConnectionInterface
     */
    private $dbh;

    /**
     * EloquentEventRepository constructor.
     * @param EloquentEvent $eventModel
     * @param EloquentImageRepository $imageRepository
     * @param EloquentCatalogRepository $catalogRepository
     * @param EloquentDateAttributeRepository $dateAttributeRepository
     * @param EloquentDateFormatRepository $dateFormatRepository
     * @param EloquentPeriodRepository $periodRepository
     * @param ConnectionInterface $dbh
     */
    public function __construct(EloquentEvent $eventModel, EloquentImageRepository $imageRepository, EloquentCatalogRepository $catalogRepository, EloquentDateAttributeRepository $dateAttributeRepository, EloquentDateFormatRepository $dateFormatRepository, EloquentPeriodRepository $periodRepository, ConnectionInterface $dbh)
    {
        $this->eventModel = $eventModel;
        $this->imageRepository = $imageRepository;
        $this->catalogRepository = $catalogRepository;
        $this->dateAttributeRepository = $dateAttributeRepository;
        $this->dateFormatRepository = $dateFormatRepository;
        $this->periodRepository = $periodRepository;
        $this->dbh = $dbh;
    }

    public function getById(EventId $id): Event
    {
        $eloquentEvent = $this->eventModel->find($id->getValue());

        if ($eloquentEvent === null) {
            throw TimelineException::ofEventWithIdDoesNotExist($id);
        }

        return $this->constructEvent($eloquentEvent);
    }

    public function getByIds(EventIdCollection $ids): EventCollection
    {
        return $this->constructEventCollection(
            $this->eventModel->findMany($ids->toValueArray())
        );
    }

    public function get(PageableRequest $request): EventCollection
    {
        $count = $this->eventModel->count();

        $query = $this->eventModel->with([
            'start_date_attribute',
            'end_date_attribute',
            'period',
            'catalogs',
            'images',
            'start_date_format',
            'end_date_format'
        ]);

        $query->offset($request->getOffset());
        $query->limit($request->getPageSize());
        $query->orderBy('startDate', 'asc');

        $eloquentCollection = $query->get();

        $events = $this->constructEventCollection($eloquentCollection);
        $events->setCount($count);

        return $events;
    }

    /**
     * @param CreateEventRequest $request
     * @param UserId $createUserId
     * @return Event
     * @throws \Throwable
     */
    public function create(
        CreateEventRequest $request,
        UserId $createUserId
    ): Event
    {
        $eventId = null;

        $this->dbh->transaction(function () use (&$eventId, $request, $createUserId) {
            $eventId = $this->createHelper($request, $createUserId);
        });

        $eloquentEvent = $this->eventModel
            ->with([
                'start_date_attribute',
                'end_date_attribute',
                'period',
                'catalogs',
                'images',
                'start_date_format',
                'end_date_format'
            ])
            ->find($eventId);

        LinkImages::dispatch(
            $this->imageRepository
                ->constructImageCollection(
                    $eloquentEvent->getImageCollection()
                )
        );

        GenerateTimeline::dispatch();

        return $this->constructEvent($eloquentEvent);
    }

    /**
     * @param CreateEventRequestCollection $requests
     * @param UserId $createUserId
     * @return EventCollection
     * @throws \Throwable
     */
    public function bulkCreate(
        CreateEventRequestCollection $requests,
        UserId $createUserId
    ): EventCollection
    {
        $eventIds = [];

        $this->dbh->transaction(function () use (&$eventIds, $requests, $createUserId) {
            foreach ($requests as $request) {
                $eventIds[] = $this->createHelper($request, $createUserId);
            }
        });

        $eloquentEvents = $this->eventModel
            ->with([
                'start_date_attribute',
                'end_date_attribute',
                'period',
                'catalogs',
                'images',
                'start_date_format',
                'end_date_format'
            ])
            ->findMany($eventIds);

        $linkImages = new ImageCollection();

        /** @var EloquentEvent $eloquentEvent */
        foreach ($eloquentEvents as $eloquentEvent) {
            $linkImages = $linkImages->merge(
                $this->imageRepository
                    ->constructImageCollection(
                        $eloquentEvent->getImageCollection()
                    )
            );
        }

        LinkImages::dispatch($linkImages);
        GenerateTimeline::dispatch();

        return $this->constructEventCollection($eloquentEvents);
    }

    /**
     * @param EventId $id
     * @param UpdateEventRequest $request
     * @param UserId $updateUserId
     * @return Event
     * @throws TimelineException
     * @throws \Throwable
     */
    public function update(
        EventId $id,
        UpdateEventRequest $request,
        UserId $updateUserId
    ): Event
    {
        $eloquentEvent = $this->eventModel->find($id->getValue());

        if ($eloquentEvent === null) {
            throw TimelineException::ofEventWithIdDoesNotExist($id);
        }

        $this->dbh->transaction(function () use ($eloquentEvent, $request, $id, $updateUserId) {
            $eloquentEvent->update([
                'start_date' => $request->getStartDate(),
                'end_date' => $request->getEndDate(),
                'start_date_attribute_id' => $request->getStartDateAttributeId(),
                'start_date_format_id' => $request->getStartDateFormatId(),
                'end_date_format_id' => $request->getEndDateFormatId(),
                'end_date_attribute_id' => $request->getEndDateAttributeId(),
                'content' => $request->getContent(),
                'period_id' => $request->getPeriodId(),
                'update_user_id' => $updateUserId->getValue()
            ]);

            $unneededImages = $eloquentEvent->images()
                ->whereNotIn('id', $request->getImageIds()->toValueArray())
                ->get();

            $unneededImages->delete();

            $existingImages = $eloquentEvent->images()->get();
            $existingImageIds = $existingImages->map(function (EloquentImage $eloquentImage) {
                return $eloquentImage->getId();
            })->toArray();

            $linkImageIds = array_diff($request->getImageIds()->toValueArray(), $existingImageIds);
            $linkImages = $this->imageRepository->getRawByIds(new ImageIdCollection($linkImageIds));
            $eloquentEvent->saveMany($linkImages);
            $eloquentEvent->catalogs()->sync($request->getCatalogIds()->toValueArray());

            CleanUnlinkedImages::dispatch($unneededImages);
            LinkImages::dispatch($linkImages);
            GenerateTimeline::dispatch();
        });

        return $this->constructEvent($this->eventModel->find($id->getValue()));
    }

    public function delete(EventId $id): bool
    {
        $eloquentEvent = $this->eventModel->find($id->getValue());

        if ($eloquentEvent === null) {
            throw TimelineException::ofEventWithIdDoesNotExist($id);
        }

        GenerateTimeline::dispatch();

        return $eloquentEvent->delete();
    }

    private function constructEvent(EloquentEvent $eloquentEvent): Event
    {
        $startDateAttribute = $eloquentEvent->getStartDateAttributeObj();
        if ($startDateAttribute !== null) {
            $startDateAttribute = $this->dateAttributeRepository
                ->constructDateAttribute($startDateAttribute);
        }

        $endDateAttribute = $eloquentEvent->getEndDateAttributeObj();
        if ($endDateAttribute !== null) {
            $endDateAttribute = $this->dateAttributeRepository
                ->constructDateAttribute($endDateAttribute);
        }

        $startDateFormat = $eloquentEvent->getStartDateFormat();
        $startDateFormat = $this->dateFormatRepository
            ->constructDateFormat($startDateFormat);

        $endDateFormat = $eloquentEvent->getEndDateFormat();
        if ($endDateFormat !== null) {
            $endDateFormat = $this->dateFormatRepository
                ->constructDateFormat($endDateFormat);
        }
        $period = $eloquentEvent->getPeriod();
        if ($period !== null) {
            $period = $this->periodRepository
                ->constructPeriod($period);
        }

        $catalogCollection = $this->catalogRepository
            ->constructCatalogCollection($eloquentEvent->getCatalogCollection());

        $imageCollection = $this->imageRepository
            ->constructImageCollection($eloquentEvent->getImageCollection());
        return new Event(
            new EventId($eloquentEvent->getId()),
            $eloquentEvent->getStartDate(),
            $eloquentEvent->getEndDate(),
            $startDateAttribute,
            $endDateAttribute,
            $startDateFormat,
            $endDateFormat,
            $period,
            $catalogCollection,
            $eloquentEvent->getContent(),
            $imageCollection,
            new UserId($eloquentEvent->getCreateUserId()),
            new UserId($eloquentEvent->getUpdateUserId()),
            $eloquentEvent->getCreatedAt(),
            $eloquentEvent->getUpdatedAt()
        );
    }

    private function constructEventCollection(Collection $collection)
    {
        $results = new EventCollection();
        foreach ($collection as $item) {
            $results->push($this->constructEvent($item));
        }

        return $results;
    }

    private function createHelper(CreateEventRequest $request, UserId $createUserId): int
    {
        /**
         * @var EloquentEvent $eloquentEvent
         * */
        $eloquentEvent = $this->eventModel->create([
            'start_date' => $request->getStartDate(),
            'end_date' => $request->getEndDate(),
            'start_date_attribute_id' => $request->getStartDateAttributeId()->getValue(),
            'start_date_format_id' => $request->getStartDateFormatId()->getValue(),
            'end_date_format_id' => $request->getEndDateFormatId()->getValue(),
            'end_date_attribute_id' => $request->getEndDateAttributeId()->getValue(),
            'content' => $request->getContent(),
            'period_id' => $request->getPeriodId(),
            'create_user_id' => $createUserId->getValue(),
            'update_user_id' => $createUserId->getValue(),
        ]);

        $eloquentImages = $this->imageRepository->getRawByIds($request->getImageIds());
        $eloquentEvent->images()->saveMany($eloquentImages);

        $eloquentEvent->catalogs()->attach($request->getCatalogIds()->toValueArray());

        return $eloquentEvent->getId();
    }
}