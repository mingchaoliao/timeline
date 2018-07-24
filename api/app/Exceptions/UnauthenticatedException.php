<?php

namespace App\Exceptions;

use Exception;

class UnauthenticatedException extends Exception
{
    protected $code = 401;
}
