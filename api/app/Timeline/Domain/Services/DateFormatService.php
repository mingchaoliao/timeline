<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/16/19
 * Time: 9:29 PM
 */

namespace App\Timeline\Domain\Services;


use App\Timeline\Domain\Collections\DateFormatCollection;
use App\Timeline\Domain\Repositories\DateFormatRepository;
use App\Timeline\Exceptions\TimelineException;

class DateFormatService
{
    /**
     * @var DateFormatRepository
     */
    private $dateFormatRepository;

    /**
     * DateFormatService constructor.
     * @param DateFormatRepository $dateFormatRepository
     */
    public function __construct(DateFormatRepository $dateFormatRepository)
    {
        $this->dateFormatRepository = $dateFormatRepository;
    }

    /**
     * @return DateFormatCollection
     * @throws TimelineException
     */
    public function getAll(): DateFormatCollection
    {
        try {
            return $this->dateFormatRepository->getAll();
        } catch (TimelineException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw TimelineException::ofUnableToRetrieveDateFormats();
        }
    }

}