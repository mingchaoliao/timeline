<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 9:37 PM
 */

namespace App\Timeline\Domain\ValueObjects;


abstract class SingleInteger
{
    /**
     * @var int
     */
    private $value;

    /**
     * SingleInteger constructor.
     * @param int $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    public function __toString()
    {
        return (string)$this->value;
    }
}