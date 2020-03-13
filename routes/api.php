<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register the API routes for your application as
| the routes are automatically authenticated using the API guard and
| loaded automatically by this application's RouteServiceProvider.
|
 */

Route::resource('/cruds','CrudsController', ['expect'=>['edit','show','store']]);

Route::group(['middleware' => 'auth:api', 'prefix' => 'auth'], function () {
    Route::post('login_by_user_id','AuthController@authenticateByUserId');
    Route::get('getCurrentRole/{project_id}','AuthController@getCurrentRole');
    Route::get('check','AuthController@check');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'bootstrap'], function (){
    Route::get('/', 'BootstrapController@bootstrap');
    Route::put('/change-projects-listing-type', 'BootstrapController@changeProjectsListingType');
    Route::get('/get-recent-datas', 'BootstrapController@getRecentDatas');
    Route::put('/disable-autocomplete-data', 'BootstrapController@disableAutocompleteData');
    Route::get('/active-subscription', 'BootstrapController@getActiveSubscription');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'payments'], function () {
    Route::get('/get-payment-methods/{customer}', 'PaymentMethodController@list');
});

Route::group(['prefix' => 'files'], function () {
    Route::post('upload', 'FileController@upload');
    Route::delete('delete/{id}', 'FileController@deleteFile');
    Route::delete('deleteFinalFile/{projectId}/{id}', 'FileController@deleteFinalFile');
    Route::post('deleteFinalFiles', 'FileController@deleteFinalFiles');
    Route::get('get_file/{file_id}', 'FileController@getById');
    Route::post('videocaptureupload', 'FileController@videoCaptureUpload');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'projects'], function () {
    Route::get('bootstrap', 'BootstrapController@bootstrap');
    Route::resource('/', 'ProjectController');
    Route::post('projects/send', 'ProjectController@send');
    Route::delete('delete_project/{project_id}', 'ProjectController@deleteProject');
    Route::get('details/{project_id}', 'ProjectController@getDetails');
    Route::put('update/{project_id}', 'ProjectController@updateProject');
    Route::put('change_status/{project_id}/{status}', 'ProjectController@changeStatus');
    Route::post('add-team-member', 'ProjectController@addTeamMember');
    Route::delete('delete-team-member/{project_id}/{member_id}', 'ProjectController@deleteTeamMember');

    Route::post('create', 'ProjectController@store');
/*         Route::post('sendProofs', 'ProjectController@sendProofs'); */
    Route::get('{project_id}/revisions', 'ProjectController@getRevisions');
    Route::get('{revision_id}/partial_proofs', 'RevisionController@getPartialProofs');
    Route::get('load/{project_id}/{revison_id}', 'ProjectController@loadInitialRevision');
    Route::get('send_project/{project_id}/{user_type}', 'ProjectController@sendProject');
    Route::post('approve_project', 'ProjectController@approveProject');
    Route::get('get_user_rol/{project_id}', 'ProjectController@getCurrentUserRol');
    Route::get('submit_corrections/{project_id}', 'ProjectController@submitCorrections');
    Route::get('getFinalFiles/{project_id}', 'ProjectController@getFinalFiles');
    Route::post('sendFinalFiles', 'ProjectController@sendFinalFiles');
    Route::post('inviteCollaborators', 'ProjectController@inviteCollaborators');
    Route::post('save-creative-brief', 'ProjectController@saveCreativeBrief');
    Route::get('creative-brief/{project_id}', 'ProjectController@getCreativeBrief');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'proof'], function () {
    Route::post('save', 'ProofController@saveProofState');
    Route::post('create_issue', 'ProofController@createIssue');
    Route::delete('delete_issue/{issue_id}', 'ProofController@deleteIssue');
    Route::post('add_comment', 'ProofController@addComment');
    Route::delete('delete_comment/{comment_id}', 'ProofController@deleteComment');
    Route::get('change_proof_status/{proof_id}/{status}', 'ProofController@changeProofStatus');
    Route::get('change_issue_status/{issue_id}/{status}/{project_typefre}', 'ProofController@changeIssueStatus');
    Route::delete('delete_proof/{proof_id}', 'ProofController@deleteProof');
    Route::delete('delete_issue_unread_comments/{issue_id}', 'ProofController@deleteIssueUnreadComments');
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'revisions'], function () {
    Route::post('create', 'RevisionController@create');
    Route::delete('delete/{project_id}', 'RevisionController@remove');
    Route::get('get_by_project/{project_id}', 'RevisionController@getRevisionsByProject');
    Route::get('load_revision_by_id/{revision_id}', 'RevisionController@loadRevisionById');
    /* Route::get('change_revision_status/{project_id}/{version}/{status}', 'RevisionController@changeRevisionStatus'); */
});

//Extension API routes
Route::group(['prefix' => 'v1'], function () {
   Route::post('login', 'API\V1\AuthController@login');
   Route::get('extension-data/{user_id}/{project_id}', 'API\V1\APIController@getExtensionData')
       ->middleware('auth:api');

   Route::middleware(['authorized'])->group(function () {
       Route::get('projects', 'API\V1\APIController@projects');
       Route::post('upload', 'API\V1\APIController@uploadProofs');
   });
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'teams'], function () {
    Route::post('invite-members', 'TeamController@inviteMembers');
});

//Admin
Route::group(['middleware' => 'auth:api, dev', 'prefix' => 'admin'], function () {
    //Users
    Route::group(['before' => 'auth', 'prefix' => 'users'], function () {
        Route::get('all', 'Admin\UserController@list');
        Route::get('active-subscription/{user}', 'Admin\UserController@getActiveSubscription');
        Route::delete('delete/{id}', 'Admin\UserController@delete');
        Route::post('search', 'Admin\UserController@search');
    });
});

//Subscriptions & payments
Route::group(['middleware' => 'auth:api', 'prefix' => 'settings'], function () {
    Route::post('/subscribe-with-new-card', 'SubscriptionController@subscribeWithNewCard');
    Route::post('/subscribe-with-exiting-card', 'SubscriptionController@subscribeWithExitingCard');
    Route::post('/swap-subscription-with-new-card', 'SubscriptionController@swapWithNewCard');
    Route::post('/swap-subscription-with-exiting-card', 'SubscriptionController@swapWithExitingCard');
    Route::post('/resume-subscription', 'SubscriptionController@resumeSubscription');
    Route::post('/cancel-subscription', 'SubscriptionController@cancelSubscription');
    Route::post('/add-payment-method', 'PaymentMethodController@create');
    Route::post('/remove-payment-method', 'PaymentMethodController@delete');
    Route::post('/set-as-default-payment', 'PaymentMethodController@setAsDefault');
});
