<?php

namespace App\Http\Controllers\Api;

use App\Timeline\Domain\Collections\EventCollection;
use App\Timeline\Domain\Models\DateFormat;
use App\Timeline\Domain\Models\Event;
use App\Timeline\Domain\Models\Image;
use App\Timeline\Exceptions\DateAttributeNotFoundException;
use App\Timeline\Exceptions\DateFormatNotFoundException;
use App\Timeline\Exceptions\PeriodNotFoundException;
use App\Http\Controllers\Controller;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentCatalogRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentDateAttributeRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentDateFormatRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentEventRepository;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentPeriodRepository;
use Carbon\Carbon;
use Elasticsearch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class EventController extends Controller
{
    private $eventRepository;
    private $dateFormatRepository;
    private $dateAttributeRepository;
    private $periodRepository;
    private $catalogRepository;

    public function __construct(
        EloquentEventRepository $eventRepository,
        EloquentDateFormatRepository $dateFormatRepository,
        EloquentDateAttributeRepository $dateAttributeRepository,
        EloquentPeriodRepository $periodRepository,
        EloquentCatalogRepository $catalogRepository
    )
    {
        $this->eventRepository = $eventRepository;
        $this->dateFormatRepository = $dateFormatRepository;
        $this->dateAttributeRepository = $dateAttributeRepository;
        $this->periodRepository = $periodRepository;
        $this->catalogRepository = $catalogRepository;
    }

    public function getById(string $id)
    {
        $event = $this->eventRepository->getById($id);

        return response()->json($event);
    }

    public function search(Request $request)
    {

        $this->validate($request, [
            'startDateFrom' => 'date_format:Y-m-d',
            'startDateTo' => 'date_format:Y-m-d',
            'endDateFrom' => 'date_format:Y-m-d',
            'endDateTo' => 'date_format:Y-m-d',
            'period' => 'nullable|string',
            'content' => 'nullable|string',
            'offset' => 'nullable|integer|gte:0',
            'size' => 'nullable|integer|lte:20'
        ]);
        $startDateFrom = $request->get('startDateFrom');
        $startDateTo = $request->get('startDateTo');
        $endDateFrom = $request->get('endDateFrom');
        $endDateTo = $request->get('endDateTo');
        $period = $request->get('period');
        $catalogs = $request->get('catalogs');
        $content = $request->get('content');
        $offset = $request->get('offset') ?? 0;
        $limit = $request->get('limit') ?? 10;

        $query = ['bool' => ['must' => []]];

        if ($startDateFrom !== null || $startDateTo !== null) {
            $config = [
                'range' => [
                    'startDate' => [
                        'format' => 'yyyy-MM-dd'
                    ]
                ]
            ];
            if($startDateFrom !== null) {
                $config['range']['startDate']['gte'] = $startDateFrom;
            }
            if($startDateTo !== null) {
                $config['range']['startDate']['lte'] = $startDateTo;
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
            if($endDateFrom !== null) {
                $config['range']['endDate']['gte'] = $endDateFrom;
            }
            if($endDateTo !== null) {
                $config['range']['endDate']['lte'] = $endDateTo;
            }
            array_push($query['bool']['must'], $config);
        }

        if ($period !== null) {
            array_push($query['bool']['must'], [
                'constant_score' => [
                    'filter' => [
                        'term' => [
                            'period' => $period
                        ]
                    ]
                ]
            ]);
        }

        if ($catalogs !== null) {
            $catalogs = explode(',', $catalogs);
            array_push($query['bool']['must'], [
                'constant_score' => [
                    'filter' => [
                        'terms' => [
                            'catalogs' => $catalogs
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
            'size' => $limit,
        ];

        if (!empty($query)) {
            $elasticSearch['body']['query'] = $query;
        }

        if(!empty($sort)) {
            $elasticSearch['body']['sort'] = $sort;
        }

        $result = Elasticsearch::search($elasticSearch);

        $total = $result['hits']['total'];
        $ids = array_map(function (array $eventData) {
            return $eventData['_id'];
        }, $result['hits']['hits']);

        $eventCollection = $this->eventRepository->getCollectionByIds($ids);

        $eventMap = [];
        foreach ($eventCollection as $event) {
            $eventMap[$event->getId()] = $event;
        }
        $collection = new EventCollection();
        foreach($ids as $id) {
            $collection->push($eventMap[$id]);
        }

        return response()
            ->json($collection)
            ->header('X-Total-Count', $total);
    }

    public function get(Request $request)
    {
        $this->validate($request, [
            'limit' => 'nullable|integer|gte:1',
            'offset' => 'nullable|integer|gte:0',
            'order' => 'nullable|string|in:startDate',
            'direction' => 'nullable|string|in:asc,desc'
        ]);

        $limit = $request->get('limit') ?? '10';
        $offset = $request->get('offset') ?? '0';
        $order = $request->get('order') ?? 'startDate';
        $direction = $request->get('direction') ?? 'asc';

        $limit = intval($limit);
        $offset = intval($offset);

        $total = 0;

        $collection = $this->eventRepository
            ->getCollection($offset, $limit, $total, $order, $direction);

        return response()
            ->json($collection)
            ->header('X-Total-Count', $total);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function createNew(Request $request)
    {
        $event = $this->createHelper(
            $request->get('startDate'),
            $request->get('startDateAttributeId'),
            $request->get('startDateFormatId'),
            $request->get('endDate'),
            $request->get('endDateAttributeId'),
            $request->get('endDateFormatId'),
            $request->get('periodId'),
            $request->get('catalogs'),
            $request->get('content'),
            $request->get('images')
        );
        Artisan::call('timeline:generate');
        return response()->json($event);
    }

    public function updateEvent(int $id, Request $request) {
        $event = $this->updateHelper(
            $id,
            $request->get('startDate'),
            $request->get('startDateAttributeId'),
            $request->get('startDateFormatId'),
            $request->get('endDate'),
            $request->get('endDateAttributeId'),
            $request->get('endDateFormatId'),
            $request->get('periodId'),
            $request->get('catalogs'),
            $request->get('content'),
            $request->get('images')
        );
        Artisan::call('timeline:generate');
        return response()->json($event);
    }

    public function bulkCreate(Request $request)
    {
        $this->validate($request, [
            'events' => 'required|array',
        ]);

        $events = $request->get('events');

        try {
            DB::transaction(function () use ($events) {
                foreach ($events as $event) {
                    $this->createHelper(
                        $event['startDate'] ?? null,
                        $event['startDateAttributeId'] ?? null,
                        $event['startDateFormatId'] ?? null,
                        $event['endDate'] ?? null,
                        $event['endDateAttributeId'] ?? null,
                        $event['endDateFormatId'] ?? null,
                        $event['periodId'] ?? null,
                        $event['catalogs'] ?? null,
                        $event['content'] ?? null,
                        $event['images'] ?? null
                    );
                }
            });
            Artisan::call('timeline:generate');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json(false, 400);
        }

        return response()->json(true);
    }

    private function updateHelper(
        int $id,
        ?string $startDate,
        ?string $startDateAttributeId,
        ?string $startDateFormatId,
        ?string $endDate,
        ?string $endDateAttributeId,
        ?string $endDateFormatId,
        ?string $periodId,
        ?array $catalogIds,
        ?string $content,
        ?array $images
    ): Event {
        // start date must be provided
        if ($startDate === null) {
            throw new BadRequestHttpException('Invalid startDate');
        }

        // start date format must be provided
        if ($startDateFormatId === null) {
            throw new BadRequestHttpException('Missing startDateFormatId');
        }

        if ($catalogIds === null) {
            $catalogIds = [];
        } else {
            $catalogIds = array_unique($catalogIds);
        }

        $numberOfCatalogs = count($catalogIds);
        if ($numberOfCatalogs !== 0) {
            $catalogCollection = $this->catalogRepository->getCollectionByIds($catalogIds);
            if ($numberOfCatalogs !== count($catalogCollection)) {
                throw new BadRequestHttpException('Invalid catalogs');
            }
        }

        if ($content === null || $content === '') {
            throw new BadRequestHttpException('Missing/empty content');
        }

        if ($images === null) {
            $images = [];
        }

        foreach ($images as $image) {
            if (!is_array($image)
                || !array_key_exists('description', $image)
            ) {
                throw new BadRequestHttpException('Invalid images');
            }

            if (!isset($image['path'])
                || !Storage::disk()->exists(Image::TMP_PATH . '/' . $image['path'])
                && !Storage::disk()->exists(Image::PATH . '/' . $image['path'])) {
                throw new BadRequestHttpException('Invalid images ' . $image['path'] ?? null);
            }
        }

        $startDatePhpFormat = null;
        $isStartDateAttributeAllowed = false;
        try {
            $startDateFormat = $this->dateFormatRepository
                ->getById($startDateFormatId);
            /**
             * @var DateFormat $startDatePhpFormat
             * */
            $startDatePhpFormat = $startDateFormat->getPhpFormat();
            $isStartDateAttributeAllowed = $startDateFormat->isAttributeAllowed();
        } catch (DateFormatNotFoundException $e) {
            throw new BadRequestHttpException('Invalid startDateFormatId');
        }

        try {
            $startDate = Carbon::createFromFormat($startDatePhpFormat, $startDate);
            if (!$startDateFormat->hasMonth() && !$startDateFormat->hasDay()) {
                $startDate->firstOfYear();
            } elseif (!$startDateFormat->hasDay()) {
                $startDate->firstOfMonth();
            }
            $startDate->setTime(0, 0, 0);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException('Invalid startDate');
        }

        // start date attribute should only be set when it is allowed
        if (!$isStartDateAttributeAllowed && $startDateAttributeId !== null) {
            throw new BadRequestHttpException('startDateAttributeId is not allowed here');
        }

        if ($startDateAttributeId !== null) {
            try {
                $this->dateAttributeRepository->getById($startDateAttributeId);
            } catch (DateAttributeNotFoundException $e) {
                throw new BadRequestHttpException('Invalid startDateAttributeId');
            }
        }

        // endDateFormatId must be provided if endDate is provided
        if ($endDate !== null && $endDateFormatId === null) {
            throw new BadRequestHttpException('Missing endDateFormatId');
        }

        $endDatePhpFormat = null;
        $isEndDateAttributeAllowed = false;
        if ($endDateFormatId !== null) {
            try {
                $endDateFormat = $this->dateFormatRepository
                    ->getById($endDateFormatId);
                $endDatePhpFormat = $endDateFormat->getPhpFormat();
                $isEndDateAttributeAllowed = $endDateFormat->isAttributeAllowed();
            } catch (DateFormatNotFoundException $e) {
                throw new BadRequestHttpException('Invalid endDateFormatId');
            }

            // end date attribute should only be set when it is allowed
            if (!$isEndDateAttributeAllowed && $endDateAttributeId !== null) {
                throw new BadRequestHttpException('endDateAttributeId is not allowed here');
            }

            if ($endDateAttributeId !== null) {
                try {
                    $this->dateAttributeRepository->getById($endDateAttributeId);
                } catch (DateAttributeNotFoundException $e) {
                    throw new BadRequestHttpException('Invalid endDateAttributeId');
                }
            }

            try {
                $endDate = Carbon::createFromFormat($endDatePhpFormat, $endDate);
                if (!$endDateFormat->hasMonth() && !$endDateFormat->hasDay()) {
                    $endDate->lastOfYear();
                } elseif (!$endDateFormat->hasDay()) {
                    $endDate->lastOfMonth();
                }
                $endDate->setTime(23, 59, 59);
            } catch (\InvalidArgumentException $e) {
                throw new BadRequestHttpException('Invalid endDate');
            }
        }

        if ($periodId !== null) {
            try {
                $this->periodRepository->getById($periodId);
            } catch (PeriodNotFoundException $e) {
                throw new BadRequestHttpException('Invalid periodId');
            }
        }

        $event = $this->eventRepository->update(
            $id,
            $startDate,
            $content,
            $startDateFormatId,
            Auth::user()->getId(),
            $endDateFormatId,
            $startDateAttributeId,
            $endDate,
            $endDateAttributeId,
            $periodId,
            $catalogIds,
            $images
        );

        Elasticsearch::index($event->toEsArray());

        return $event;
    }

    private function createHelper(
        ?string $startDate,
        ?string $startDateAttributeId,
        ?string $startDateFormatId,
        ?string $endDate,
        ?string $endDateAttributeId,
        ?string $endDateFormatId,
        ?string $periodId,
        ?array $catalogIds,
        ?string $content,
        ?array $images
    ): Event
    {
        // start date must be provided
        if ($startDate === null) {
            throw new BadRequestHttpException('Invalid startDate');
        }

        // start date format must be provided
        if ($startDateFormatId === null) {
            throw new BadRequestHttpException('Missing startDateFormatId');
        }

        if ($catalogIds === null) {
            $catalogIds = [];
        } else {
            $catalogIds = array_unique($catalogIds);
        }

        $numberOfCatalogs = count($catalogIds);
        if ($numberOfCatalogs !== 0) {
            $catalogCollection = $this->catalogRepository->getCollectionByIds($catalogIds);
            if ($numberOfCatalogs !== count($catalogCollection)) {
                throw new BadRequestHttpException('Invalid catalogs');
            }
        }

        if ($content === null || $content === '') {
            throw new BadRequestHttpException('Missing/empty content');
        }

        if ($images === null) {
            $images = [];
        }

        foreach ($images as $image) {
            if (!is_array($image)
                || !array_key_exists('description', $image)
            ) {
                throw new BadRequestHttpException('Invalid images');
            }

            if (!isset($image['path']) || !Storage::disk()->exists(Image::TMP_PATH . '/' . $image['path'])) {
                throw new BadRequestHttpException('Invalid images ' . $image['path'] ?? null);
            }
        }

        $startDatePhpFormat = null;
        $isStartDateAttributeAllowed = false;
        try {
            $startDateFormat = $this->dateFormatRepository
                ->getById($startDateFormatId);
            /**
             * @var DateFormat $startDatePhpFormat
             * */
            $startDatePhpFormat = $startDateFormat->getPhpFormat();
            $isStartDateAttributeAllowed = $startDateFormat->isAttributeAllowed();
        } catch (DateFormatNotFoundException $e) {
            throw new BadRequestHttpException('Invalid startDateFormatId');
        }

        try {
            $startDate = Carbon::createFromFormat($startDatePhpFormat, $startDate);
            if (!$startDateFormat->hasMonth() && !$startDateFormat->hasDay()) {
                $startDate->firstOfYear();
            } elseif (!$startDateFormat->hasDay()) {
                $startDate->firstOfMonth();
            }
            $startDate->setTime(0, 0, 0);
        } catch (\InvalidArgumentException $e) {
            throw new BadRequestHttpException('Invalid startDate');
        }

        // start date attribute should only be set when it is allowed
        if (!$isStartDateAttributeAllowed && $startDateAttributeId !== null) {
            throw new BadRequestHttpException('startDateAttributeId is not allowed here');
        }

        if ($startDateAttributeId !== null) {
            try {
                $this->dateAttributeRepository->getById($startDateAttributeId);
            } catch (DateAttributeNotFoundException $e) {
                throw new BadRequestHttpException('Invalid startDateAttributeId');
            }
        }

        // endDateFormatId must be provided if endDate is provided
        if ($endDate !== null && $endDateFormatId === null) {
            throw new BadRequestHttpException('Missing endDateFormatId');
        }

        $endDatePhpFormat = null;
        $isEndDateAttributeAllowed = false;
        if ($endDateFormatId !== null) {
            try {
                $endDateFormat = $this->dateFormatRepository
                    ->getById($endDateFormatId);
                $endDatePhpFormat = $endDateFormat->getPhpFormat();
                $isEndDateAttributeAllowed = $endDateFormat->isAttributeAllowed();
            } catch (DateFormatNotFoundException $e) {
                throw new BadRequestHttpException('Invalid endDateFormatId');
            }

            // end date attribute should only be set when it is allowed
            if (!$isEndDateAttributeAllowed && $endDateAttributeId !== null) {
                throw new BadRequestHttpException('endDateAttributeId is not allowed here');
            }

            if ($endDateAttributeId !== null) {
                try {
                    $this->dateAttributeRepository->getById($endDateAttributeId);
                } catch (DateAttributeNotFoundException $e) {
                    throw new BadRequestHttpException('Invalid endDateAttributeId');
                }
            }

            try {
                $endDate = Carbon::createFromFormat($endDatePhpFormat, $endDate);
                if (!$endDateFormat->hasMonth() && !$endDateFormat->hasDay()) {
                    $endDate->lastOfYear();
                } elseif (!$endDateFormat->hasDay()) {
                    $endDate->lastOfMonth();
                }
                $endDate->setTime(23, 59, 59);
            } catch (\InvalidArgumentException $e) {
                throw new BadRequestHttpException('Invalid endDate');
            }
        }

        if ($periodId !== null) {
            try {
                $this->periodRepository->getById($periodId);
            } catch (PeriodNotFoundException $e) {
                throw new BadRequestHttpException('Invalid periodId');
            }
        }

        if(intval($startDateFormatId) === 1 && $startDateAttributeId === null) {
            $startDateAttributeId = 1;
        }

        if(intval($endDateFormatId) === 1 && $endDateAttributeId === null) {
            $endDateAttributeId = 1;
        }

        $event = $this->eventRepository->create(
            $startDate,
            $content,
            $startDateFormatId,
            Auth::user()->getId(),
            $endDateFormatId,
            $startDateAttributeId,
            $endDate,
            $endDateAttributeId,
            $periodId,
            $catalogIds,
            $images
        );

        Elasticsearch::index($event->toEsArray());

        return $event;
    }

    public function deleteById($id)
    {
        try {
            $this->eventRepository->deleteById(intval($id));
            Elasticsearch::delete([
                'index' => 'timelines',
                'type' => 'event',
                'id' => intval($id)
            ]);
            Artisan::call('timeline:generate');
            return response()->json(true);
        } catch (\Exception $e) {
            return response()->json(false);
        }
    }
}
