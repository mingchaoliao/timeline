<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/5/19
 * Time: 1:28 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Models\Image;
use App\Timeline\Domain\ValueObjects\EventId;
use App\Timeline\Domain\ValueObjects\ImageId;
use App\Timeline\Domain\ValueObjects\UserId;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\Models\Image
 */
class ImageTest extends TestCase
{
    public function testConvertModelToArray()
    {
        $date = Carbon::create(2018, 1, 1, 0, 0, 0);
        $userId = new UserId(1);
        $modelId = new ImageId(1);
        $model = new Image(
            $modelId,
            '20190123-asdflk.jpg',
            null,
            'a.jpg',
            null,
            $userId,
            $userId,
            $date,
            $date
        );
        $this->assertSame([
            'id' => 1,
            'path' => '20190123-asdflk.jpg',
            'description' => null,
            'originalName' => 'a.jpg',
            'eventId' => null,
            'createUserId' => 1,
            'updateUserId' => 1,
            'createdAt' => $date->toIso8601String(),
            'updatedAt' => $date->toIso8601String()
        ], $model->toArray());
    }

    public function testConvertModelWithEventIdToArray()
    {
        $date = Carbon::create(2018, 1, 1, 0, 0, 0);
        $userId = new UserId(1);
        $modelId = new ImageId(1);
        $model = new Image(
            $modelId,
            '20190123-asdflk.jpg',
            null,
            'a.jpg',
            new EventId(3),
            $userId,
            $userId,
            $date,
            $date
        );
        $this->assertSame([
            'id' => 1,
            'path' => '20190123-asdflk.jpg',
            'description' => null,
            'originalName' => 'a.jpg',
            'eventId' => 3,
            'createUserId' => 1,
            'updateUserId' => 1,
            'createdAt' => $date->toIso8601String(),
            'updatedAt' => $date->toIso8601String()
        ], $model->toArray());
    }
}