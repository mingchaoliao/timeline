<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 9:15 PM
 */

namespace App\Timeline\Domain\Repositories;


use App\Timeline\Domain\Collections\CreateEventRequestCollection;
use App\Timeline\Domain\Collections\EventCollection;
use App\Timeline\Domain\Collections\EventIdCollection;
use App\Timeline\Domain\Models\Event;
use App\Timeline\Domain\Requests\CreateEventRequest;
use App\Timeline\Domain\Requests\PageableRequest;
use App\Timeline\Domain\Requests\UpdateEventRequest;
use App\Timeline\Domain\ValueObjects\CatalogId;
use App\Timeline\Domain\ValueObjects\EventId;
use App\Timeline\Domain\ValueObjects\PeriodId;
use App\Timeline\Domain\ValueObjects\UserId;

interface EventRepository
{
    public function getById(EventId $id): Event;

    public function getByIds(EventIdCollection $ids): EventCollection;

    public function getByPeriodId(PeriodId $id): EventCollection;

    public function getByCatalogId(CatalogId $id): EventCollection;

    public function getAll(): EventCollection;

    public function get(PageableRequest $request): EventCollection;

    public function create(
        CreateEventRequest $request,
        UserId $createUserId
    ): Event;

    public function bulkCreate(
        CreateEventRequestCollection $requests,
        UserId $createUserId
    ): EventCollection;

    public function update(
        EventId $id,
        UpdateEventRequest $request,
        UserId $updateUserId
    ): Event;

    public function delete(EventId $id): bool;
}