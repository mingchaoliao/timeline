<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/8/19
 * Time: 9:36 AM
 */

namespace App\Timeline\Domain\Models;


use App\Timeline\Utils\JsonSerializable;

class Typeahead extends BaseModel
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $value;

    /**
     * Typeahead constructor.
     * @param int $id
     * @param string $value
     */
    public function __construct(int $id, string $value)
    {
        $this->id = $id;
        $this->value = $value;
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
    public function getValue(): string
    {
        return $this->value;
    }

    public function toValueArray(): array
    {
        return [
            'id' => $this->getId(),
            'value' => $this->getValue()
        ];
    }
}