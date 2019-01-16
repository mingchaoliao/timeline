<?php

namespace App\Timeline\Exceptions;

use Exception;

class UnauthorizedException extends Exception
{
    protected $code = 401;
}
