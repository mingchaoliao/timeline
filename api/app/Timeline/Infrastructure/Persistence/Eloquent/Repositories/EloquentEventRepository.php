<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 5:01 PM
 */

namespace App\Timeline\Infrastructure\Persistence\Eloquent\Repositories;


use App\Common\Authorization;
use App\Timeline\Domain\Collections\EventCollection;
use App\Timeline\Domain\Models\Event;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentEvent;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EloquentEventRepository extends EloquentBaseRepository
{
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
     * @var EloquentImageRepository
     */
    private $imageRepository;

    /**
     * EventRepository constructor.
     *
     * @param Authorization $authorization
     * @param EloquentCatalogRepository $catalogRepository
     * @param EloquentDateAttributeRepository $dateAttributeRepository
     * @param EloquentDateFormatRepository $dateFormatRepository
     * @param EloquentPeriodRepository $periodRepository
     * @param EloquentImageRepository $imageRepository
     */
    public function __construct(
        Authorization $authorization,
        EloquentCatalogRepository $catalogRepository,
        EloquentDateAttributeRepository $dateAttributeRepository,
        EloquentDateFormatRepository $dateFormatRepository,
        EloquentPeriodRepository $periodRepository,
        EloquentImageRepository $imageRepository
    ) {
        parent::__construct($authorization);
        $this->catalogRepository = $catalogRepository;
        $this->dateAttributeRepository = $dateAttributeRepository;
        $this->dateFormatRepository = $dateFormatRepository;
        $this->periodRepository = $periodRepository;
        $this->imageRepository = $imageRepository;
    }

    public function getById(string $id): Event
    {
        $eloquentEvent = EloquentEvent::find($id);

        if ($eloquentEvent === null) {
            throw new BadRequestHttpException('Event with id, "' . $id . '", does not found');
        }

        return $this->constructEvent($eloquentEvent);
    }

    public function getCollectionByIds(array $ids): EventCollection {
        $eloquentEvents = EloquentEvent::byIds($ids)->get();
        return $this->constructEventCollection($eloquentEvents);
    }

    public function deleteById(int $id) {
        $event = EloquentEvent::findOrFail($id);
        $event->delete();
    }

    public function getCollection(
        int $offset = null,
        int $limit = null,
        int &$count = null,
        string $order = 'startDate',
        string $direction = 'asc'
    ): EventCollection {
        if ($count !== null) {
            $count = EloquentEvent::count();
        }
        $query = EloquentEvent::with([
            'start_date_attribute',
            'end_date_attribute',
            'period',
            'catalogs',
            'images',
            'start_date_format',
            'end_date_format'
        ]);

        switch ($order) {
            case 'startDate':
                $order = 'start_date';
                break;
            default:
                throw new \InvalidArgumentException('Invalid order');
        }

        if(!in_array($direction, ['asc', 'desc'])) {
            throw new \InvalidArgumentException('Invalid directory');
        }

        if ($offset !== null) {
            $query->offset($offset);
        }
        if ($limit !== null) {
            $query->limit($limit);
        }
        $query->orderBy($order, $direction);
        $eloquentCollection = $query->get();

        return $this->constructEventCollection($eloquentCollection);
    }

    /**
     * @param Carbon $startDate
     * @param string $content
     * @param int $startDateFormatId
     * @param int $createUserId
     * @param int|null $endDateFormatId
     * @param int|null $startDateAttributeId
     * @param Carbon|null $endDate
     * @param int|null $endDateAttributeId
     * @param int|null $periodId
     * @param array $catalogIds
     * @param array $imageData
     *
     * @return Event
     */
    public function create(
        Carbon $startDate,
        string $content,
        int $startDateFormatId,
        int $createUserId,
        int $endDateFormatId = null,
        int $startDateAttributeId = null,
        Carbon $endDate = null,
        int $endDateAttributeId = null,
        int $periodId = null,
        array $catalogIds = [],
        array $imageData = []
    ): Event {
        $eloquentEvent = EloquentEvent::createNew(
            $startDate,
            $content,
            $createUserId,
            $startDateFormatId,
            $endDateFormatId,
            $startDateAttributeId,
            $endDate,
            $endDateAttributeId,
            $periodId,
            $catalogIds,
            $imageData
        );

        return $this->constructEvent($eloquentEvent);
    }

    public function update(
        int $id,
        Carbon $startDate,
        string $content,
        int $startDateFormatId,
        int $createUserId,
        int $endDateFormatId = null,
        int $startDateAttributeId = null,
        Carbon $endDate = null,
        int $endDateAttributeId = null,
        int $periodId = null,
        array $catalogIds = [],
        array $imageData = []
    ): Event {
        $eloquentEvent = EloquentEvent::updateById(
            $id,
            $startDate,
            $content,
            $createUserId,
            $startDateFormatId,
            $endDateFormatId,
            $startDateAttributeId,
            $endDate,
            $endDateAttributeId,
            $periodId,
            $catalogIds,
            $imageData
        );

        return $this->constructEvent($eloquentEvent);
    }


    public function constructEvent(EloquentEvent $eloquentEvent): Event
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
            $eloquentEvent->getId(),
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
            $eloquentEvent->getCreateUserId(),
            $eloquentEvent->getUpdateUserId(),
            $eloquentEvent->getCreatedAt(),
            $eloquentEvent->getUpdatedAt()
        );
    }

    public function constructEventCollection(Collection $collection)
    {
        $results = new EventCollection();
        foreach ($collection as $item) {
            $results->push($this->constructEvent($item));
        }

        return $results;
    }
}