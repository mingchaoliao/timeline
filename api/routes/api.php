<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/event', 'EventController@get');
Route::get('/event/search', 'EventController@search');
Route::get('/event/{id}', 'EventController@getById');

Route::get('/dateAttribute/typeahead', 'DateAttributeController@getTypeahead');

Route::get('/catalog/typeahead', 'CatalogController@getTypeahead');

Route::get('/period/typeahead', 'PeriodController@getTypeahead');

Route::post('/register', 'UserController@register');
Route::post('/login', 'UserController@login');

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/user/current', 'UserController@getCurrentUser');
    Route::put('/user/{id}', 'UserController@update');
});

Route::group(['middleware' => ['auth:api', 'editor']], function () {
    Route::post('/timeline', 'TimelineController@create');

    Route::post('/event', 'EventController@create');
    Route::post('/event/bulk', 'EventController@bulkCreate');
    Route::put('/event/{id}', 'EventController@update');
    Route::delete('/event/{id}', 'EventController@delete');

    Route::get('/period', 'PeriodController@getAll');
    Route::post('/period', 'PeriodController@createPeriod');
    Route::post('/period/bulk', 'PeriodController@bulkCreate');
    Route::put('/period/{id}', 'PeriodController@update');
    Route::delete('/period/{id}', 'PeriodController@delete');

    Route::get('/catalog', 'CatalogController@getAll');
    Route::post('/catalog', 'CatalogController@createCatalog');
    Route::post('/catalog/bulk', 'CatalogController@bulkCreate');
    Route::put('/catalog/{id}', 'CatalogController@update');
    Route::delete('/catalog/{id}', 'CatalogController@delete');

    Route::get('/dateAttribute', 'DateAttributeController@getAll');
    Route::post('/dateAttribute', 'DateAttributeController@createDateAttribute');
    Route::post('/dateAttribute/bulk', 'DateAttributeController@bulkCreate');
    Route::put('/dateAttribute/{id}', 'DateAttributeController@update');
    Route::delete('/dateAttribute/{id}', 'DateAttributeController@delete');

    Route::post('/image', 'ImageController@upload');
    Route::put('/image/{id}', 'ImageController@update');


});

Route::group(['middleware' => ['auth:api', 'admin']], function () {
    Route::get('/user', 'UserController@getAllUser');
});