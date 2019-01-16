<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 5:19 PM
 */

namespace App\Common;


use App\Timeline\Exceptions\UnauthenticatedException;
use App\Timeline\Exceptions\UnauthorizedException;
use Illuminate\Support\Facades\Auth;

class Authorization
{
    /**
     * @throws UnauthenticatedException
     * @throws UnauthorizedException
     */
    public function requireAdminAccess()
    {
        $this->requireLoggedIn();

        if (!Auth::user()->isAdmin()) {
            throw new UnauthorizedException();
        }
    }

    /**
     * @throws UnauthenticatedException
     */
    private function requireLoggedIn()
    {
        if (!Auth::check()) {
            throw new UnauthenticatedException();
        }
    }
}