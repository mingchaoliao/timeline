<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:45 PM
 */

namespace App\DomainModels;

class PeriodTypeahead extends AbstractBase
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $value;

    /**
     * PeriodTypeahead constructor.
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

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'value' => $this->getValue()
        ];
    }
}