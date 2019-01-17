<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 9:39 PM
 */

namespace App\Timeline\Domain\ValueObjects;


abstract class SingleString
{
    /**
     * @var string
     */
    private $value;

    /**
     * SingleString constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        $this->validation($value);
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function validation(string $value): void {
        // validate value
    }

    public function __toString()
    {
        return $this->value;
    }
}