<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/21/19
 * Time: 9:31 PM
 */

namespace App\Timeline\Infrastructure\Persistence\Filesystem;


use Illuminate\Contracts\Filesystem\Filesystem;

abstract class BaseFsRepository
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * BaseFsRepository constructor.
     * @param Filesystem $fs
     */
    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
    }

    /**
     * @return Filesystem
     */
    public function getFs(): Filesystem
    {
        return $this->fs;
    }
}