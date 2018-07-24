<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 6/21/18
 * Time: 9:24 PM
 */

namespace App\Repositories;

use App\DomainModels\Collections\ImageCollection;
use App\DomainModels\Image;
use App\DomainModels\Event;
use App\EloquentModels\EloquentImage;
use Illuminate\Database\Eloquent\Collection;

class ImageRepository extends BaseRepository
{
    public function constructImage(EloquentImage $eloquentImage): Image
    {
        return new Image(
            $eloquentImage->getId(),
            $eloquentImage->getPath(),
            $eloquentImage->getDescription(),
            $eloquentImage->getCreateUserId(),
            $eloquentImage->getUpdateUserId(),
            $eloquentImage->getCreatedAt(),
            $eloquentImage->getUpdatedAt()
        );
    }

    public function constructImageCollection(Collection $collection): ImageCollection
    {
        $results = new ImageCollection();
        foreach ($collection as $item) {
            $results->push($this->constructImage($item));
        }
        return $results;
    }
}