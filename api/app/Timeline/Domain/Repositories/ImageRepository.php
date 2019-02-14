<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 9:15 PM
 */

namespace App\Timeline\Domain\Repositories;


use App\Timeline\Domain\Collections\ImageCollection;
use App\Timeline\Domain\Models\Image;
use App\Timeline\Domain\ValueObjects\ImageId;
use App\Timeline\Domain\ValueObjects\UserId;

interface ImageRepository
{
    public function update(ImageId $id, string $description, UserId $updateUserId): Image;

    public function getUnusedImagesFor(int $days): ImageCollection;

    public function deleteImages(ImageCollection $images): void;

    public function create(string $name, string $originalName, ?string $description, UserId $createUserId): Image;
}