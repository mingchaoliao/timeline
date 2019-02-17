<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/17/19
 * Time: 12:41 AM
 */

namespace App\Timeline\Domain\Models;


use Carbon\Carbon;

class Backup extends BaseModel
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var int
     */
    private $size;
    /**
     * @var Carbon
     */
    private $date;

    /**
     * Backup constructor.
     * @param string $name
     * @param int $size
     * @param Carbon $date
     */
    public function __construct(string $name, int $size, Carbon $date)
    {
        $this->name = $name;
        $this->size = $size;
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return Carbon
     */
    public function getDate(): Carbon
    {
        return $this->date;
    }

    public function toValueArray(): array
    {
        return [
            'name' => $this->getName(),
            'size' => $this->getSize(),
            'date' => $this->getDate()->toIso8601String()
        ];
    }
}