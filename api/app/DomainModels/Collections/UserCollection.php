<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:46 PM
 */

namespace App\DomainModels\Collections;


use App\DomainModels\User;

class UserCollection extends BaseCollection
{
    public function toJsonArray(): array {
        return $this->map(function(User $user) {
            return $user->toArray();
        })->toArray();
    }
}