<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/5/19
 * Time: 3:51 PM
 */

namespace Tests\Unit\Timeline;

use App\Timeline\Domain\Requests\UpdateEventRequest;
use App\Timeline\Exceptions\InvalidArgumentException;
use Tests\TestCase;

/**
 * @covers \App\Timeline\Domain\Requests\UpdateEventRequest
 */
class UpdateEventRequestTest extends TestCase
{
    public function testStartDateAttributeShouldNotBeSetIfStartDateHasMonth()
    {
        $this->expectException(InvalidArgumentException::class);
        UpdateEventRequest::createFromValueArray([
            'startDate' => '2018-03',
            'startDateAttributeId' => 1,
            'endDate' => '2019',
            'endDateAttributeId' => null,
            'periodId' => 1,
            'catalogIds' => [1, 2],
            'content' => '123',
            'imageIds' => [2, 3]
        ]);
    }

    public function testEndDateAttributeShouldNotBeSetIfEndDateHasMonth()
    {
        $this->expectException(InvalidArgumentException::class);
        UpdateEventRequest::createFromValueArray([
            'startDate' => '2018',
            'startDateAttributeId' => 1,
            'endDate' => '2019-03',
            'endDateAttributeId' => 1,
            'periodId' => 1,
            'catalogIds' => [1, 2],
            'content' => '123',
            'imageIds' => [2, 3]
        ]);
    }

    public function testStartDateIsRequired()
    {
        $this->expectException(InvalidArgumentException::class);
        UpdateEventRequest::createFromValueArray([
            'startDateAttributeId' => 1,
            'endDate' => '2019-03',
            'endDateAttributeId' => 1,
            'periodId' => 1,
            'catalogIds' => [1, 2],
            'content' => '123',
            'imageIds' => [2, 3]
        ]);
    }

    public function testStartDateMustBeEventDateFormat()
    {
        $this->expectException(InvalidArgumentException::class);
        UpdateEventRequest::createFromValueArray([
            'startDate' => '2018/03/23',
            'startDateAttributeId' => 1,
            'endDate' => '2019-03',
            'endDateAttributeId' => 1,
            'periodId' => 1,
            'catalogIds' => [1, 2],
            'content' => '123',
            'imageIds' => [2, 3]
        ]);
    }

    public function testStartDateAttributeIdMustBeValidId()
    {
        try {
            UpdateEventRequest::createFromValueArray([
                'startDate' => '2018',
                'startDateAttributeId' => 's',
                'endDate' => '2019',
                'endDateAttributeId' => 1,
                'periodId' => 1,
                'catalogIds' => [1, 2],
                'content' => '123',
                'imageIds' => [2, 3]
            ]);
            $this->fail();
        } catch (InvalidArgumentException $e) {
        }

        try {
            UpdateEventRequest::createFromValueArray([
                'startDate' => '2018',
                'startDateAttributeId' => -3,
                'endDate' => '2019',
                'endDateAttributeId' => 1,
                'periodId' => 1,
                'catalogIds' => [1, 2],
                'content' => '123',
                'imageIds' => [2, 3]
            ]);
            $this->fail();
        } catch (InvalidArgumentException $e) {
        }

        try {
            UpdateEventRequest::createFromValueArray([
                'startDate' => '2018',
                'startDateAttributeId' => 0,
                'endDate' => '2019',
                'endDateAttributeId' => 1,
                'periodId' => 1,
                'catalogIds' => [1, 2],
                'content' => '123',
                'imageIds' => [2, 3]
            ]);
            $this->fail();
        } catch (InvalidArgumentException $e) {
        }

        $this->assertTrue(true);
    }

    public function testEndDateMustBeEventDateFormat()
    {
        $this->expectException(InvalidArgumentException::class);
        UpdateEventRequest::createFromValueArray([
            'startDate' => '2018',
            'startDateAttributeId' => 1,
            'endDate' => '2019/03',
            'endDateAttributeId' => 1,
            'periodId' => 1,
            'catalogIds' => [1, 2],
            'content' => '123',
            'imageIds' => [2, 3]
        ]);
    }

