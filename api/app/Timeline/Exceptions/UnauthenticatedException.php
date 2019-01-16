<?php

namespace App\Timeline\Exceptions;

use Exception;

class UnauthenticatedException extends Exception
{
    protected $code = 401;
}
