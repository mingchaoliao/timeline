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
 * @covers \App\Timeline\App\Validators\IDValidator
 */
class IDValidatorTest extends TestCase
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
                    'id' => 1,
                ],
                [
                    'id' => 'id'
                ]
            );
            $this->assertTrue(true);
        } catch (InvalidArgumentException $e) {
            $this->fail();
        }

        try {
            $this->validatorFactory->validate(
                [
                    'id' => '1',
                ],
                [
                    'id' => 'id'
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
                    'id' => 'a',
                ],
                [
                    'id' => 'id'
                ]
            );
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertTrue(true);
        }

        try {
            $this->validatorFactory->validate(
                [
                    'id' => 0,
                ],
                [
                    'id' => 'id'
                ]
            );
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertTrue(true);
        }

        try {
            $this->validatorFactory->validate(
                [
                    'id' => -1,
                ],
                [
                    'id' => 'id'
                ]
            );
            $this->fail();
        } catch (InvalidArgumentException $e) {
            $this->assertTrue(true);
        }
    }
}