<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/5/19
 * Time: 1:28 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Models\Typeahead;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Domain\Models\Typeahead
 */
class TypeaheadTest extends TestCase
{
    public function testConvertModelToArray()
    {
        $model = new Typeahead(1, 'a');
        $this->assertSame([
            'id' => 1,
            'value' => 'a'
        ], $model->toValueArray());
    }
}