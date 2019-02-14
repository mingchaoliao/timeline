<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/6/19
 * Time: 2:11 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\ValueObjects\DateAttributeId;
use App\Timeline\Domain\ValueObjects\EventId;
use App\Timeline\Exceptions\TimelineException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\ValueObjects\DateAttributeId
 * @covers \App\Timeline\Domain\ValueObjects\SingleInteger
 */
class DateAttributeIdTest extends TestCase
{
    public function testCreateIdFromString()
    {
        $id = DateAttributeId::createFromString('1');
        $this->assertSame(1, $id->getValue());
        $this->assertSame('1', (string)$id);
        $this->assertTrue($id->equalsWith(new EventId(1)));
        $this->assertFalse($id->equalsWith(new EventId(2)));
    }

    public function testCreateIdFromNull()
    {
        $this->assertSame(
            null,
            DateAttributeId::createFromString(null)
        );
    }

    public function testStringValueIsNotValidId()
    {
        $this->expectException(TimelineException::class);
        DateAttributeId::createFromString('-3');
    }
}