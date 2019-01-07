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

Route::get('/dateAttribute', 'DateAttributeController@get');

Route::get('/dateFormat', 'DateFormatController@get');

Route::get('/catalog', 'CatalogController@get');

Route::get('/period', 'PeriodController@get');

Route::post('/register', 'UserController@register');
Route::post('/login', 'UserController@login');

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/user', 'UserController@getCurrentUser');
});

Route::group(['middleware' => ['auth:api', 'admin']], function () {
    Route::post('/timeline', 'TimelineController@create');

    Route::post('/event/bulkCreate', 'EventController@bulkCreate');
    Route::post('/event', 'EventController@createNew');
    Route::delete('/event/{id}', 'EventController@deleteById');
    Route::put('/event/{id}', 'EventController@updateEvent');


    Route::post('/period/bulkCreate', 'PeriodController@bulkCreate');
    Route::post('/period', 'PeriodController@createPeriod');
    Route::get('/period/detail', 'PeriodController@getDetail');

    Route::post('/catalog/bulkCreate', 'CatalogController@bulkCreate');
    Route::post('/catalog', 'CatalogController@createCatalog');

    Route::post('/dateAttribute/bulkCreate', 'DateAttributeController@bulkCreate');
    Route::post('/dateAttribute', 'DateAttributeController@createDateAttribute');

    Route::post('/image', 'ImageController@uploadImage');

    Route::get('/user/all', 'UserController@getAllUser');
    Route::put('/user/grantOrRevokeAdminPrivilege', 'UserController@grantOrRevokeAdminPrivilege');
});