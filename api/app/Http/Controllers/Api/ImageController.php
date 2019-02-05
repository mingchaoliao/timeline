<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Timeline\App\Validators\ValidatorFactory;
use App\Timeline\Domain\Services\ImageService;
use App\Timeline\Domain\ValueObjects\ImageId;
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

    public function upload(Request $request, ValidatorFactory $validatorFactory)
    {
        $validatorFactory->validate($request->all(), [
            'image' => 'required|image',
            'description' => 'nullable|string'
        ]);

        $image = $this->imageService->upload(
            $request->file('image'),
            $request->get('description') ?? null
        );

        return response()->json($image);
    }

    public function update(string $id, Request $request, ValidatorFactory $validatorFactory)
    {
        $params = $request->all();
        $params['id'] = $id;

        $validatorFactory->validate($request->all(), [
            'id' => 'required|id',
            'description' => 'required|string'
        ]);

        $image = $this->imageService->update(ImageId::createFromString($id), $request->get('description'));
        return response()->json($image);
    }
}
