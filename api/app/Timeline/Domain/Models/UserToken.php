<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/16/19
 * Time: 6:51 PM
 */

namespace App\Timeline\Domain\Models;


class UserToken extends BaseModel
{
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $token;

    /**
     * UserToken constructor.
     * @param string $type
     * @param string $token
     */
    public function __construct(string $type, string $token)
    {
        $this->type = $type;
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    public function toValueArray(): array
    {
        return [
            'type' => $this->getType(),
            'token' => $this->getToken()
        ];
    }
}