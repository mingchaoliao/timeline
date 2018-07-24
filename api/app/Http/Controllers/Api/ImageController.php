<?php

namespace App\Http\Controllers\Api;

use App\DomainModels\Image;
use App\Http\Controllers\Controller;
use App\Repositories\ImageRepository;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    private $imageRepository;

    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function uploadImage(Request $request)
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

        $uploadedFile = $request->file('image');

        $extension = $uploadedFile->getClientOriginalExtension();

        $name = str_random(32) . '.' . $extension;

        $uploadedFile->storeAs(Image::TMP_PATH,$name);

        return response()->json([
            'path' => $name,
            'originalName' => $uploadedFile->getClientOriginalName()
        ]);
    }
}
