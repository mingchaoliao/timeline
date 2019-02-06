<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/25/19
 * Time: 8:59 PM
 */

namespace App\Timeline\Domain\Models;


class Bucket extends BaseModel
{
    /**
     * @var string
     */
    private $value;
    /**
     * @var int
     */
    private $count;

    /**
     * Bucket constructor.
     * @param string $value
     * @param int $count
     */
    public function __construct(string $value, int $count)
    {
        $this->value = $value;
        $this->count = $count;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    public function toValueArray(): array
    {
        return [
            'value' => $this->getValue(),
            'count' => $this->getCount()
        ];
    }
}