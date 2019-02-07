<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/7/19
 * Time: 11:13 AM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\App\Validators\ValidatorFactory;
use App\Timeline\Exceptions\InvalidArgumentException;
use Tests\TestCase;

/**
 * @covers \App\Timeline\App\Validators\ValidatorFactory
 * @covers \App\Timeline\Exceptions\InvalidArgumentException
 * @covers \App\Timeline\App\Validators\EventDateValidator
 */
class EventDateValidatorTest extends TestCase
{
    /**
     * @var ValidatorFactory
     */
    private $validatorFactory;

    protected function setUp()
    {
        parent::setUp();
        $this->validatorFactory = resolve(ValidatorFactory::class);
    }

    public function testSuccess()
    {
        try {
            $this->validatorFactory->validate(
                [
                    'startDate' => '2018',
                ],
                [
                    'startDate' => 'event_date'
                ]
            );
            $this->assertTrue(true);
        } catch (InvalidArgumentException $e) {
            $this->fail();
        }

        try {
            $this->validatorFactory->validate(
                [
                    'startDate' => '2018-01',
                ],
                [
                    'startDate' => 'event_date'
                ]
            );
            $this->assertTrue(true);
        } catch (InvalidArgumentException $e) {
            $this->fail();
        }

        try {
            $this->validatorFactory->validate(
                [
                    'startDate' => '2018-01-01',
                ],
                [
                    'startDate' => 'event_date'
                ]
            );
            $this->assertTrue(true);
        } catch (InvalidArgumentException $e) {
            $this->fail();
        }
    }

    public function testFailure()
    {
        try {
            $this->validatorFactory->validate(
                [
                    'startDate' => '2018/01',
                ],
                [
                    'startDate' => 'event_date'
                ]
            );
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertTrue(true);
        }
    }
}