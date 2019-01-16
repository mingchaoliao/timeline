<?php
namespace App\Exceptions;

use Exception;

class HttpException extends Exception
{
    /**
     * @var bool
     */
    private $isJson = false;
    /**
     * @return bool
     */
    public function isJson(): bool
    {
        return $this->isJson;
    }
    /**
     * @param bool $isJson
     */
    public function setIsJson(bool $isJson): void
    {
        $this->isJson = $isJson;
    }
    public static function withJsonMessage(string $message = '', int $code = 0): self
    {
        $exception = new static(
            $message,
            $code
        );
        $exception->setIsJson(true);
        return $exception;
    }
}