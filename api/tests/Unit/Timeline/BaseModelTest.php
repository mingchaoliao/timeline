<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/5/19
 * Time: 1:22 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Models\BaseModel;
use PHPUnit\Framework\TestCase;

final class TestModel1 extends BaseModel {

    public function toArray(): array
    {
        return [
            'attr1' => 1,
            'attr2' => 'a'
        ];
    }
}

/**
 * @covers \App\Timeline\Domain\Models\BaseModel
 */
class BaseModelTest extends TestCase
{
    public function testConvertModelToJson() {
        $this->assertSame('{"attr1":1,"attr2":"a"}', (new TestModel1())->toJson());
    }
}