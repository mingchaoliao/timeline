<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/5/19
 * Time: 1:28 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Models\Catalog;
use App\Timeline\Domain\ValueObjects\CatalogId;
use App\Timeline\Domain\ValueObjects\UserId;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\Models\Catalog
 */
class CatalogTest extends TestCase
{
    public function testConvertModelToArray()
    {
        $date = Carbon::create(2018, 1, 1, 0, 0, 0);
        $userId = new UserId(1);
        $modelId = new CatalogId(1);
        $model = new Catalog(
            $modelId,
            'a',
            1,
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
            'numberOfEvents' => 1,
            'createUserId' => 1,
            'createUserName' => 'user1',
            'updateUserId' => 1,
            'updateUserName' => 'user1',
            'createdAt' => $date->toIso8601String(),
            'updatedAt' => $date->toIso8601String()
        ], $model->toValueArray());
    }
}