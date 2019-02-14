<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/5/19
 * Time: 1:28 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Models\Bucket;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\Models\Bucket
 */
class BucketTest extends TestCase
{
    public function testConvertModelToArray()
    {
        $model = new Bucket('a', 3);
        $this->assertSame([
            'value' => 'a',
            'count' => 3
        ], $model->toValueArray());
    }
}