<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/6/19
 * Time: 2:32 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Collections\CatalogIdCollection;
use App\Timeline\Exceptions\TimelineException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\Collections\CatalogIdCollection
 */
class CatalogIdCollectionTest extends TestCase
{
    public function testCreateFromValueArray()
    {
        $collection = CatalogIdCollection::createFromValueArray([1, 2]);
        $this->assertSame(2, count($collection));
    }

    public function testCreateFromNull()
    {
        $this->assertSame(null, CatalogIdCollection::createFromValueArray(null));
    }

    public function testCreateWithInvalidValue()
    {
        $this->expectException(TimelineException::class);
        CatalogIdCollection::createFromValueArray([1, 'a']);
    }
}