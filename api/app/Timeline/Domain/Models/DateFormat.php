<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:45 PM
 */

namespace App\Timeline\Domain\Models;


use Illuminate\Support\Carbon;

class DateFormat extends AbstractBase
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $mysqlFormat;
    /**
     * @var string
     */
    private $phpFormat;
    /**
     * @var bool
     */
    private $hasYear;
    /**
     * @var bool
     */
    private $hasMonth;
    /**
     * @var bool
     */
    private $hasDay;
    /**
     * @var bool
     */
    private $isAttributeAllowed;
    /**
     * @var Carbon
     */
    private $createdAt;
    /**
     * @var Carbon
     */
    private $updatedAt;

    /**
     * DateFormat constructor.
     *
     * @param int $id
     * @param string $mysqlFormat
     * @param string $phpFormat
     * @param bool $hasYear
     * @param bool $hasMonth
     * @param bool $hasDay
     * @param bool $isAttributeAllowed
     * @param Carbon $createdAt
     * @param Carbon $updatedAt
     */
    public function __construct(
        int $id,
        string $mysqlFormat,
        string $phpFormat,
        bool $hasYear,
        bool $hasMonth,
        bool $hasDay,
        bool $isAttributeAllowed,
        Carbon $createdAt,
        Carbon $updatedAt
    ) {
        $this->id = $id;
        $this->mysqlFormat = $mysqlFormat;
        $this->phpFormat = $phpFormat;
        $this->hasYear = $hasYear;
        $this->hasMonth = $hasMonth;
        $this->hasDay = $hasDay;
        $this->isAttributeAllowed = $isAttributeAllowed;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getMysqlFormat(): string
    {
        return $this->mysqlFormat;
    }

    /**
     * @return string
     */
    public function getPhpFormat(): string
    {
        return $this->phpFormat;
    }

    /**
     * @return bool
     */
    public function hasYear(): bool
    {
        return $this->hasYear;
    }

    /**
     * @return bool
     */
    public function hasMonth(): bool
    {
        return $this->hasMonth;
    }

    /**
     * @return bool
     */
    public function hasDay(): bool
    {
        return $this->hasDay;
    }


    /**
     * @return bool
     */
    public function isAttributeAllowed(): bool
    {
        return $this->isAttributeAllowed;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    /**
     * @return Carbon
     */
    public function getUpdatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'mysqlFormat' => $this->getMysqlFormat(),
            'phpFormat' => $this->getPhpFormat(),
            'isAttributeAllowed' => $this->isAttributeAllowed(),
            'createdAt' => $this->getCreatedAt()->format(DATE_ISO8601),
            'updatedAt' => $this->getUpdatedAt()->format(DATE_ISO8601)
        ];
    }
}