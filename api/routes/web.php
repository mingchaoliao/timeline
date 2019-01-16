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
    return view('welcome');
});

Route::get('/admin/image/{path}', function ($path) {
    $image = null;
    if(Storage::exists(\App\Timeline\Domain\Models\Image::TMP_PATH . '/' . $path)) {
        $image = Storage::get(\App\Timeline\Domain\Models\Image::TMP_PATH . '/' . $path);
    }
    if(Storage::exists(\App\Timeline\Domain\Models\Image::PATH . '/' . $path)) {
        $image = Storage::get(\App\Timeline\Domain\Models\Image::PATH . '/' . $path);
    }
    return \Intervention\Image\Facades\Image::make($image)->response();
});

Route::get('/image/{path}', function ($path) {
    return \Intervention\Image\Facades\Image::make(
        \Illuminate\Support\Facades\Storage::get(
            \App\Timeline\Domain\Models\Image::PATH . '/' . $path
        )
    )->response();
});

Route::get('/home', 'HomeController@index')->name('home');