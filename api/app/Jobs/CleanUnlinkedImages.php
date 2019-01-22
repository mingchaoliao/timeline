<?php

namespace App\Jobs;

use App\Timeline\Domain\Collections\ImageCollection;
use App\Timeline\Domain\Services\ImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanUnlinkedImages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var ImageCollection|null
     */
    private $images;

    /**
     * CleanUnlinkedImages constructor.
     * @param ImageCollection|null $images
     */
    public function __construct(ImageCollection $images = null)
    {
        $this->images = $images;
    }

    /**
     * Execute the job.
     *
     * @param ImageService $imageServices
     * @return void
     */
    public function handle(ImageService $imageServices)
    {
        if ($this->images === null) {
            $imageServices->cleanUnusedImagesFor(1);
        } else {
            $imageServices->deletePublishedImages($this->images);
        }
    }
}
