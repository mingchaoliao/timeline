<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/5/19
 * Time: 3:51 PM
 */

namespace Tests\Unit\Timeline;

use App\Timeline\Domain\Requests\CreateEventRequest;
use App\Timeline\Exceptions\TimelineException;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\Requests\CreateEventRequest
 */
class CreateEventRequestTest extends TestCase
{
    public function testStartDateAttributeShouldNotBeSetIfStartDateHasMonth() {
        $this->expectException(TimelineException::class);
        CreateEventRequest::fromArray([
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
}