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
    public function testCreatePageableRequestFromArray()
    {
        $request = PageableRequest::createFromValueArray([
            'page' => '3',
            'pageSize' => '10'
        ]);

        $this->assertSame(3, $request->getPage());
        $this->assertSame(10, $request->getPageSize());
        $this->assertSame(20, $request->getOffset());
    }
}