<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


/**
 * USERS  LOGIN
 */

Route::prefix('{instance}/api/v1')->group(function(){
    Route::post('/login', 'api\v1\LoginController@login');
    Route::middleware('auth:api')->get('/all','api\v1\user\UserController@index');
    Route::get('/user/view/{id}','api\v1\user\UserController@view');
    Route::post('/user/new', 'api\v1\user\UserController@new')->name('user.new');
    Route::post('/user/edit/{id}', 'api\v1\user\UserController@edit')->name('user.edit');
    Route::post('/user/remove/{id}', 'api\v1\user\UserController@remove')->name('user.remove');
});

/**
 *  EVIDENCES
 */
Route::group(['prefix' => '{instance}/api/v1'], function(){
Route::get('/evidence/list', 'api\v1\EvidenceController@list')->name('evidence.list');
Route::middleware(['checkuploadevidences'])->group(function () {
    Route::get('/evidence/create', 'api\v1\EvidenceController@create')->name('evidence.create');
    Route::post('/evidence/draft', 'api\v1\EvidenceController@draft')->name('evidence.draft');
    Route::post('/evidence/publish', 'api\v1\EvidenceController@publish')->name('evidence.publish');
    Route::post('/evidence/draft/edit/{id}', 'api\v1\EvidenceController@draft_edit')->name('evidence.draft.edit');
    Route::post('/evidence/publish/edit/{id}', 'api\v1\EvidenceController@publish_edit')->name('evidence.publish.edit');
});;

Route::get('/evidence/view/{id}', 'api\v1\EvidenceController@view')->name('evidence.view');
Route::post('/evidence/remove/{id}', 'api\v1\EvidenceController@remove')->name('evidence.remove');
Route::post('/evidence/reedit/{id}', 'api\v1\EvidenceController@reedit')->name('evidence.reedit');
Route::middleware(['checknotnull:Evidence','evidencemine'])->group(function () {


    Route::middleware(['checkuploadevidences'])->group(function () {

    });
});
});


/**
 *  BONUS
 */

Route::prefix('{instance}/api/v1')->group(function(){
    Route::get('/bonus/list', 'api\v1\BonusController@list')->name('bonus.list');

    Route::middleware(['checkregisterbonus', 'auth:api'])->group(function () {
        Route::post('/bonus/new', 'api\v1\BonusController@new')->name('bonus.new');
    });
});
