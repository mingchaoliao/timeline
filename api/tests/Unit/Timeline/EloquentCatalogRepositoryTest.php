<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/7/19
 * Time: 7:17 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\ValueObjects\CatalogId;
use App\Timeline\Domain\ValueObjects\UserId;
use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentCatalog;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentUser;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentCatalogRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @covers \App\Timeline\Exceptions\TimelineException
 * @covers \App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentCatalogRepository
 */
class EloquentCatalogRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var EloquentCatalogRepository
     */
    private $catalogRepo;
    /**
     * @var Callback
     */
    private $sortCatalog;
    /**
     * @var Callback
     */
    private $createCatalogValueArray;

    protected function setUp()
    {
        parent::setUp();
        $this->catalogRepo = new EloquentCatalogRepository(
            resolve(EloquentCatalog::class)
        );
        $this->sortCatalog = function (EloquentCatalog $c1, EloquentCatalog $c2) {
            $id1 = $c1->getId();
            $id2 = $c2->getId();
            if ($id1 === $id2) {
                return 0;
            }

            return $id1 < $id2 ? -1 : 1;
        };
        $this->createCatalogValueArray = function (EloquentCatalog $eloquentCatalog) {
            return [
                'id' => $eloquentCatalog->getId(),
                'value' => $eloquentCatalog->getValue(),
                'numberOfEvents' => $eloquentCatalog->getNumberOfEvents(),
                'createUserId' => $eloquentCatalog->getCreateUserId(),
                'createUserName' => $eloquentCatalog->getCreateUser()->getName(),
                'updateUserId' => $eloquentCatalog->getUpdateUserId(),
                'updateUserName' => $eloquentCatalog->getUpdateUser()->getName(),
                'createdAt' => $eloquentCatalog->getCreatedAt()->toIso8601String(),
                'updatedAt' => $eloquentCatalog->getUpdatedAt()->toIso8601String()
            ];
        };
    }

    public function testGetTypeahead()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create();
        /** @var Collection $catalogs */
        $catalogs = factory(EloquentCatalog::class)->times(2)->create([
            'create_user_id' => $user->getId(),
            'update_user_id' => $user->getId()
        ]);

        $results = $this->catalogRepo->getTypeahead();

        $expectedResults = $catalogs
            ->sort($this->sortCatalog)
            ->map(function (EloquentCatalog $eloquentCatalog) {
                return [
                    'id' => $eloquentCatalog->getId(),
                    'value' => $eloquentCatalog->getValue()
                ];
            })->toArray();

        $this->assertEquals($expectedResults, $results->toValueArray());
    }

    public function testGetAll()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create();
        /** @var Collection $catalogs */
        $catalogs = factory(EloquentCatalog::class)->times(2)->create([
            'create_user_id' => $user->getId(),
            'update_user_id' => $user->getId()
        ]);

        $results = $this->catalogRepo->getAll();

        $expectedResults = $catalogs
            ->sort($this->sortCatalog)
            ->map($this->createCatalogValueArray)
            ->toArray();

        $this->assertEquals($expectedResults, $results->toValueArray());
    }

    public function testCreateCatalog()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create();

        $catalog = $this->catalogRepo->create('catalog1', new UserId($user->getId()));

        $this->assertEquals('catalog1', $catalog->getValue());
    }

    public function testCreateCatalogWhichAlreadyExists()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create();
        factory(EloquentCatalog::class)->create([
            'value' => 'catalog1',
            'create_user_id' => $user->getId(),
            'update_user_id' => $user->getId()
        ]);

        $this->expectException(TimelineException::class);
        $this->catalogRepo->create('catalog1', new UserId($user->getId()));
    }

    public function testCreateCatalogWithNonExistingUser()
    {
        $this->expectException(TimelineException::class);
        $this->catalogRepo->create('catalog1', new UserId(1));
    }

    public function testBulkCreateCatalogs()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create();
        factory(EloquentCatalog::class)->create([
            'value' => 'catalog1',
            'create_user_id' => $user->getId(),
            'update_user_id' => $user->getId()
        ]);
        $catalogs = $this->catalogRepo->bulkCreate(['catalog1', 'catalog2'], new UserId($user->getId()));
        $this->assertSame(2, count($catalogs));
        $this->assertSame('catalog1', $catalogs[0]->getValue());
        $this->assertSame('catalog2', $catalogs[1]->getValue());
    }

    public function testUpdateCatalog()
    {
        /** @var EloquentUser $createUser */
        $createUser = factory(EloquentUser::class)->create();
        /** @var EloquentCatalog $catalog */
        $catalog = factory(EloquentCatalog::class)->create([
            'value' => 'value',
            'create_user_id' => $createUser->getId(),
            'update_user_id' => $createUser->getId()
        ]);

        /** @var EloquentUser $updateUser */
        $updateUser = factory(EloquentUser::class)->create();

        $newCatalog = $this->catalogRepo->update(
            new CatalogId($catalog->getId()),
            'new_value',
            new UserId($updateUser->getId())
        );

        $this->assertSame($catalog->getId(), $newCatalog->getId()->getValue());
        $this->assertSame('new_value', $newCatalog->getValue());
        $this->assertSame($updateUser->getId(), $newCatalog->getUpdateUserId()->getValue());
    }

    public function testUpdateCatalogWithNonExistingCatalogId()
    {
        $this->expectException(TimelineException::class);
        $this->catalogRepo->update(
            new CatalogId(1),
            'new_value',
            new UserId(1)
        );
    }

    public function testUpdateCatalogWithExistingValue()
    {
        /** @var EloquentUser $createUser */
        $createUser = factory(EloquentUser::class)->create();
        factory(EloquentCatalog::class)->create([
            'value' => 'value1',
            'create_user_id' => $createUser->getId(),
            'update_user_id' => $createUser->getId()
        ]);
        /** @var EloquentCatalog $catalog */
        $catalog = factory(EloquentCatalog::class)->create([
            'value' => 'value2',
            'create_user_id' => $createUser->getId(),
            'update_user_id' => $createUser->getId()
        ]);

        /** @var EloquentUser $updateUser */
        $updateUser = factory(EloquentUser::class)->create();

        $this->expectException(TimelineException::class);
        $this->catalogRepo->update(
            new CatalogId($catalog->getId()),
            'value1',
            new UserId($updateUser->getId())
        );
    }

    public function testUpdateCatalogWithNonExistingUser()
    {
        /** @var EloquentUser $createUser */
        $createUser = factory(EloquentUser::class)->create();
        /** @var EloquentCatalog $catalog */
        $catalog = factory(EloquentCatalog::class)->create([
            'value' => 'value',
            'create_user_id' => $createUser->getId(),
            'update_user_id' => $createUser->getId()
        ]);

        $this->expectException(TimelineException::class);

        $this->catalogRepo->update(
            new CatalogId($catalog->getId()),
            'new_value',
            new UserId(1000)
        );
    }

    public function testDeleteCatalog()
    {
        /** @var EloquentUser $createUser */
        $createUser = factory(EloquentUser::class)->create();
        /** @var EloquentCatalog $catalog */
        $catalog = factory(EloquentCatalog::class)->create([
            'create_user_id' => $createUser->getId(),
            'update_user_id' => $createUser->getId()
        ]);
        $this->catalogRepo->delete(new CatalogId($catalog->getId()));
        $this->assertEmpty(EloquentCatalog::all());
    }

    public function testDeleteCatalogWithNonExistingID()
    {
        $this->expectException(TimelineException::class);
        $this->catalogRepo->delete(new CatalogId(1));
    }
}