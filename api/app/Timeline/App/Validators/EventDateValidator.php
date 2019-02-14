<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/28/19
 * Time: 7:06 PM
 */

namespace App\Timeline\App\Validators;


use App\Timeline\Domain\Models\EventDate;
use App\Timeline\Exceptions\TimelineException;

class EventDateValidator
{
    public function validate($attribute, $value, $parameters, $validator)
    {
        try {
            EventDate::validate($value);
        } catch (TimelineException $e) {
            return false;
        }

        return true;
    }

    public function message($message, $attribute, $rule, $parameters)
    {
        return sprintf(
            'The %s must be a valid event date',
            $attribute
        );
    }
}