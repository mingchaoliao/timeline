<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:46 PM
 */

namespace App\Timeline\Domain\Collections;


use App\Timeline\Domain\Models\Event;

class EventCollection extends BaseCollection
{
    public function toTimelineArray(): array {
        return $this->map(function(Event $event) {
            return $event->toTimelineArray();
        })->toArray();
    }
}