<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/6/19
 * Time: 2:11 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\ValueObjects\Email;
use App\Timeline\Exceptions\TimelineException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\ValueObjects\Email
 * @covers \App\Timeline\Domain\ValueObjects\SingleString
 */
class EmailTest extends TestCase
{
    public function testCreateFromNull()
    {
        $this->assertSame(null, Email::createFromString(null));
    }

    public function testCreateEmailFromString()
    {
        $email = Email::createFromString('user1@test.com');
        $this->assertSame('user1@test.com', (string)$email);
        $this->assertSame('user1@test.com', $email->getValue());
    }

    public function testInvalidEmail()
    {
        $this->expectException(TimelineException::class);
        new Email('aaa');
    }
}