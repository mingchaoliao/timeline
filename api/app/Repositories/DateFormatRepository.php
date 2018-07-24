<?php
/**
 * Created by PhpStorm.
 * User: ubuntu
 * Date: 6/21/18
 * Time: 9:24 PM
 */

namespace App\Repositories;


use App\DomainModels\Collections\DateFormatCollection;
use App\DomainModels\DateFormat;
use App\EloquentModels\EloquentDateFormat;
use App\Exceptions\DateFormatNotFoundException;
use Illuminate\Database\Eloquent\Collection;

class DateFormatRepository extends BaseRepository
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