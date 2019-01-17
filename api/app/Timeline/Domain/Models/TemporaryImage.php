<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/16/19
 * Time: 10:41 PM
 */

namespace App\Timeline\Domain\Models;


class TemporaryImage
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var string|null
     */
    private $description;

    /**
     * TemporaryImage constructor.
     * @param string $path
     * @param null|string $description
     */
    public function __construct(string $path, ?string $description)
    {
        $this->path = $path;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}