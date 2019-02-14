<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/21/19
 * Time: 9:53 PM
 */

namespace App\Timeline\Domain\Services;


use App\Timeline\Domain\Collections\ImageCollection;
use App\Timeline\Domain\Models\Image;
use App\Timeline\Domain\Repositories\ImageFileRepository;
use App\Timeline\Domain\Repositories\ImageRepository;
use App\Timeline\Domain\ValueObjects\ImageId;
use Illuminate\Http\UploadedFile;

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
     * @var UserService
     */
    private $userService;

    /**
     * ImageService constructor.
     * @param ImageRepository $imageRepository
     * @param ImageFileRepository $imageFileRepository
     * @param UserService $userService
     */
    public function __construct(ImageRepository $imageRepository, ImageFileRepository $imageFileRepository, UserService $userService)
    {
        $this->imageRepository = $imageRepository;
        $this->imageFileRepository = $imageFileRepository;
        $this->userService = $userService;
    }

    public function publishImages(ImageCollection $images): void
    {
        $this->imageFileRepository->publishImageFiles($images);
    }

    public function deletePublishedImages(ImageCollection $images): void
    {
        $this->imageFileRepository->deleteImageFiles($images);
    }

    public function cleanUnusedImagesFor(int $days): void
    {
        $unusedImages = $this->imageRepository->getUnusedImagesFor($days);
        $this->imageFileRepository->deleteTemporaryImageFiles($unusedImages);
        $this->imageRepository->deleteImages($unusedImages);
    }

    public function update(ImageId $id, string $description): Image
    {
        $currentUser = $this->userService->getCurrentUser();
        return $this->imageRepository->update($id, $description, $currentUser->getId());
    }

    public function upload(UploadedFile $file, ?string $description): Image
    {
        $currentUser = $this->userService->getCurrentUser();
        $name = $this->imageFileRepository->upload($file);
        return $this->imageRepository->create(
            $name,
            $file->getClientOriginalName(),
            $description,
            $currentUser->getId()
        );
    }
}