<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/5/19
 * Time: 1:48 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Models\EventDate;
use App\Timeline\Exceptions\TimelineException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\Models\EventDate
 */
class EventDateTest extends TestCase
{
    public function testDateWithYearOnly()
    {
        $dateStr = '2018';
        $date = EventDate::createFromString($dateStr);
        $this->assertSame($dateStr, (string)$date);
        $this->assertSame($dateStr, $date->getDate());
        $this->assertTrue($date->isAttributeAllowed());
        $this->assertSame([
            'year' => 2018
        ], $date->toDateArray());
        $this->assertSame([
            'date' => '2018'
        ], $date->toValueArray());
        $this->assertSame(
            '2018-01-01 00:00:00',
            $date->toStartDate()->format('Y-m-d H:i:s')
        );
        $this->assertSame(
            '2018-12-31 23:59:59',
            $date->toEndDate()->format('Y-m-d H:i:s')
        );
    }

    public function testDateWithYearAndMonthOnly()
    {
        $dateStr = '2018-03';
        $date = EventDate::createFromString($dateStr);
        $this->assertSame($dateStr, (string)$date);
        $this->assertSame($dateStr, $date->getDate());
        $this->assertFalse($date->isAttributeAllowed());
        $this->assertSame([
            'year' => 2018,
            'month' => 3
        ], $date->toDateArray());
        $this->assertSame([
            'date' => '2018-03'
        ], $date->toValueArray());
        $this->assertSame(
            '2018-03-01 00:00:00',
            $date->toStartDate()->format('Y-m-d H:i:s')
        );
        $this->assertSame(
            '2018-03-31 23:59:59',
            $date->toEndDate()->format('Y-m-d H:i:s')
        );
    }

    public function testDateWithYearMonthAndDay()
    {
        $dateStr = '2018-03-13';
        $date = EventDate::createFromString($dateStr);
        $this->assertSame($dateStr, (string)$date);
        $this->assertSame($dateStr, $date->getDate());
        $this->assertFalse($date->isAttributeAllowed());
        $this->assertSame([
            'year' => 2018,
            'month' => 3,
            'day' => 13
        ], $date->toDateArray());
        $this->assertSame([
            'date' => '2018-03-13'
        ], $date->toValueArray());
        $this->assertSame(
            '2018-03-13 00:00:00',
            $date->toStartDate()->format('Y-m-d H:i:s')
        );
        $this->assertSame(
            '2018-03-13 23:59:59',
            $date->toEndDate()->format('Y-m-d H:i:s')
        );
    }

    public function testUnsupportedDateFormat()
    {
        $this->expectException(TimelineException::class);
        EventDate::createFromString('03/13/2019');
    }

    public function testCreateFromNull()
    {
        $this->assertSame(null, EventDate::createFromString(null));
    }
}