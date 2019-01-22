<?php

namespace App\Events;

use App\Timeline\Domain\ValueObjects\CatalogId;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimelineCatalogUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var CatalogId
     */
    private $catalogId;

    /**
     * TimelineCatalogUpdated constructor.
     * @param CatalogId $catalogId
     */
    public function __construct(CatalogId $catalogId)
    {
        $this->catalogId = $catalogId;
    }

    /**
     * @return CatalogId
     */
    public function getCatalogId(): CatalogId
    {
        return $this->catalogId;
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
