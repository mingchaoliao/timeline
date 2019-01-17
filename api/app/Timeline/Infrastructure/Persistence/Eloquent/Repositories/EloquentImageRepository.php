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
use App\Timeline\Domain\Models\TemporaryImage;
use App\Timeline\Domain\Repositories\ImageRepository;
use App\Timeline\Domain\ValueObjects\ImageId;
use App\Timeline\Domain\ValueObjects\UserId;
use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentImage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;

class EloquentImageRepository implements ImageRepository
{
    /**
     * @var EloquentImage
     */
    private $imageModel;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * EloquentImageRepository constructor.
     * @param EloquentImage $imageModel
     */
    public function __construct(EloquentImage $imageModel)
    {
        $this->imageModel = $imageModel;
    }

    /**
     * @param TemporaryImage $tempImage
     * @param UserId $createUserId
     * @return EloquentImage
     * @throws TimelineException
     */
    public function createRaw(TemporaryImage $tempImage, UserId $createUserId): EloquentImage
    {
        try {
            if (!$this->fs->exists(Image::TMP_PATH . '/' . $tempImage->getPath())) {
                throw TimelineException::ofTemporaryImagePathDoesNotExist($tempImage->getPath());
            }

            $eloquentImage = $this->imageModel->create([
                'path' => $tempImage->getPath(),
                'description' => $tempImage->getDescription(),
                'create_user_id' => $createUserId->getValue(),
                'update_user_id' => $createUserId->getValue()
            ]);

            return $eloquentImage;
        } catch (QueryException $e) {
            /** @var \PDOException $pdoException */
            $pdoException = $e->getPrevious();
            $errorInfo = $pdoException->errorInfo;

            if ($errorInfo['1'] === 1062) { // duplicated value
                throw TimelineException::ofDuplicatedTemporaryImagePath($tempImage->getPath());
            } elseif ($errorInfo['1'] === 1452) { // non-exist user id
                throw TimelineException::ofUserWithIdDoesNotExist($createUserId);
            }

            throw $e;
        }
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
        return $this->constructImageCollection(
            $this->catalogModel->findMany($ids->toValueArray())
        );
    }
}