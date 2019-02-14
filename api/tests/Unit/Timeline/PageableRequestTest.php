<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/5/19
 * Time: 3:44 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Requests\PageableRequest;
use Tests\TestCase;

/**
 * @covers \App\Timeline\Domain\Requests\PageableRequest
 */
class PageableRequestTest extends TestCase
{
    public function testDefaultPageAndPageSize() {
        $request = new PageableRequest();
        $this->assertSame(1, $request->getPage());
        $this->assertSame(10, $request->getPageSize());
    }

    public function testCreatePageableRequestFromArray()
    {
        $data = [
            'page' => '3',
            'pageSize' => '10'
        ];

        $request = PageableRequest::createFromValueArray($data);

        $this->assertSame(3, $request->getPage());
        $this->assertSame(10, $request->getPageSize());
        $this->assertSame(20, $request->getOffset());
        $this->assertSame([
            'page' => 3,
            'pageSize' => 10
        ], $request->toValueArray());
    }

    public function testCreateFromNull()
    {
        $this->assertSame(null, PageableRequest::createFromValueArray(null));
    }
}