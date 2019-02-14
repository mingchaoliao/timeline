<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/5/19
 * Time: 4:26 PM
 */

namespace App\Timeline\App\Validators;


use App\Timeline\Exceptions\InvalidArgumentException;
use Illuminate\Contracts\Validation\Factory;

class ValidatorFactory
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * ValidatorFactory constructor.
     * @param Factory $factory
     */
    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     */
    public function validate(
        array $data,
        array $rules,
        array $messages = [],
        array $customAttributes = []
    ): void
    {
        foreach ($rules as &$rule) {
            if (strpos($rule, 'bail') === false) {
                $rule .= '|bail';
            }
        }

        $validator = $this->factory->make($data, $rules, $messages, $customAttributes);

        if ($validator->fails()) {
            throw InvalidArgumentException::createFromMessageBag($validator->errors());
        }
    }
}