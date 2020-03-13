<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
 */

use App\Mail\Hello;

Route::get('testemail', function () {
    if (\Mail::to(['jammoin8282@gmail.com'])->send(new Hello))
        echo 'sent';
    else
        echo 'not sent';
});

Route::get('/', 'WelcomeController@show');

Route::get('/home', function () {
    return redirect('/projects');
});

Route::group(['middleware' => ['auth']], function () {
    Route::get('/projects/{path?}', function () {
        return view('spa-modules.spa-projects.spa-projects');
    })->where('path', '^(?!api).*$');

    Route::get('/proofer/{path?}', function () {
        return view('spa-modules.spa-projects.spa-projects');
    })->where('path', '^(?!api).*$');

    Route::get('/loadProofer/{project_hash}/{revision_id}', 'ProjectController@loadProofer');
    Route::get('/loadProofer/{project_hash}/{revision_id}/{proof_id}', 'ProjectController@loadProofer3');
    Route::get('/loadProofer/{project_hash}/{revision_id}/{proof_id}/{issue_id}', 'ProjectController@loadProofer2');
    /* Route::get('/load_editor_freelancer/{project_id}/{revision_id}', 'ProjectController@loadEditorFreelancer'); */
});

/* Route::get('/load_editor_client/{project_hash}/{revision_id}', 'ProjectController@loadEditorClient');
Route::get('/load_editor_freelancer/{project_id}/{revision_id}', 'ProjectController@loadEditorFreelancer'); */

/* Route::get('/proofer_guest/{path?}', function () {
    return view('spa-modules.spa-projects.spa-projects');
})->where('path', '^(?!api).*$');

Route::get('/proofer_freelancer/{path?}', function () {
    return view('spa-modules.spa-projects.spa-projects');
})->where('path', '^(?!api).*$');
 */

//Email notifications settings
Route::put('/settings/email-notifications',  'EmailNotificationSettingsController@update');

//Custom Notifications
Route::get('/notifications/recent', 'NotificationController@recent');

//CUSTOM AUTH ROUTES

// Overwrite Spark’s Register function to add User Verification…
Route::post('/register', 'Auth\RegisterController@register');

// Overwrite Spark’s login function to add User Verification…
Route::post('/login', 'Auth\LoginController@login');

// To stop un-verified users getting a reset email…
Route::post('/password/email', 'Auth\PasswordController@sendResetLinkEmail');

//Overwrite Spark’s Password reset function
Route::post('password/reset', 'Auth\PasswordController@reset');

//Verify user profile
Route::get('verify-user/{id}/{code}', 'UserController@verifyUser');

//Overwrite Spark’s Delete Team Member function
$pluralTeamString = str_plural(Spark::teamString());
Route::delete('/settings/'.$pluralTeamString.'/{team}/members/{team_member}', 'TeamController@deleteMember');

//Plans management
Route::get('plans/list', 'PlanController@list');
Route::group(['middleware' => 'dev', 'prefix' => 'plans'], function () {
    Route::post('store', 'PlanController@store');
    Route::put('update/{planId}', 'PlanController@update');
    Route::delete('delete/{planId}', 'PlanController@delete');
});

// Settings Dashboard...
Route::get('/settings/{tab}', 'DashboardController@showByTab');

//Stripe Webhook Handler
Route::post('/stripe/webhook', 'StripeWebhookController@handleWebhook')->middleware('verifyStripeSignature');