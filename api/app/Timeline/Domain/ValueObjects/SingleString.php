<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/15/19
 * Time: 9:39 PM
 */

namespace App\Timeline\Domain\ValueObjects;


abstract class SingleString extends SingleValue
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

    public static function createFromString(?string $value): ?self
    {
        if($value === null) {
            return null;
        }

        return new static($value);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public abstract function validation(string $value): void;

    public function __toString()
    {
        return $this->value;
    }
}