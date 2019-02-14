<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('horizon.index');
});

Route::get('/home', function () {
    return redirect()->route('horizon.index');
});

Route::get('/admin/images/{path}', function ($path) {
    $image = null;
    if (Storage::exists(\App\Timeline\Domain\Models\Image::TMP_PATH . '/' . $path)) {
        $image = Storage::get(\App\Timeline\Domain\Models\Image::TMP_PATH . '/' . $path);
        return \Intervention\Image\Facades\Image::make($image)->response();
    }
    return response()->json(false, 404);
});

Auth::routes();
