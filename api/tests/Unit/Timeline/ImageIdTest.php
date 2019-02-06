<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/6/19
 * Time: 2:11 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\ValueObjects\ImageId;
use App\Timeline\Exceptions\TimelineException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\ValueObjects\ImageId
 */
class ImageIdTest extends TestCase
{
    public function testCreateIdFromString() {
        $this->assertSame(
            1,
            ImageId::createFromString('1')->getValue()
        );
    }

    public function testCreateIdFromNull() {
        $this->assertSame(
            null,
            ImageId::createFromString(null)
        );
    }

    public function testStringValueIsNotValidId() {
        $this->expectException(TimelineException::class);
        ImageId::createFromString('-3');
    }
}