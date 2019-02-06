<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/5/19
 * Time: 1:28 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Models\User;
use App\Timeline\Domain\ValueObjects\Email;
use App\Timeline\Domain\ValueObjects\UserId;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\Models\User
 */
class UserTest extends TestCase
{
    public function testConvertModelToArray()
    {
        $date = Carbon::create(2018, 1, 1, 0, 0, 0);
        $modelId = new UserId(1);
        $model = new User(
            $modelId,
            'name',
            new Email('name@test.com'),
            true,
            false,
            $date,
            $date
        );
        $this->assertSame([
            'id' => 1,
            'name' => 'name',
            'email' => 'name@test.com',
            'isAdmin' => true,
            'isEditor' => false,
            'createdAt' => $date->toIso8601String(),
            'updatedAt' => $date->toIso8601String()
        ], $model->toValueArray());
    }
}