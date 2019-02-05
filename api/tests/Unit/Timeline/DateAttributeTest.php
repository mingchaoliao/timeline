<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/5/19
 * Time: 1:28 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Models\DateAttribute;
use App\Timeline\Domain\ValueObjects\DateAttributeId;
use App\Timeline\Domain\ValueObjects\UserId;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\Models\DateAttribute
 */
class DateAttributeTest extends TestCase
{
    public function testConvertModelToArray()
    {
        $date = Carbon::create(2018, 1, 1, 0, 0, 0);
        $userId = new UserId(1);
        $modelId = new DateAttributeId(1);
        $model = new DateAttribute(
            $modelId,
            'a',
            $userId,
            'user1',
            $userId,
            'user1',
            $date,
            $date
        );
        $this->assertSame([
            'id' => 1,
            'value' => 'a',
            'createUserId' => 1,
            'createUserName' => 'user1',
            'updateUserId' => 1,
            'updateUserName' => 'user1',
            'createdAt' => $date->toIso8601String(),
            'updatedAt' => $date->toIso8601String()
        ], $model->toArray());
    }
}