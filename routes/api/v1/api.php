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
    Route::get('user/all','api\v1\user\UserController@index');
    Route::get('/user/view/{id}','api\v1\user\UserController@view');
    Route::post('/user/new', 'api\v1\user\UserController@new')->name('user.new');
    Route::post('/user/edit/{id}', 'api\v1\user\UserController@edit')->name('user.edit');
    Route::post('/user/remove/{id}', 'api\v1\user\UserController@remove')->name('user.remove');
});

/**
 *  EVIDENCES
 */
Route::group(['prefix' => '{instance}/api/v1'], function(){
    Route::middleware('auth:api')->get('/evidence/list', 'api\v1\EvidenceController@list')->name('evidence.list');
    Route::middleware('auth:api')->get('/evidence/create', 'api\v1\EvidenceController@create')->name('evidence.create');
    Route::middleware('auth:api')->post('/evidence/draft', 'api\v1\EvidenceController@draft')->name('evidence.draft');
    Route::middleware('auth:api')->post('/evidence/publish', 'api\v1\EvidenceController@publish')->name('evidence.publish');
    Route::middleware('auth:api')->post('/evidence/draft/edit/{id}', 'api\v1\EvidenceController@draft_edit')->name('evidence.draft.edit');
    Route::middleware('auth:api')->post('/evidence/publish/edit/{id}', 'api\v1\EvidenceController@publish_edit')->name('evidence.publish.edit');
    Route::middleware('auth:api')->get('/evidence/view/{id}', 'api\v1\EvidenceController@view')->name('evidence.view');
    Route::middleware('auth:api')->post('/evidence/remove/{id}', 'api\v1\EvidenceController@remove')->name('evidence.remove');
    Route::middleware('auth:api')->post('/evidence/reedit/{id}', 'api\v1\EvidenceController@reedit')->name('evidence.reedit');
});



/**
 *  EVIDENCES COORDINATOR
 */
Route::group(['prefix' => '{instance}/api/v1/coordinator'], function(){
    Route::middleware('auth:api')->get('/evidence/list/all', 'api\v1\EvidenceCoordinatorController@all')->name('coordinator.evidence.list.all');
    Route::middleware('auth:api')->get('/evidence/list/pending', 'api\v1\EvidenceCoordinatorController@pending')->name('coordinator.evidence.list.pending');
    Route::middleware('auth:api')->get('/evidence/list/accepted', 'api\v1\EvidenceCoordinatorController@accepted')->name('coordinator.evidence.list.accepted');
    Route::middleware('auth:api')->get('/evidence/list/rejected', 'api\v1\EvidenceCoordinatorController@rejected')->name('coordinator.evidence.list.rejected');
    Route::middleware('auth:api')->post('/evidenceCoordinator/accept/{id}', 'api\v1\EvidenceCoordinatorController@accept')->name('evidence.coordinator.accept');
    Route::middleware('auth:api')->post('/evidenceCoordinator/reject/{id}', 'api\v1\EvidenceCoordinatorController@reject')->name('evidence.coordinator.reject');



});


/**
 *    MEETINGS
 */

Route::group(['prefix' => '{instance}/api/v1'], function(){
    Route::middleware('auth:api')->get('/meeting/list', 'api\v1\MeetingController@list')->name('meeting.mylist');

});

/**
 *    MEETINGS SECRETARY
 */


Route::group(['prefix' => '{instance}/api/v1/secretary'], function(){
    Route::middleware('auth:api')->get('/meeting/list', 'api\v1\MeetingSecretaryController@list')->name('meeting.list');
    //Route::middleware('auth:api')->get('/meeting/create', 'api\v1\MeetingSecretaryController@create')->name('secretary.meeting.create');
    Route::middleware('auth:api')->post('/meeting/new', 'api\v1\MeetingSecretaryController@new')->name('secretary.meeting.list');
    Route::middleware('auth:api')->post('/meeting/edit/{id}', 'api\v1\MeetingSecretaryController@save')->name('secretary.meeting.list');
    Route::middleware('auth:api')->post('/meeting/remove/{id}', 'api\v1\MeetingSecretaryController@remove')->name('secretary.meeting.list');


});

/**
 *    DEFAULT LISTS SECRETARY
 */

Route::group(['prefix' => '{instance}/api/v1/secretary'], function(){
    Route::middleware('auth:api')->get('/defaultlist/list', 'api\v1\DefaultListSecretaryController@list')->name('secretary.defaultlist.list');
    Route::middleware('auth:api')->post('/defaultlist/new', 'api\v1\DefaultListSecretaryController@new')->name('secretary.defaultlist.new');
    Route::middleware('auth:api')->post('/defaultlist/edit/{id}', 'api\v1\DefaultListSecretaryController@save')->name('secretary.defaultlist.save');
    Route::middleware('auth:api')->post('/defaultlist/remove/{id}', 'api\v1\DefaultListSecretaryController@remove')->name('secretary.defaultlist.remove');


});

/**
 *  BONUS
 */
Route::prefix('{instance}/api/v1')->group(function() {
    Route::get('/bonus/list', 'api\v1\BonusController@list')->name('bonus.list');

    Route::middleware(['checkregisterbonus', 'auth:api'])->group(function () {
        Route::post('/bonus/new', 'api\v1\BonusController@new')->name('bonus.new');
    });
    Route::post('/bonus/edit/{id}', 'api\v1\BonusController@edit')->name('bonus.edit');
    Route::post('/bonus/remove/{id}', 'api\v1\BonusController@remove')->name('bonus.remove');

});
