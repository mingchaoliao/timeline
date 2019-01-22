<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/21/19
 * Time: 9:53 PM
 */

namespace App\Timeline\Domain\Services;


use App\Timeline\Domain\Collections\ImageCollection;
use App\Timeline\Domain\Repositories\ImageFileRepository;
use App\Timeline\Domain\Repositories\ImageRepository;

class ImageService
{
    /**
     * @var ImageRepository
     */
    private $imageRepository;
    /**
     * @var ImageFileRepository
     */
    private $imageFileRepository;

    /**
     * ImageService constructor.
     * @param ImageRepository $imageRepository
     * @param ImageFileRepository $imageFileRepository
     */
    public function __construct(ImageRepository $imageRepository, ImageFileRepository $imageFileRepository)
    {
        $this->imageRepository = $imageRepository;
        $this->imageFileRepository = $imageFileRepository;
    }

    public function publishImages(ImageCollection $images): void {
        $this->imageFileRepository->publishImageFiles($images);
    }

    public function deletePublishedImages(ImageCollection $images): void {
        $this->imageFileRepository->deleteImageFiles($images);
    }

    public function cleanUnusedImagesFor(int $days): void {
        $unusedImages = $this->imageRepository->getUnusedImagesFor($days);
        $this->imageFileRepository->deleteTemporaryImageFiles($unusedImages);
        $this->imageRepository->deleteImages($unusedImages);
    }
}