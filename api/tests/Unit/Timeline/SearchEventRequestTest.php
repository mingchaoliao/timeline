<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/6/19
 * Time: 11:47 AM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Requests\SearchEventRequest;
use App\Timeline\Exceptions\InvalidArgumentException;
use Tests\TestCase;

/**
 * @covers \App\Timeline\Domain\Requests\SearchEventRequest
 */
class SearchEventRequestTest extends TestCase
{
    public function testStartDateMustBeAValidEventDateIfPresents()
    {
        $this->expectException(InvalidArgumentException::class);
        SearchEventRequest::createFromValueArray([
            'startDate' => '2018/01'
        ]);
    }

    public function testStartDateFromMustBeAValidISODateIfPresents()
    {
        $this->expectException(InvalidArgumentException::class);
        SearchEventRequest::createFromValueArray([
            'startDateFrom' => '2018-01'
        ]);
    }

    public function testStartDateToMustBeAValidISODateIfPresents()
    {
        $this->expectException(InvalidArgumentException::class);
        SearchEventRequest::createFromValueArray([
            'startDateTo' => '2018-01'
        ]);
    }

    public function testEndDateMustBeAValidEventDateIfPresents()
    {
        $this->expectException(InvalidArgumentException::class);
        SearchEventRequest::createFromValueArray([
            'endDate' => '2018/01'
        ]);
    }

    public function testEndDateFromMustBeAValidISODateIfPresents()
    {
        $this->expectException(InvalidArgumentException::class);
        SearchEventRequest::createFromValueArray([
            'endDateFrom' => '2018-01'
        ]);
    }

    public function testEndDateToMustBeAValidISODateIfPresents()
    {
        $this->expectException(InvalidArgumentException::class);
        SearchEventRequest::createFromValueArray([
            'endDateTo' => '2018-01'
        ]);
    }

    public function testPageNumberMustBeAnPositiveInteger()
    {
        try {
            SearchEventRequest::createFromValueArray([
                'page' => 'a'
            ]);

            $this->fail();
        } catch (InvalidArgumentException $e) {
        }

        try {
            SearchEventRequest::createFromValueArray([
                'page' => 0
            ]);

            $this->fail();
        } catch (InvalidArgumentException $e) {
        }

        try {
            SearchEventRequest::createFromValueArray([
                'page' => -3
            ]);

            $this->fail();
        } catch (InvalidArgumentException $e) {
        }

        $this->assertTrue(true);
    }

    public function testPageSizeMustBeAnPositiveInteger()
    {
        try {
            SearchEventRequest::createFromValueArray([
                'pageSize' => 'a'
            ]);

            $this->fail();
        } catch (InvalidArgumentException $e) {
        }

        try {
            SearchEventRequest::createFromValueArray([
                'pageSize' => 0
            ]);

            $this->fail();
        } catch (InvalidArgumentException $e) {
        }

        try {
            SearchEventRequest::createFromValueArray([
                'pageSize' => -3
            ]);

            $this->fail();
        } catch (InvalidArgumentException $e) {
        }

        $this->assertTrue(true);
    }

    public function testCreateRequestFromBody()
    {
        $data = [
            'content' => 'c',
            'startDate' => '2018',
            'endDate' => '2019',
            'period' => 'p',
            'catalogs' => 'c1,c2',
            'page' => 1,
            'pageSize' => 1
        ];
        $request = SearchEventRequest::createFromValueArray($data);

        $this->assertSame('c', $request->getContent());
        $this->assertSame('2018', $request->getStartDate()->getDate());
        $this->assertSame('2019', $request->getEndDate()->getDate());
        $this->assertSame('p', $request->getPeriod());
        $this->assertSame(['c1', 'c2'], $request->getCatalogs());
        $this->assertSame(1, $request->getPage());
        $this->assertSame(1, $request->getPageSize());
        $this->assertSame([
            'content' => 'c',
            'startDate' => '2018',
            'startDateFrom' => null,
            'startDateTo' => null,
            'endDate' => '2019',
            'endDateFrom' => null,
            'endDateTo' => null,
            'period' => 'p',
            'catalogs' => [
                'c1',
                'c2'
            ],
            'page' => 1,
            'pageSize' => 1
        ], $request->toValueArray());

        $data = [
            'startDateFrom' => '2018-01-01',
            'startDateTo' => '2018-01-02',
            'endDateFrom' => '2018-02-01',
            'endDateTo' => '2018-02-02',
        ];
        $request = SearchEventRequest::createFromValueArray($data);

        $this->assertSame('2018-01-01', $request->getStartDateFrom()->format('Y-m-d'));
        $this->assertSame('2018-01-02', $request->getStartDateTo()->format('Y-m-d'));
        $this->assertSame('2018-02-01', $request->getEndDateFrom()->format('Y-m-d'));
        $this->assertSame('2018-02-02', $request->getEndDateTo()->format('Y-m-d'));
        $this->assertSame([
            'content' => null,
            'startDate' => null,
            'startDateFrom' => '2018-01-01',
            'startDateTo' => '2018-01-02',
            'endDate' => null,
            'endDateFrom' => '2018-02-01',
            'endDateTo' => '2018-02-02',
            'period' => null,
            'catalogs' => [],
            'page' => 1,
            'pageSize' => 10
        ], $request->toValueArray());
    }

    public function testCreateFromNull()
    {
        $this->assertSame(null, SearchEventRequest::createFromValueArray(null));
    }
}