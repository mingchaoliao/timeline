<?php

namespace App\Http\Controllers\Api;

use App\Timeline\Domain\Models\Image;
use App\Http\Controllers\Controller;
use App\Timeline\Domain\Services\ImageService;
use App\Timeline\Domain\ValueObjects\ImageId;
use App\Timeline\Infrastructure\Persistence\Eloquent\Repositories\EloquentImageRepository;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    /**
     * @var ImageService
     */
    private $imageService;

    /**
     * ImageController constructor.
     * @param ImageService $imageService
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function upload(Request $request)
    {
        $this->validate(
            $request,
            [
                'image' => 'required|image'
            ],
            [
                'required' => 'Missing image file',
                'image' => 'Invalid image file'
            ]
        );

       $image = $this->imageService->upload(
           $request->file('image'),
           $request->get('description') ?? null
       );

       return response()->json($image);
    }

    public function update(string $id, Request $request) {
        $this->validate(
            $request,
            [
                'description' => 'required'
            ]
        );
        $image = $this->imageService->update(ImageId::createFromString($id),$request->get('description'));
        return response()->json($image);
    }
}
