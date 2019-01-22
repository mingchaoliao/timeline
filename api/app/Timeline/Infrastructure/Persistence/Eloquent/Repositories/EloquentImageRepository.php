<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 6/21/18
 * Time: 9:24 PM
 */

namespace App\Timeline\Infrastructure\Persistence\Eloquent\Repositories;

use App\Timeline\Domain\Collections\ImageCollection;
use App\Timeline\Domain\Collections\ImageIdCollection;
use App\Timeline\Domain\Models\Image;
use App\Timeline\Domain\Repositories\ImageRepository;
use App\Timeline\Domain\ValueObjects\ImageId;
use App\Timeline\Domain\ValueObjects\UserId;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentImage;
use Carbon\Carbon;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentImageRepository implements ImageRepository
{
    /**
     * @var EloquentImage
     */
    private $imageModel;

    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * EloquentImageRepository constructor.
     * @param EloquentImage $imageModel
     * @param ConnectionInterface $connection
     */
    public function __construct(EloquentImage $imageModel, ConnectionInterface $connection)
    {
        $this->imageModel = $imageModel;
        $this->connection = $connection;
    }

    public function constructImage(EloquentImage $eloquentImage): Image
    {
        return new Image(
            new ImageId($eloquentImage->getId()),
            $eloquentImage->getPath(),
            $eloquentImage->getDescription(),
            new UserId($eloquentImage->getCreateUserId()),
            new UserId($eloquentImage->getUpdateUserId()),
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

    public function getRawByIds(ImageIdCollection $ids): Collection
    {
        return $this->imageModel->findMany($ids->toValueArray());
    }

    public function getUnusedImagesFor(int $days): ImageCollection
    {
        $eloquentImages = $this->imageModel
            ->whereNull('event_id')
            ->where('updatedAt', '<', Carbon::now()->subDays($days))
            ->get();

        return $this->constructImageCollection($eloquentImages);
    }

    public function deleteImages(ImageCollection $images): void
    {
        $ids = new ImageIdCollection($images->map(function(Image $image) {
            return $image->getId();
        })->toArray());

        $this->connection->transaction(function () use ($ids) {
            $chunks = $ids->chunk(100);

            /** @var ImageIdCollection $chunk */
            foreach ($chunks as $chunk) {
                $this->imageModel->findMany($chunk->toValueArray())->get()->delete();
            }
        });
    }
}