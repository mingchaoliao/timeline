<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/21/19
 * Time: 6:57 AM
 */

namespace App\Timeline\Domain\Repositories;


use App\Timeline\Domain\Collections\EventCollection;
use App\Timeline\Domain\Collections\EventIdCollection;
use App\Timeline\Domain\Models\Event;
use App\Timeline\Domain\Requests\SearchEventRequest;
use App\Timeline\Domain\ValueObjects\EventId;

interface SearchEventRepository
{
    public function search(SearchEventRequest $request): EventIdCollection;
    public function index(Event $event): void;
    public function bulkIndex(EventCollection $events): void;
    public function deleteDocument(EventId $id): void;
}