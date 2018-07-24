<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 5:01 PM
 */

namespace App\Repositories;


use App\Common\Authorization;

class BaseRepository
{
    /**
     * @var Authorization
     */
    protected $authorization;

    /**
     * BaseRepository constructor.
     *
     * @param Authorization $authorization
     */
    public function __construct(Authorization $authorization)
    {
        $this->authorization = $authorization;
    }


}