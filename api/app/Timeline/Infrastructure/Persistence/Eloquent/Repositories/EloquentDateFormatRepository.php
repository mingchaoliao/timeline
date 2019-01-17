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
use App\Timeline\Domain\Repositories\DateFormatRepository;
use App\Timeline\Domain\ValueObjects\DateFormatId;
use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentDateFormat;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;

class EloquentDateFormatRepository implements DateFormatRepository
{
    /**
     * @var EloquentDateFormat
     */
    private $dateFormatModel;

    /**
     * EloquentDateFormatRepository constructor.
     * @param EloquentDateFormat $dateFormatModel
     */
    public function __construct(EloquentDateFormat $dateFormatModel)
    {
        $this->dateFormatModel = $dateFormatModel;
    }

    /**
     * @return DateFormatCollection
     */
    public function getAll(): DateFormatCollection
    {
        $eloquentCollection = $this->dateFormatModel->all();

        return $this->constructDateFormatCollection($eloquentCollection);
    }

    public function constructDateFormat(EloquentDateFormat $eloquentDateFormat): DateFormat
    {
        return new DateFormat(
            new DateFormatId($eloquentDateFormat->getId()),
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

    private function constructDateFormatCollection(Collection $collection): DateFormatCollection
    {
        $results = new DateFormatCollection();
        foreach ($collection as $item) {
            $results->push($this->constructDateFormat($item));
        }
        return $results;
    }
}