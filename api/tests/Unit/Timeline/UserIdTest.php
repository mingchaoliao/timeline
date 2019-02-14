<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/6/19
 * Time: 2:11 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\ValueObjects\UserId;
use App\Timeline\Exceptions\TimelineException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\ValueObjects\UserId
 */
class UserIdTest extends TestCase
{
    public function testCreateIdFromString() {
        $this->assertSame(
            1,
            UserId::createFromString('1')->getValue()
        );
    }

    public function testCreateIdFromNull() {
        $this->assertSame(
            null,
            UserId::createFromString(null)
        );
    }

    public function testStringValueIsNotValidId() {
        $this->expectException(TimelineException::class);
        UserId::createFromString('-3');
    }
}