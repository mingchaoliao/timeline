<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/7/19
 * Time: 7:17 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\ValueObjects\PeriodId;
use App\Timeline\Domain\ValueObjects\UserId;
use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentPeriod;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentUser;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentPeriodRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @covers \App\Timeline\Exceptions\TimelineException
 * @covers \App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentPeriodRepository
 */
class EloquentPeriodRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var EloquentPeriodRepository
     */
    private $periodRepo;
    /**
     * @var callable
     */
    private $sortPeriod;
    /**
     * @var callable
     */
    private $createPeriodValueArray;

    protected function setUp()
    {
        parent::setUp();
        $this->periodRepo = new EloquentPeriodRepository(
            resolve(EloquentPeriod::class)
        );
        $this->sortPeriod = function (EloquentPeriod $c1, EloquentPeriod $c2) {
            $id1 = $c1->getId();
            $id2 = $c2->getId();
            if ($id1 === $id2) {
                return 0;
            }

            return $id1 < $id2 ? -1 : 1;
        };
        $this->createPeriodValueArray = function (EloquentPeriod $eloquentPeriod) {
            return [
                'id' => $eloquentPeriod->getId(),
                'value' => $eloquentPeriod->getValue(),
                'numberOfEvents' => $eloquentPeriod->getNumberOfEvents(),
                'createUserId' => $eloquentPeriod->getCreateUserId(),
                'createUserName' => $eloquentPeriod->getCreateUser()->getName(),
                'updateUserId' => $eloquentPeriod->getUpdateUserId(),
                'updateUserName' => $eloquentPeriod->getUpdateUser()->getName(),
                'createdAt' => $eloquentPeriod->getCreatedAt()->toIso8601String(),
                'updatedAt' => $eloquentPeriod->getUpdatedAt()->toIso8601String()
            ];
        };
    }

    public function testGetTypeahead()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create();
        /** @var Collection $periods */
        $periods = factory(EloquentPeriod::class)->times(2)->create([
            'create_user_id' => $user->getId(),
            'update_user_id' => $user->getId()
        ]);

        $results = $this->periodRepo->getTypeahead();

        $expectedResults = $periods
            ->sort($this->sortPeriod)
            ->map(function (EloquentPeriod $eloquentPeriod) {
                return [
                    'id' => $eloquentPeriod->getId(),
                    'value' => $eloquentPeriod->getValue()
                ];
            })->toArray();

        $this->assertEquals($expectedResults, $results->toValueArray());
    }

    public function testGetAll()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create();
        /** @var Collection $periods */
        $periods = factory(EloquentPeriod::class)->times(2)->create([
            'create_user_id' => $user->getId(),
            'update_user_id' => $user->getId()
        ]);

        $results = $this->periodRepo->getAll();

        $expectedResults = $periods
            ->sort($this->sortPeriod)
            ->map($this->createPeriodValueArray)
            ->toArray();

        $this->assertEquals($expectedResults, $results->toValueArray());
    }

    public function testCreatePeriod()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create();

        $period = $this->periodRepo->create('period1', new UserId($user->getId()));

        $this->assertEquals('period1', $period->getValue());
    }

    public function testCreatePeriodWhichAlreadyExists()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create();
        factory(EloquentPeriod::class)->create([
            'value' => 'period1',
            'create_user_id' => $user->getId(),
            'update_user_id' => $user->getId()
        ]);

        $this->expectException(TimelineException::class);
        $this->periodRepo->create('period1', new UserId($user->getId()));
    }

    public function testCreatePeriodWithNonExistingUser()
    {
        $this->expectException(TimelineException::class);
        $this->periodRepo->create('period1', new UserId(1));
    }

    public function testBulkCreatePeriods()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create();
        factory(EloquentPeriod::class)->create([
            'value' => 'period1',
            'create_user_id' => $user->getId(),
            'update_user_id' => $user->getId()
        ]);
        $periods = $this->periodRepo->bulkCreate(['period1', 'period2'], new UserId($user->getId()));
        $this->assertSame(2, count($periods));
        $this->assertSame('period1', $periods[0]->getValue());
        $this->assertSame('period2', $periods[1]->getValue());
    }

    public function testUpdatePeriod()
    {
        /** @var EloquentUser $createUser */
        $createUser = factory(EloquentUser::class)->create();
        /** @var EloquentPeriod $period */
        $period = factory(EloquentPeriod::class)->create([
            'value' => 'value',
            'create_user_id' => $createUser->getId(),
            'update_user_id' => $createUser->getId()
        ]);

        /** @var EloquentUser $updateUser */
        $updateUser = factory(EloquentUser::class)->create();

        $newPeriod = $this->periodRepo->update(
            new PeriodId($period->getId()),
            'new_value',
            new UserId($updateUser->getId())
        );

        $this->assertSame($period->getId(), $newPeriod->getId()->getValue());
        $this->assertSame('new_value', $newPeriod->getValue());
        $this->assertSame($updateUser->getId(), $newPeriod->getUpdateUserId()->getValue());
    }

    public function testUpdatePeriodWithNonExistingPeriodId()
    {
        $this->expectException(TimelineException::class);
        $this->periodRepo->update(
            new PeriodId(1),
            'new_value',
            new UserId(1)
        );
    }

    public function testUpdatePeriodWithExistingValue()
    {
        /** @var EloquentUser $createUser */
        $createUser = factory(EloquentUser::class)->create();
        factory(EloquentPeriod::class)->create([
            'value' => 'value1',
            'create_user_id' => $createUser->getId(),
            'update_user_id' => $createUser->getId()
        ]);
        /** @var EloquentPeriod $period */
        $period = factory(EloquentPeriod::class)->create([
            'value' => 'value2',
            'create_user_id' => $createUser->getId(),
            'update_user_id' => $createUser->getId()
        ]);

        /** @var EloquentUser $updateUser */
        $updateUser = factory(EloquentUser::class)->create();

        $this->expectException(TimelineException::class);
        $this->periodRepo->update(
            new PeriodId($period->getId()),
            'value1',
            new UserId($updateUser->getId())
        );
    }

    public function testUpdatePeriodWithNonExistingUser()
    {
        /** @var EloquentUser $createUser */
        $createUser = factory(EloquentUser::class)->create();
        /** @var EloquentPeriod $period */
        $period = factory(EloquentPeriod::class)->create([
            'value' => 'value',
            'create_user_id' => $createUser->getId(),
            'update_user_id' => $createUser->getId()
        ]);

        $this->expectException(TimelineException::class);

        $this->periodRepo->update(
            new PeriodId($period->getId()),
            'new_value',
            new UserId(1000)
        );
    }

    public function testDeletePeriod()
    {
        /** @var EloquentUser $createUser */
        $createUser = factory(EloquentUser::class)->create();
        /** @var EloquentPeriod $period */
        $period = factory(EloquentPeriod::class)->create([
            'create_user_id' => $createUser->getId(),
            'update_user_id' => $createUser->getId()
        ]);
        $this->periodRepo->delete(new PeriodId($period->getId()));
        $this->assertEmpty(EloquentPeriod::all());
    }

    public function testDeletePeriodWithNonExistingID()
    {
        $this->expectException(TimelineException::class);
        $this->periodRepo->delete(new PeriodId(1));
    }
}