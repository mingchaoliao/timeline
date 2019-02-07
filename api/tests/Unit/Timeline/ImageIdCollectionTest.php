<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/6/19
 * Time: 2:32 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Collections\ImageIdCollection;
use App\Timeline\Exceptions\TimelineException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\Collections\ImageIdCollection
 * @covers \App\Timeline\Domain\Collections\BaseSingleValueCollection
 * @covers \App\Timeline\Domain\Collections\BaseCollection
 */
class ImageIdCollectionTest extends TestCase
{
    public function testCreateFromValueArray()
    {
        $collection = ImageIdCollection::createFromArray([1, 2]);
        $this->assertSame(2, count($collection));
    }

    public function testCreateFromNull()
    {
        $this->assertSame(null, ImageIdCollection::createFromArray(null));
    }

    public function testCreateWithInvalidValue()
    {
        $this->expectException(TimelineException::class);
        ImageIdCollection::createFromArray([1, 'a']);
    }
}