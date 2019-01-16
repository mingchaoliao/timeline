<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 6/21/18
 * Time: 9:24 PM
 */

namespace App\Timeline\Infrastructure\Persistence\Eloquent\Repositories;

use App\Timeline\Domain\Collections\ImageCollection;
use App\Timeline\Domain\Models\Image;
use App\Timeline\Domain\Models\Event;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentImage;
use Illuminate\Database\Eloquent\Collection;

class EloquentImageRepository extends EloquentBaseRepository
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