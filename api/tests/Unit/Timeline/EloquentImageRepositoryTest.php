<?php
/**
 * Created by PhpStorm.
 * User: liaom
 * Date: 2/8/19
 * Time: 6:10 PM
 */

namespace Tests\Unit\Timeline;


use App\Timeline\Domain\Collections\ImageIdCollection;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentImage;
use App\Timeline\Infrastructure\Persistence\Eloquent\Models\EloquentUser;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentImageRepository;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @covers \App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentImageRepository
 * @covers \App\Timeline\Exceptions\TimelineException
 */
class EloquentImageRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var EloquentImageRepository
     */
    private $imageRepository;

    protected function setUp()
    {
        parent::setUp();
        $this->imageRepository = new EloquentImageRepository(
            resolve(EloquentImage::class),
            resolve(ConnectionInterface::class)
        );
    }

    public function testGetRawImage()
    {
        /** @var EloquentUser $user */
        $user = factory(EloquentUser::class)->create();

        /** @var EloquentImage $image */
        $image = factory(EloquentImage::class)->create([
            'create_user_id' => $user->getId(),
            'update_user_id' => $user->getId()
        ]);

        $this->assertSame(
            $image->getId(),
            $this->imageRepository->getRawByIds(
                ImageIdCollection::createFromArray([$image->getId()])
            )[0]->getId()
        );
    }
}