<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/5/19
 * Time: 4:23 PM
 */

namespace App\Timeline\Exceptions;


use Illuminate\Support\MessageBag;
use Throwable;

class InvalidArgumentException extends TimelineException
{
    private const CREATE_FROM_MESSAGE_BAG = 20000;

    /**
     * @var array
     */
    private $messages;

    public function __construct(string $message = "", array $messages = [], int $code = 0, int $statusCode = 500, array $headers = [], Throwable $previous = null)
    {
        parent::__construct($message, $code, $statusCode, $headers, $previous);
        $this->messages = $messages;
    }

    public static function createFromMessageBag(MessageBag $bag, Throwable $previous = null)
    {
        $messages = $bag->messages();

        foreach ($messages as &$message) {
            $message = $message[0] ?? '';
        }

        return new static(
            $bag->first(),
            $messages,
            static::CREATE_FROM_MESSAGE_BAG,
            400,
            [],
            $previous
        );
    }
}