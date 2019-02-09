<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/7/19
 * Time: 7:17 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\ValueObjects\DateAttributeId;
use App\Timeline\Domain\ValueObjects\UserId;
use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentDateAttribute;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentUser;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentDateAttributeRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @covers \App\Timeline\Exceptions\TimelineException
 * @covers \App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentDateAttributeRepository
 */
class EloquentDateAttributeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var EloquentDateAttributeRepository
     */
    private $dateAttributeRepo;
    /**
     * @var callable
     */
    private $sortDateAttribute;
    /**
     * @var callable
     */
    private $createDateAttributeValueArray;

    protected function setUp()
    {
        parent::setUp();
        $this->dateAttributeRepo = new EloquentDateAttributeRepository(
            resolve(EloquentDateAttribute::class)
        );
        $this->sortDateAttribute = function (EloquentDateAttribute $c1, EloquentDateAttribute $c2) {
            $id1 = $c1->getId();
            $id2 = $c2->getId();
            if ($id1 === $id2) {
                return 0;
            }

            return $id1 < $id2 ? -1 : 1;
        };
        $this->createDateAttributeValueArray = function (EloquentDateAttribute $eloquentDateAttribute) {
            return [
                'id' => $eloquentDateAttribute->getId(),
                'value' => $eloquentDateAttribute->getValue(),
                'createUserId' => $eloquentDateAttribute->getCreateUserId(),
                'createUserName' => $eloquentDateAttribute->getCreateUser()->getName(),
                'updateUserId' => $eloquentDateAttribute->getUpdateUserId(),
                'updateUserName' => $eloquentDateAttribute->getUpdateUser()->getName(),
                'createdAt' => $eloquentDateAttribute->getCreatedAt()->toIso8601String(),
                'updatedAt' => $eloquentDateAttribute->getUpdatedAt()->toIso8601String()
            ];
        };
    }

    public function testGetTypeahead()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create();
        /** @var Collection $dateAttributes */
        $dateAttributes = factory(EloquentDateAttribute::class)->times(2)->create([
            'create_user_id' => $user->getId(),
            'update_user_id' => $user->getId()
        ]);

        $results = $this->dateAttributeRepo->getTypeahead();

        $expectedResults = $dateAttributes
            ->sort($this->sortDateAttribute)
            ->map(function (EloquentDateAttribute $eloquentDateAttribute) {
                return [
                    'id' => $eloquentDateAttribute->getId(),
                    'value' => $eloquentDateAttribute->getValue()
                ];
            })->toArray();

        $this->assertEquals($expectedResults, $results->toValueArray());
    }

    public function testGetAll()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create();
        /** @var Collection $dateAttributes */
        $dateAttributes = factory(EloquentDateAttribute::class)->times(2)->create([
            'create_user_id' => $user->getId(),
            'update_user_id' => $user->getId()
        ]);

        $results = $this->dateAttributeRepo->getAll();

        $expectedResults = $dateAttributes
            ->sort($this->sortDateAttribute)
            ->map($this->createDateAttributeValueArray)
            ->toArray();

        $this->assertEquals($expectedResults, $results->toValueArray());
    }

    public function testCreateDateAttribute()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create();

        $dateAttribute = $this->dateAttributeRepo->create('dateAttribute1', new UserId($user->getId()));

        $this->assertEquals('dateAttribute1', $dateAttribute->getValue());
    }

    public function testCreateDateAttributeWhichAlreadyExists()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create();
        factory(EloquentDateAttribute::class)->create([
            'value' => 'dateAttribute1',
            'create_user_id' => $user->getId(),
            'update_user_id' => $user->getId()
        ]);

        $this->expectException(TimelineException::class);
        $this->dateAttributeRepo->create('dateAttribute1', new UserId($user->getId()));
    }

    public function testCreateDateAttributeWithNonExistingUser()
    {
        $this->expectException(TimelineException::class);
        $this->dateAttributeRepo->create('dateAttribute1', new UserId(1));
    }

    public function testBulkCreateDateAttributes()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create();
        factory(EloquentDateAttribute::class)->create([
            'value' => 'dateAttribute1',
            'create_user_id' => $user->getId(),
            'update_user_id' => $user->getId()
        ]);
        $dateAttributes = $this->dateAttributeRepo->bulkCreate(['dateAttribute1', 'dateAttribute2'], new UserId($user->getId()));
        $this->assertSame(2, count($dateAttributes));
        $this->assertSame('dateAttribute1', $dateAttributes[0]->getValue());
        $this->assertSame('dateAttribute2', $dateAttributes[1]->getValue());
    }

    public function testUpdateDateAttribute()
    {
        /** @var EloquentUser $createUser */
        $createUser = factory(EloquentUser::class)->create();
        /** @var EloquentDateAttribute $dateAttribute */
        $dateAttribute = factory(EloquentDateAttribute::class)->create([
            'value' => 'value',
            'create_user_id' => $createUser->getId(),
            'update_user_id' => $createUser->getId()
        ]);

        /** @var EloquentUser $updateUser */
        $updateUser = factory(EloquentUser::class)->create();

        $newDateAttribute = $this->dateAttributeRepo->update(
            new DateAttributeId($dateAttribute->getId()),
            'new_value',
            new UserId($updateUser->getId())
        );

        $this->assertSame($dateAttribute->getId(), $newDateAttribute->getId()->getValue());
        $this->assertSame('new_value', $newDateAttribute->getValue());
        $this->assertSame($updateUser->getId(), $newDateAttribute->getUpdateUserId()->getValue());
    }

    public function testUpdateDateAttributeWithNonExistingDateAttributeId()
    {
        $this->expectException(TimelineException::class);
        $this->dateAttributeRepo->update(
            new DateAttributeId(1),
            'new_value',
            new UserId(1)
        );
    }

    public function testUpdateDateAttributeWithExistingValue()
    {
        /** @var EloquentUser $createUser */
        $createUser = factory(EloquentUser::class)->create();
        factory(EloquentDateAttribute::class)->create([
            'value' => 'value1',
            'create_user_id' => $createUser->getId(),
            'update_user_id' => $createUser->getId()
        ]);
        /** @var EloquentDateAttribute $dateAttribute */
        $dateAttribute = factory(EloquentDateAttribute::class)->create([
            'value' => 'value2',
            'create_user_id' => $createUser->getId(),
            'update_user_id' => $createUser->getId()
        ]);

        /** @var EloquentUser $updateUser */
        $updateUser = factory(EloquentUser::class)->create();

        $this->expectException(TimelineException::class);
        $this->dateAttributeRepo->update(
            new DateAttributeId($dateAttribute->getId()),
            'value1',
            new UserId($updateUser->getId())
        );
    }

    public function testUpdateDateAttributeWithNonExistingUser()
    {
        /** @var EloquentUser $createUser */
        $createUser = factory(EloquentUser::class)->create();
        /** @var EloquentDateAttribute $dateAttribute */
        $dateAttribute = factory(EloquentDateAttribute::class)->create([
            'value' => 'value',
            'create_user_id' => $createUser->getId(),
            'update_user_id' => $createUser->getId()
        ]);

        $this->expectException(TimelineException::class);

        $this->dateAttributeRepo->update(
            new DateAttributeId($dateAttribute->getId()),
            'new_value',
            new UserId(1000)
        );
    }

    public function testDeleteDateAttribute()
    {
        /** @var EloquentUser $createUser */
        $createUser = factory(EloquentUser::class)->create();
        /** @var EloquentDateAttribute $dateAttribute */
        $dateAttribute = factory(EloquentDateAttribute::class)->create([
            'create_user_id' => $createUser->getId(),
            'update_user_id' => $createUser->getId()
        ]);
        $this->dateAttributeRepo->delete(new DateAttributeId($dateAttribute->getId()));
        $this->assertEmpty(EloquentDateAttribute::all());
    }

    public function testDeleteDateAttributeWithNonExistingID()
    {
        $this->expectException(TimelineException::class);
        $this->dateAttributeRepo->delete(new DateAttributeId(1));
    }
}