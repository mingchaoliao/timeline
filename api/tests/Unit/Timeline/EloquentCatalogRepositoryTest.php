<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/7/19
 * Time: 7:17 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\ValueObjects\UserId;
use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentCatalog;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentUser;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentCatalogRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Timeline\Exceptions\TimelineException
 * @covers \App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentCatalogRepository
 */
class EloquentCatalogRepositoryTest extends TestCase
{
    /**
     * @var MockObject
     */
    private $catalogModel;
    /**
     * @var EloquentCatalogRepository
     */
    private $catalogRepo;

    protected function setUp()
    {
        parent::setUp();
        $this->catalogModel = $this->getMockBuilder(EloquentCatalog::class)
            ->setMethods([
                'select',
                'withFullInfo',
                'get',
                'create',
                'whereIn'
            ])
            ->getMock();
        $this->catalogModel->method('select')->willReturnSelf();
        $this->catalogModel->method('withFullInfo')->willReturnSelf();
        $this->catalogRepo = new EloquentCatalogRepository($this->catalogModel);
    }

    public function testGetTypeahead()
    {
        $eloquentCatalog1 = $this->createMock(EloquentCatalog::class);
        $eloquentCatalog1->method('getId')->willReturn(1);
        $eloquentCatalog1->method('getValue')->willReturn('catalog1');
        $eloquentCatalog2 = $this->createMock(EloquentCatalog::class);
        $eloquentCatalog2->method('getId')->willReturn(2);
        $eloquentCatalog2->method('getValue')->willReturn('catalog2');

        $this->catalogModel->method('get')->willReturn(new Collection([$eloquentCatalog1, $eloquentCatalog2]));

        $results = $this->catalogRepo->getTypeahead();
        $this->assertSame([
            [
                'id' => 1,
                'value' => 'catalog1'
            ],
            [
                'id' => 2,
                'value' => 'catalog2'
            ]
        ], $results->toValueArray());
    }

    public function testGetAllCatalogs()
    {
        $eloquentCatalog1 = $this->createMock(EloquentCatalog::class);
        $c1CreateDate = Carbon::create(2018, 1, 1, 0, 0, 0);
        $c1UpdateDate = Carbon::create(2018, 2, 2, 0, 0, 0);
        $eloquentCatalog1->method('getId')->willReturn(1);
        $eloquentCatalog1->method('getValue')->willReturn('catalog1');
        $eloquentCatalog1->method('getCreateUserId')->willReturn(1);
        $eloquentCatalog1->method('getUpdateUserId')->willReturn(1);
        $eloquentCatalog1->method('getCreatedAt')->willReturn($c1CreateDate);
        $eloquentCatalog1->method('getUpdatedAt')->willReturn($c1UpdateDate);
        $eloquentCatalog1->method('getNumberOfEvents')->willReturn(1);
        $c1User = $this->createMock(EloquentUser::class);
        $c1User->method('getName')->willReturn('user1');
        $eloquentCatalog1->method('getCreateUser')->willReturn($c1User);
        $eloquentCatalog1->method('getUpdateUser')->willReturn($c1User);

        $this->catalogModel->method('get')->willReturn(new Collection([$eloquentCatalog1]));

        $catalogs = $this->catalogRepo->getAll();
        $this->assertSame([
            [
                'id' => 1,
                'value' => 'catalog1',
                'numberOfEvents' => 1,
                'createUserId' => 1,
                'createUserName' => 'user1',
                'updateUserId' => 1,
                'updateUserName' => 'user1',
                'createdAt' => $c1CreateDate->toIso8601String(),
                'updatedAt' => $c1UpdateDate->toIso8601String()
            ]
        ], $catalogs->toValueArray());
    }

    public function testCreateCatalog()
    {
        $eloquentCatalog1 = $this->createMock(EloquentCatalog::class);
        $c1CreateDate = Carbon::create(2018, 1, 1, 0, 0, 0);
        $c1UpdateDate = Carbon::create(2018, 2, 2, 0, 0, 0);
        $eloquentCatalog1->method('getId')->willReturn(1);
        $eloquentCatalog1->method('getValue')->willReturn('catalog1');
        $eloquentCatalog1->method('getCreateUserId')->willReturn(1);
        $eloquentCatalog1->method('getUpdateUserId')->willReturn(1);
        $eloquentCatalog1->method('getCreatedAt')->willReturn($c1CreateDate);
        $eloquentCatalog1->method('getUpdatedAt')->willReturn($c1UpdateDate);
        $eloquentCatalog1->method('getNumberOfEvents')->willReturn(1);
        $c1User = $this->createMock(EloquentUser::class);
        $c1User->method('getName')->willReturn('user1');
        $eloquentCatalog1->method('getCreateUser')->willReturn($c1User);
        $eloquentCatalog1->method('getUpdateUser')->willReturn($c1User);

        $this->catalogModel->method('create')->willReturn($eloquentCatalog1);

        $catalog = $this->catalogRepo->create('a', new UserId(1));
        $this->assertSame([
            'id' => 1,
            'value' => 'catalog1',
            'numberOfEvents' => 1,
            'createUserId' => 1,
            'createUserName' => 'user1',
            'updateUserId' => 1,
            'updateUserName' => 'user1',
            'createdAt' => $c1CreateDate->toIso8601String(),
            'updatedAt' => $c1UpdateDate->toIso8601String()
        ], $catalog->toValueArray());
    }

