<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 9:15 PM
 */

namespace App\Timeline\Domain\Repositories;


use App\Timeline\Domain\Collections\ImageCollection;

interface ImageRepository
{
    public function getUnusedImagesFor(int $days): ImageCollection;

    public function deleteImages(ImageCollection $images): void;
}