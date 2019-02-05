<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/28/19
 * Time: 7:06 PM
 */

namespace App\Timeline\App\Validators;


use App\Timeline\Utils\Common;

class IDValidator
{
    public function validate($attribute, $value, $parameters, $validator)
    {
        return Common::isPosInt($value);
    }

    public function message($message, $attribute, $rule, $parameters)
    {
        return sprintf(
            'The %s must be a valid ID',
            $attribute
        );
    }
}