    public function testCreateCatalogWhichAlreadyExists()
    {
        $this->expectException(TimelineException::class);
        $queryException = $this->createMock(QueryException::class);
        $queryException->errorInfo = ['', 1062];
        $this->catalogModel->method('create')->willThrowException($queryException);
        $this->catalogRepo->create('a', new UserId(1));
    }

    public function testCreateCatalogWithNonExistingUserId()
    {
        $this->expectException(TimelineException::class);
        $queryException = $this->createMock(QueryException::class);
        $queryException->errorInfo = ['', 1452];
        $this->catalogModel->method('create')->willThrowException($queryException);
        $this->catalogRepo->create('a', new UserId(1));
    }

    public function testFailedToCreateCatalogBecauseOfUnknownDatabaseError()
    {
        $this->expectException(QueryException::class);
        $queryException = $this->createMock(QueryException::class);
        $queryException->errorInfo = ['', 2000];
        $this->catalogModel->method('create')->willThrowException($queryException);
        $this->catalogRepo->create('a', new UserId(1));
    }

//    public function testBulkCreateCatalogs() {
//        $eloquentCatalog1 = $this->createMock(EloquentCatalog::class);
//        $c1CreateDate = Carbon::create(2018, 1, 1, 0, 0, 0);
//        $c1UpdateDate = Carbon::create(2018, 2, 2, 0, 0, 0);
//        $eloquentCatalog1->method('getId')->willReturn(1);
//        $eloquentCatalog1->method('getValue')->willReturn('catalog1');
//        $eloquentCatalog1->method('getCreateUserId')->willReturn(1);
//        $eloquentCatalog1->method('getUpdateUserId')->willReturn(1);
//        $eloquentCatalog1->method('getCreatedAt')->willReturn($c1CreateDate);
//        $eloquentCatalog1->method('getUpdatedAt')->willReturn($c1UpdateDate);
//        $eloquentCatalog1->method('getNumberOfEvents')->willReturn(1);
//        $c1User = $this->createMock(EloquentUser::class);
//        $c1User->method('getName')->willReturn('user1');
//        $eloquentCatalog1->method('getCreateUser')->willReturn($c1User);
//        $eloquentCatalog1->method('getUpdateUser')->willReturn($c1User);
//
//        $eloquentCatalog2 = $this->createMock(EloquentCatalog::class);
//        $c2CreateDate = Carbon::create(2018, 1, 1, 0, 0, 0);
//        $c2UpdateDate = Carbon::create(2018, 2, 2, 0, 0, 0);
//        $eloquentCatalog1->method('getId')->willReturn(2);
//        $eloquentCatalog2->method('getValue')->willReturn('catalog2');
//        $eloquentCatalog2->method('getCreateUserId')->willReturn(2);
//        $eloquentCatalog2->method('getUpdateUserId')->willReturn(2);
//        $eloquentCatalog2->method('getCreatedAt')->willReturn($c2CreateDate);
//        $eloquentCatalog2->method('getUpdatedAt')->willReturn($c2UpdateDate);
//        $eloquentCatalog2->method('getNumberOfEvents')->willReturn(2);
//        $c2User = $this->createMock(EloquentUser::class);
//        $c2User->method('getName')->willReturn('user2');
//        $eloquentCatalog2->method('getCreateUser')->willReturn($c2User);
//        $eloquentCatalog2->method('getUpdateUser')->willReturn($c2User);
//
//
//        $this->catalogModel->method('whereIn')
//            ->with(
//                $this->equalTo('value'),
//                $this->equalTo(['catalog2'])
//            )
//            ->willReturnSelf();
//
//        $this->catalogModel
//            ->expects($this->at(0))
//            ->method('get')
//            ->willReturn(new Collection([$eloquentCatalog1]));
//
//
//    }
}