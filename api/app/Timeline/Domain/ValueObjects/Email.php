<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/16/19
 * Time: 5:26 PM
 */

namespace App\Timeline\Domain\ValueObjects;


use App\Timeline\Exceptions\TimelineException;

final class Email extends SingleString
{
    public function validation(string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw TimelineException::ofInvalidEmail($value);
        }
    }
}