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

Route::get('/dateFormat', 'DateFormatController@getAll');

Route::get('/catalog/typeahead', 'CatalogController@getTypeahead');

Route::get('/period/typeahead', 'PeriodController@getTypeahead');

Route::post('/register', 'UserController@register');
Route::post('/login', 'UserController@login');

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/user/current', 'UserController@getCurrentUser');
});

Route::group(['middleware' => ['auth:api', 'admin']], function () {
    Route::post('/timeline', 'TimelineController@create');

    Route::post('/event', 'EventController@create');
    Route::post('/event/bulk', 'EventController@bulkCreate');
    Route::put('/event/{id}', 'EventController@update');
    Route::delete('/event/{id}', 'EventController@delete');

    Route::get('/period', 'PeriodController@getAll');
    Route::post('/period', 'PeriodController@createPeriod');
    Route::post('/period/bulk', 'PeriodController@bulkCreate');
    Route::put('/period', 'PeriodController@update');
    Route::delete('/period', 'PeriodController@delete');

    Route::get('/catalog', 'CatalogController@getAll');
    Route::post('/catalog', 'CatalogController@createCatalog');
    Route::post('/catalog/bulk', 'CatalogController@bulkCreate');
    Route::put('/catalog', 'CatalogController@update');
    Route::delete('/catalog', 'CatalogController@delete');

    Route::get('/dateAttribute', 'DateAttributeController@getAll');
    Route::post('/dateAttribute', 'DateAttributeController@createDateAttribute');
    Route::post('/dateAttribute/bulk', 'DateAttributeController@bulkCreate');
    Route::put('/dateAttribute', 'DateAttributeController@update');
    Route::delete('/dateAttribute', 'DateAttributeController@delete');

    Route::post('/image', 'ImageController@uploadImage');

    Route::get('/user', 'UserController@getAllUser');
    Route::put('/user', 'UserController@update');
});