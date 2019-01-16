<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 6/21/18
 * Time: 9:24 PM
 */

namespace App\Timeline\Infrastructure\Persistence\Eloquent\Repositories;


use App\Timeline\Domain\Collections\DateFormatCollection;
use App\Timeline\Domain\Models\DateFormat;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentDateFormat;
use App\Timeline\Exceptions\DateFormatNotFoundException;
use Illuminate\Database\Eloquent\Collection;

class EloquentDateFormatRepository extends EloquentBaseRepository
{
    public function getCollection(): DateFormatCollection {
        $eloquentCollection = EloquentDateFormat::all();
        return $this->constructDateFormatCollection($eloquentCollection);
    }

    public function getById(int $id): DateFormat {
        $eloquentDateFormat = EloquentDateFormat::find($id);
        if($eloquentDateFormat === null) {
            throw new DateFormatNotFoundException();
        }
        return $this->constructDateFormat($eloquentDateFormat);
    }

    public function constructDateFormat(EloquentDateFormat $eloquentDateFormat): DateFormat
    {
        return new DateFormat(
            $eloquentDateFormat->getId(),
            $eloquentDateFormat->getMysqlFormat(),
            $eloquentDateFormat->getPhpFormat(),
            $eloquentDateFormat->hasYear(),
            $eloquentDateFormat->hasMonth(),
            $eloquentDateFormat->hasDay(),
            $eloquentDateFormat->isAttributeAllowed(),
            $eloquentDateFormat->getCreatedAt(),
            $eloquentDateFormat->getUpdatedAt()
        );
    }

    public function constructDateFormatCollection(Collection $collection): DateFormatCollection
    {
        $results = new DateFormatCollection();
        foreach ($collection as $item) {
            $results->push($this->constructDateFormat($item));
        }
        return $results;
    }
}