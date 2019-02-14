<?php

namespace App\Events;

use App\Timeline\Domain\ValueObjects\EventId;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TimelineEventDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var EventId
     */
    private $eventId;

    /**
     * Create a new event instance.
     *
     * @param EventId $eventId
     */
    public function __construct(EventId $eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * @return EventId
     */
    public function getEventId(): EventId
    {
        return $this->eventId;
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
