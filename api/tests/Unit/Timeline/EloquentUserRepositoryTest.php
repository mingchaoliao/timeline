<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/8/19
 * Time: 5:19 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Models\UserToken;
use App\Timeline\Domain\ValueObjects\Email;
use App\Timeline\Domain\ValueObjects\UserId;
use App\Timeline\Exceptions\TimelineException;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentUser;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * @covers \App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentUserRepository
 * @covers \App\Timeline\Exceptions\TimelineException
 */
class EloquentUserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var EloquentUserRepository
     */
    private $userRepository;
    /**
     * @var callable
     */
    private $constructUserData;
    /**
     * @var callable
     */
    private $sortUsers;

    protected function setUp()
    {
        parent::setUp();
        $this->userRepository = new EloquentUserRepository(
            resolve(EloquentUser::class),
            resolve(Hasher::class),
            Auth::guard('api')
        );
        $this->constructUserData = function (EloquentUser $eloquentUser) {
            return [
                'id' => $eloquentUser->getId(),
                'name' => $eloquentUser->getName(),
                'email' => $eloquentUser->getEmail(),
                'isAdmin' => $eloquentUser->isAdmin(),
                'isEditor' => $eloquentUser->isEditor(),
                'createdAt' => $eloquentUser->getCreatedAt()->toIso8601String(),
                'updatedAt' => $eloquentUser->getUpdatedAt()->toIso8601String()
            ];
        };
        $this->sortUsers = function (EloquentUser $u1, EloquentUser $u2) {
            $id1 = $u1->getId();
            $id2 = $u2->getId();
            if ($id1 === $id2) {
                return 0;
            }

            return $id1 < $id2 ? -1 : 1;
        };
    }

    public function testGetCurrentUser()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create();

        $this->loginAs($user->getId());

        $this->assertSame(
            $user->getId(),
            $this->userRepository->getCurrentUser()->getId()->getValue()
        );
    }

    public function testGetCurrentUserWhenUserHavenSignedIn()
    {
        $this->assertSame(
            null,
            $this->userRepository->getCurrentUser()
        );
    }

    public function testLogin()
    {
        $email = 'test1@test.com';
        $password = 'test1';

        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create([
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        $token = $this->userRepository->login(new Email($email), $password);

        $this->assertInstanceOf(UserToken::class, $token);
    }

    public function testLoginWithWrongEmailOrPassword()
    {
        $email = 'test1@test.com';
        $password = 'test1';

        factory(EloquentUser::class)->create([
            'email' => $email,
            'password' => Hash::make($password)
        ]);

        $this->expectException(TimelineException::class);

        $this->userRepository->login(new Email($email), 'something');
    }

    public function testGetAllUser()
    {
        /** @var Collection $users */
        $users = factory(EloquentUser::class)
            ->times(10)
            ->create();

        $this->assertSame(
            $users->sort($this->sortUsers)->map($this->constructUserData)->toArray(),
            $this->userRepository->getAll()->toValueArray()
        );
    }

    public function testCreateUser()
    {
        $user = $this->userRepository->create(
            'test',
            new Email('test@test.com'),
            '1234',
            true,
            false
        );

        $this->assertSame('test', $user->getName());
        $this->assertSame('test@test.com', $user->getEmail()->getValue());
        $this->assertSame(true, $user->isAdmin());
        $this->assertSame(false, $user->isEditor());
        $this->assertTrue(Hash::check('1234', EloquentUser::find($user->getId()->getValue())->getPasswordHash()));
    }

    public function testCreatUserWithExistingEmail()
    {
        factory(EloquentUser::class)->create([
            'email' => 'test@test.com'
        ]);

        $this->expectException(TimelineException::class);
        $this->userRepository->create(
            'test',
            new Email('test@test.com'),
            '1234',
            true,
            false
        );
    }

    public function testUpdateUserWithNonExistingId()
    {
        $this->expectException(TimelineException::class);
        $this->userRepository->update(new UserId(1));
    }

    public function testUpdateUserName()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create([
            'name' => 'name1'
        ]);

        $updatedUser = $this->userRepository->update(
            new UserId($user->getId()),
            'name2'
        );

        $this->assertSame('name2', $updatedUser->getName());
    }

    public function testUpdatePassword()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create([
            'password' => '1234'
        ]);

        $this->userRepository->update(
            new UserId($user->getId()),
            null,
            '5678'
        );

        $this->assertTrue(Hash::check('5678', EloquentUser::find($user->getId())->getPasswordHash()));
    }

    public function testUpdateAdminPrivilege()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create([
            'is_admin' => 1
        ]);

        $updatedUser = $this->userRepository->update(
            new UserId($user->getId()),
            null,
            null,
            false
        );

        $this->assertFalse($updatedUser->isAdmin());
    }

    public function testUpdateEditorPrivilege()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create([
            'is_editor' => 0
        ]);

        $updatedUser = $this->userRepository->update(
            new UserId($user->getId()),
            null,
            null,
            null,
            true
        );

        $this->assertTrue($updatedUser->isEditor());
    }
}