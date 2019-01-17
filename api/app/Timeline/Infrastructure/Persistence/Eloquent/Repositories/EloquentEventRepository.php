<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 5:01 PM
 */

namespace App\Timeline\Infrastructure\Persistence\Eloquent\Repositories;

use App\Jobs\CleanUnlinkedImages;
use App\Jobs\LinkImages;
use App\Timeline\Domain\Collections\EventCollection;
use App\Timeline\Domain\Collections\EventIdCollection;
use App\Timeline\Domain\Collections\ImageIdCollection;
use App\Timeline\Domain\Models\Event;
use App\Timeline\Domain\Models\Image;
use App\Timeline\Domain\Repositories\CatalogRepository;
use App\Timeline\Domain\Repositories\DateAttributeRepository;
use App\Timeline\Domain\Repositories\DateFormatRepository;
use App\Timeline\Domain\Repositories\EventRepository;
use App\Timeline\Domain\Repositories\ImageRepository;
use App\Timeline\Domain\Repositories\PeriodRepository;
use App\Timeline\Domain\Requests\CreateEventRequest;
use App\Timeline\Domain\Requests\PageableRequest;
use App\Timeline\Domain\Requests\UpdateEventRequest;
use App\Timeline\Domain\ValueObjects\EventId;
use App\Timeline\Domain\ValueObjects\UserId;
use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentEvent;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentImage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentEventRepository implements EventRepository
{
    /**
     * @var EloquentEvent
     */
    private $eventModel;
    /**
     * @var ImageRepository
     */
    private $imageRepository;
    /**
     * @var CatalogRepository
     */
    private $catalogRepository;
    /**
     * @var DateAttributeRepository
     */
    private $dateAttributeRepository;
    /**
     * @var DateFormatRepository
     */
    private $dateFormatRepository;
    /**
     * @var PeriodRepository
     */
    private $periodRepository;
    /**
     * @var ConnectionInterface
     */
    private $dbh;
    /**
     * @var Filesystem
     */
    private $fs;

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
        $eloquentEvent = null;

        $this->dbh->transaction(function () use ($eloquentEvent, $request, $createUserId) {
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

            LinkImages::dispatch($eloquentImages);
        });

        return $this->constructEvent($this->eventModel->find($eloquentEvent->getId()));
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
        });

        return $this->constructEvent($this->eventModel->find($id->getValue()));
    }

    public function delete(EventId $id): bool
    {
        // TODO: Implement delete() method.
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
}