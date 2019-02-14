<?php

namespace App\Events;

use App\Timeline\Domain\ValueObjects\PeriodId;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TimelinePeriodUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var PeriodId
     */
    private $periodId;

    /**
     * Create a new event instance.
     *
     * @param PeriodId $id
     */
    public function __construct(PeriodId $id)
    {
        $this->periodId = $id;
    }

    /**
     * @return PeriodId
     */
    public function getPeriodId(): PeriodId
    {
        return $this->periodId;
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
