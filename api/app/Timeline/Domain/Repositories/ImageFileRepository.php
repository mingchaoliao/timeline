<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/21/19
 * Time: 9:26 PM
 */

namespace App\Timeline\Domain\Repositories;


use App\Timeline\Domain\Collections\ImageCollection;

interface ImageFileRepository
{
    public function publishImageFiles(ImageCollection $images): void;

    public function deleteImageFiles(ImageCollection $images): void;

    public function deleteTemporaryImageFiles(ImageCollection $images): void;
}