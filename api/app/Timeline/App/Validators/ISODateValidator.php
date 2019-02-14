<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/28/19
 * Time: 7:06 PM
 */

namespace App\Timeline\App\Validators;


use Carbon\Carbon;

class ISODateValidator
{
    public function validate($attribute, $value, $parameters, $validator)
    {
        try {
            return Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d') === $value;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function message($message, $attribute, $rule, $parameters)
    {
        return sprintf(
            'The %s must be a valid date with format YYYY-MM-DD',
            $attribute
        );
    }
}