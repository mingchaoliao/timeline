<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/6/19
 * Time: 2:11 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\ValueObjects\PeriodId;
use App\Timeline\Exceptions\TimelineException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\ValueObjects\PeriodId
 */
class PeriodIdTest extends TestCase
{
    public function testCreateIdFromString() {
        $this->assertSame(
            1,
            PeriodId::createFromString('1')->getValue()
        );
    }

    public function testCreateIdFromNull() {
        $this->assertSame(
            null,
            PeriodId::createFromString(null)
        );
    }

    public function testStringValueIsNotValidId() {
        $this->expectException(TimelineException::class);
        PeriodId::createFromString('-3');
    }
}