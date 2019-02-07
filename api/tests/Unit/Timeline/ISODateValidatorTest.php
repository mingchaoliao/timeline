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
 * @covers \App\Timeline\App\Validators\ISODateValidator
 */
class ISODateValidatorTest extends TestCase
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
                    'startDateFrom' => '2018-01-03',
                ],
                [
                    'startDateFrom' => 'iso_date'
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
                    'startDateFrom' => '2018-01',
                ],
                [
                    'startDateFrom' => 'iso_date'
                ]
            );
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertTrue(true);
        }

        try {
            $this->validatorFactory->validate(
                [
                    'startDateFrom' => '2018',
                ],
                [
                    'startDateFrom' => 'iso_date'
                ]
            );
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertTrue(true);
        }

        try {
            $this->validatorFactory->validate(
                [
                    'startDateFrom' => '2018/03/03',
                ],
                [
                    'startDateFrom' => 'iso_date'
                ]
            );
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertTrue(true);
        }

        try {
            $this->validatorFactory->validate(
                [
                    'startDateFrom' => 'asdf',
                ],
                [
                    'startDateFrom' => 'iso_date'
                ]
            );
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertTrue(true);
        }
    }
}