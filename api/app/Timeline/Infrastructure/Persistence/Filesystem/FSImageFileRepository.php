<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/21/19
 * Time: 9:30 PM
 */

namespace App\Timeline\Infrastructure\Persistence\Filesystem;


use App\Timeline\Domain\Collections\ImageCollection;
use App\Timeline\Domain\Models\Image;
use App\Timeline\Domain\Repositories\ImageFileRepository;

class FSImageFileRepository extends BaseFsRepository implements ImageFileRepository
{
    private const TEMP_IMAGE_DIR = 'images';
    private const IMAGE_DIR = 'public/images';

    public function publishImageFiles(ImageCollection $images): void
    {
        /** @var Image $image */
        foreach ($images as $image) {
            $tempPath = $this->getImagePath(self::TEMP_IMAGE_DIR, $image);
            if ($this->getFs()->exists($tempPath)) {
                $this->getFs()
                    ->move(
                        $tempPath,
                        $this->getImagePath(self::IMAGE_DIR, $image)
                    );
            }
        }
    }

    public function deleteImageFiles(ImageCollection $images): void
    {
        /** @var Image $image */
        foreach ($images as $image) {
            $path = $this->getImagePath(self::IMAGE_DIR, $image);
            if ($this->getFs()->exists($path)) {
                $this->getFs()->delete($path);
            }
        }
    }

    public function deleteTemporaryImageFiles(ImageCollection $images): void
    {
        /** @var Image $image */
        foreach ($images as $image) {
            $path = $this->getImagePath(self::TEMP_IMAGE_DIR, $image);
            if ($this->getFs()->exists($path)) {
                $this->getFs()->delete($path);
            }
        }
    }

    private function getImagePath(string $dir, Image $image): string
    {
        return sprintf(
            '%s/%s',
            $dir,
            $image->getPath()
        );
    }
}