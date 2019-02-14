<?php

namespace App\Events;

use App\Timeline\Domain\Collections\EventCollection;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TimelineEventsCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var EventCollection
     */
    private $events;

    /**
     * TimelineEventsCreated constructor.
     * @param EventCollection $events
     */
    public function __construct(EventCollection $events)
    {
        $this->events = $events;
    }

    /**
     * @return EventCollection
     */
    public function getEvents(): EventCollection
    {
        return $this->events;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
