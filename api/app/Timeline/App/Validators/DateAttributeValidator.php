<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/28/19
 * Time: 7:06 PM
 */

namespace App\Timeline\App\Validators;


use App\Timeline\Domain\Models\EventDate;

class DateAttributeValidator
{
    public function validate($attribute, $value, $parameters, $validator)
    {
        try {
            $dateStr = $validator->getData()[$parameters[0] ?? null] ?? null;

            if($dateStr === null) {
                return false;
            }

            $date = EventDate::createFromString($dateStr);

            return $date->isAttributeAllowed();
        } catch (\Exception $e) {
            return false;
        }
    }

    public function message($message, $attribute, $rule, $parameters)
    {
        return sprintf(
            '%s should not be provided when format of %s is not YYYY',
            $attribute,
            $parameters[0] ?? ''
        );
    }
}