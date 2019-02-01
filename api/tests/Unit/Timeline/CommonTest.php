<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 1/31/19
 * Time: 9:03 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Utils\Common;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Utils\Common
 */
class CommonTest extends TestCase
{
    public function testIsInteger()
    {
        $this->assertTrue(Common::isInt(1));
        $this->assertTrue(Common::isInt(0));
        $this->assertTrue(Common::isInt(-3));
        $this->assertTrue(Common::isInt('1'));

        $this->assertFalse(Common::isInt(null));
        $this->assertFalse(Common::isInt('s'));
        $this->assertFalse(Common::isInt(1.3));
        $this->assertFalse(Common::isInt('1.3'));
        $this->assertFalse(Common::isInt(-1.3));
        $this->assertFalse(Common::isInt('/'));
        $this->assertFalse(Common::isInt(new \stdClass()));
    }

    public function testIsPositiveInteger()
    {
        $this->assertTrue(Common::isPosInt(1));
        $this->assertTrue(Common::isPosInt('1'));

        $this->assertFalse(Common::isPosInt(0));
        $this->assertFalse(Common::isPosInt(-3));
        $this->assertFalse(Common::isPosInt('-3'));
        $this->assertFalse(Common::isPosInt(null));
        $this->assertFalse(Common::isPosInt('s'));
        $this->assertFalse(Common::isPosInt(1.3));
        $this->assertFalse(Common::isPosInt(-1.3));
        $this->assertFalse(Common::isPosInt(new \stdClass()));
    }

    public function testSplitByComma()
    {
        $this->assertSame(null, Common::splitByComma(null));
        $this->assertSame(['1', '2', '3'], Common::splitByComma('1,2,3'));
        $this->assertSame(['1', 's', '3'], Common::splitByComma('1,s,3'));
        $this->assertSame(['1', ' 2', '3'], Common::splitByComma('1, 2,3'));
        $this->assertSame(['', '2', ''], Common::splitByComma(',2,'));
    }
}