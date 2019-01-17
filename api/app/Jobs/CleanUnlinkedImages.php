<?php

namespace App\Jobs;

use App\Timeline\Domain\Collections\ImageCollection;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CleanUnlinkedImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var ImageCollection
     */
    private $images;

    /**
     * Create a new job instance.
     *
     * @param ImageCollection $images
     */
    public function __construct(ImageCollection $images = null)
    {
        $this->images = $images;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }
}
