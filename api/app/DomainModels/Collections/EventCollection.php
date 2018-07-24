<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:46 PM
 */

namespace App\DomainModels\Collections;


use App\DomainModels\Event;

class EventCollection extends BaseCollection
{
    public function toTimelineArray(): array {
        return $this->map(function(Event $event) {
            return $event->toTimelineArray();
        })->toArray();
    }
}