    public function testEndDateAttributeIdMustBeValidId()
    {
        try {
            UpdateEventRequest::createFromValueArray([
                'startDate' => '2018',
                'startDateAttributeId' => 1,
                'endDate' => '2019',
                'endDateAttributeId' => 's',
                'periodId' => 1,
                'catalogIds' => [1, 2],
                'content' => '123',
                'imageIds' => [2, 3]
            ]);
            $this->fail();
        } catch (InvalidArgumentException $e) {
        }

        try {
            UpdateEventRequest::createFromValueArray([
                'startDate' => '2018',
                'startDateAttributeId' => 1,
                'endDate' => '2019',
                'endDateAttributeId' => -3,
                'periodId' => 1,
                'catalogIds' => [1, 2],
                'content' => '123',
                'imageIds' => [2, 3]
            ]);
            $this->fail();
        } catch (InvalidArgumentException $e) {
        }

        try {
            UpdateEventRequest::createFromValueArray([
                'startDate' => '2018',
                'startDateAttributeId' => 1,
                'endDate' => '2019',
                'endDateAttributeId' => 0,
                'periodId' => 1,
                'catalogIds' => [1, 2],
                'content' => '123',
                'imageIds' => [2, 3]
            ]);
            $this->fail();
        } catch (InvalidArgumentException $e) {
        }

        $this->assertTrue(true);
    }

    public function testContentMustBeProvided() {
        $this->expectException(InvalidArgumentException::class);
        UpdateEventRequest::createFromValueArray([
            'startDate' => '2018',
            'startDateAttributeId' => 1,
            'endDate' => '2019',
            'endDateAttributeId' => 1,
            'periodId' => 1,
            'catalogIds' => [1, 2],
            'imageIds' => [2, 3]
        ]);
    }

    public function testPeriodIdMustBeAValidIdWhenProvided() {
        $this->expectException(InvalidArgumentException::class);
        UpdateEventRequest::createFromValueArray([
            'startDate' => '2018',
            'startDateAttributeId' => 1,
            'endDate' => '2019',
            'endDateAttributeId' => 1,
            'content' => 'content',
            'periodId' => -3,
            'catalogIds' => [1, 2],
            'imageIds' => [2, 3]
        ]);
    }

    public function testCatalogIdsMustBeAnArrayWhenProvided() {
        $this->expectException(InvalidArgumentException::class);
        UpdateEventRequest::createFromValueArray([
            'startDate' => '2018',
            'startDateAttributeId' => 1,
            'endDate' => '2019',
            'endDateAttributeId' => 1,
            'content' => 'content',
            'periodId' => 1,
            'catalogIds' => 1,
            'imageIds' => [2, 3]
        ]);
    }

    public function testEachIdInTheCatalogIdsMustBeAValidId() {
        $this->expectException(InvalidArgumentException::class);
        UpdateEventRequest::createFromValueArray([
            'startDate' => '2018',
            'startDateAttributeId' => 1,
            'endDate' => '2019',
            'endDateAttributeId' => 1,
            'content' => 'content',
            'periodId' => 1,
            'catalogIds' => [1, -1],
            'imageIds' => [2, 3]
        ]);
    }

    public function testImageIdsMustBeAnArrayWhenProvided() {
        $this->expectException(InvalidArgumentException::class);
        UpdateEventRequest::createFromValueArray([
            'startDate' => '2018',
            'startDateAttributeId' => 1,
            'endDate' => '2019',
            'endDateAttributeId' => 1,
            'content' => 'content',
            'periodId' => 1,
            'catalogIds' => [],
            'imageIds' => 2
        ]);
    }

    public function testEachIdInTheImageIdsMustBeAValidId() {
        $this->expectException(InvalidArgumentException::class);
        UpdateEventRequest::createFromValueArray([
            'startDate' => '2018',
            'startDateAttributeId' => 1,
            'endDate' => '2019',
            'endDateAttributeId' => 1,
            'content' => 'content',
            'periodId' => 1,
            'catalogIds' => [],
            'imageIds' => [2, -2]
        ]);
    }

    public function testCreateRequestFromRequestBody() {
        $request = UpdateEventRequest::createFromValueArray([
            'startDate' => '2018-01',
            'startDateAttributeId' => null,
            'endDate' => '2019',
            'endDateAttributeId' => 1,
            'content' => 'content',
            'periodId' => 1,
            'catalogIds' => [1, 2],
            'imageIds' => [3, 4]
        ]);

        $this->assertSame('2018-01', $request->getStartDate()->getDate());
        $this->assertSame(null, $request->getStartDateAttributeId());
        $this->assertSame('2019', $request->getEndDate()->getDate());
        $this->assertSame(1, $request->getEndDateAttributeId()->getValue());
        $this->assertSame('content', $request->getContent());
        $this->assertSame(1, $request->getPeriodId()->getValue());
        $this->assertSame([1, 2], $request->getCatalogIds()->toValueArray());
        $this->assertSame([3, 4], $request->getImageIds()->toValueArray());
    }
}