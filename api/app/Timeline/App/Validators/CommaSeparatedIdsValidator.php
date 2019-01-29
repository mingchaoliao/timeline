<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/28/19
 * Time: 7:06 PM
 */

namespace App\Timeline\App\Validators;


use App\Timeline\Utils\Common;

class CommaSeparatedIdsValidator
{
    public function validate($attribute, $value, $parameters, $validator)
    {
        $ids = explode(',', $value);

        foreach ($ids as $id) {
            if (!Common::isPosInt($id)) {
                return false;
            }
        }

        return true;
    }

    public function message($message, $attribute, $rule, $parameters) {
        return sprintf(
            'The %s must be positive integers separated by comma',
            $attribute
        );
    }
}