<?php

namespace App\Jobs;

use App\Timeline\Domain\Collections\ImageCollection;
use App\Timeline\Domain\Services\ImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LinkImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var ImageCollection
     */
    private $images;

    /**
     * LinkImages constructor.
     * @param ImageCollection $images
     */
    public function __construct(ImageCollection $images)
    {
        $this->images = $images;
    }

    /**
     * Execute the job.
     *
     * @param ImageService $imageService
     * @return void
     */
    public function handle(ImageService $imageService)
    {
        $imageService->publishImages($this->images);
    }
}
