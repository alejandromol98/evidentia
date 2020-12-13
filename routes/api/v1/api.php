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
    Route::middleware('auth:api')->get('/user/all','api\v1\user\UserController@index');
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
Route::middleware(['checknotnull:Evidence','evidencemine'])->group(function () {


    Route::middleware(['checkuploadevidences'])->group(function () {
        Route::post('/evidence/reedit', 'api\v1\EvidenceController@reedit')->name('evidence.reedit');
    });
});
});

