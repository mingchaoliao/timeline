<?php
/**
 * Author: liaom
 * Date: 6/21/18
 * Time: 4:46 PM
 */

namespace App\Timeline\Domain\Collections;


use App\Timeline\Domain\Models\User;

class UserCollection extends BaseCollection
{
    public function toJsonArray(): array {
        return $this->map(function(User $user) {
            return $user->toArray();
        })->toArray();
    }
}