<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/5/19
 * Time: 1:28 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Models\UserToken;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\Models\UserToken
 */
class UserTokenTest extends TestCase
{
    public function testConvertModelToArray()
    {
        $model = new UserToken(
            'bearer',
            'abcd'
        );
        $this->assertSame([
            'type' => 'bearer',
            'token' => 'abcd'
        ], $model->toValueArray());
    }
}