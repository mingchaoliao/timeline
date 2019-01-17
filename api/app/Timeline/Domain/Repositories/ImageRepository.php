<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 9:15 PM
 */

namespace App\Timeline\Domain\Repositories;


use App\Timeline\Domain\Collections\ImageIdCollection;
use App\Timeline\Domain\Models\TemporaryImage;
use App\Timeline\Domain\ValueObjects\UserId;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentImage;
use Illuminate\Database\Eloquent\Collection;

interface ImageRepository
{
    public function getRawByIds(ImageIdCollection $ids): Collection;
    public function createRaw(TemporaryImage $tempImage, UserId $createUserId): EloquentImage;
}