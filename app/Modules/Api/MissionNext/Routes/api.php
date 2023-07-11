<?php

use App\Modules\Api\MissionNext\Controllers\BaseController;
use Illuminate\Support\Facades\Route;
use App\Modules\Api\MissionNext\Controllers\Affiliate\AffiliateController;
use App\Modules\Api\MissionNext\Controllers\AppConfig\UserConfigController;
use App\Modules\Api\MissionNext\Controllers\Folder\FolderAppsController;
use App\Modules\Api\MissionNext\Controllers\GlobalConfig\GlobalConfigController;
use App\Modules\Api\MissionNext\Controllers\Inquire\InquireController;
use App\Modules\Api\MissionNext\Controllers\Meta\MetaController;
use App\Modules\Api\MissionNext\Controllers\Subscription\SubConfigController;
use App\Modules\Api\MissionNext\Controllers\Subscription\SubscriptionController;
use App\Modules\Api\MissionNext\Controllers\Translation\CustomTransController;
use App\Modules\Api\MissionNext\Controllers\Translation\FolderTransController;
use App\Modules\Api\MissionNext\Controllers\Translation\FormGroupTransController;
use App\Modules\Api\MissionNext\Controllers\Translation\LanguageController;
use App\Modules\Api\MissionNext\Controllers\Matching\ConfigController;
use App\Modules\Api\MissionNext\Controllers\Notes\NotesController;
use App\Modules\Api\MissionNext\Controllers\User\AdminController;
use App\Modules\Api\MissionNext\Controllers\User\OrganizationController;
use App\Modules\Api\MissionNext\Controllers\Profile\SearchController;
use App\Modules\Api\MissionNext\Controllers\User\UserController;
use App\Modules\Api\MissionNext\Controllers\Profile\UserController as UserProfileController;
use App\Modules\Api\MissionNext\Controllers\Field\Controller as FieldController;
use App\Modules\Api\MissionNext\Controllers\Form\Controller as FormController;
use App\Modules\Api\MissionNext\Controllers\User\JobController;
use App\Modules\Api\MissionNext\Controllers\Profile\JobController as JobProfileController;
use App\Modules\Api\MissionNext\Controllers\Matching\CandidateJobsController as MatchCandidateJobsController;
use App\Modules\Api\MissionNext\Controllers\Matching\CandidateOrganizationsController as MatchCandidateOrgsController;
use App\Modules\Api\MissionNext\Controllers\Matching\JobCandidatesController as MatchJobCandidatesController;
use App\Modules\Api\MissionNext\Controllers\Matching\OrganizationCandidatesController as MatchOrgCandidatesController;
use App\Modules\Api\Filter\RoleChecker;
use App\Models\Affiliate\Affiliate;
use App\Modules\Api\MissionNext\Controllers\Folder\FolderController as FolderResource;
use App\Modules\Api\MissionNext\Controllers\Favorite\Controller as FavoriteResource;
use App\Models\DataModel\BaseDataModel;
use App\Modules\Api\MissionNext\Controllers\Field\Controller as TransFieldController;
use App\Modules\Api\MissionNext\Controllers\AppConfig\ConfigController as AppConfigController;
use App\Modules\Api\MissionNext\Controllers\Subscription\CouponController;


//const API_PREFIX = 'api/v1';
//const RESOURCE_USER = 'user';
//const RESOURCE_PROFILE = 'profile';
//const RESOURCE_JOB_PROFILE = 'profile/job';
//const RESOURCE_JOB = 'job';
//const RESOURCE_FOLDER = 'folder';
//const RESOURCE_FAVORITE = 'favorite';
//const ROUTE_CREATE_USER = 'mission.next.user.create';
//const ROUTE_CREATE_JOB = 'mission.next.job.create';

Route::get('match/getOneResult/{forUserId}/{userId}', [BaseController::class, 'getOneMatch']);

Route::get('/test', [BaseController::class, 'testApi']);

Route::controller(User\AdminController::class)->prefix('administrator')->group(function () {
    Route::get('emails', 'getEmails');
});


Route::pattern('type', '[A-Za-z_-]+');
Route::pattern('form', '[A-Za-z_-]+');
Route::pattern('candidate_id', '\d+');

Route::controller(Field\Controller::class)->prefix('{type}/field')->group(function () {
    Route::get('/', 'getIndex')->name('model.fields.get');
    Route::put('/', 'putIndex')->name('model.fields.put');
    Route::post('/', 'postIndex')->name('model.fields.post');
    Route::delete('/', 'deleteIndex')->name('model.fields.delete');
    Route::get('/model', 'getModel')->name('model.fields.model.get');
    Route::put('/model', 'putIndex')->name('model.fields.model.put');
    Route::post('/model', 'postModel')->name('model.fields.model.post');
    Route::delete('/model', 'deleteIndex')->name('model.fields.model.delete');
});

Route::controller(Matching\ConfigController::class)->prefix('{type}/matching/config')->group(function () {
    Route::get('/', 'getIndex');
    Route::put('/', 'putIndex');
});

//Route::controller('{type}/matching/config', ConfigController::class, [
//
//]);

Route::resource('profile/job', \Profile\JobController::class,
    [
        'only' => ['show', 'update', 'destroy']
    ]
);

//Route::controller('{type}/{form}/form', FormController::class);
Route::controller(Form\Controller::class)->prefix('{type}/{form}/form')->group(function () {
    Route::get('/', 'getIndex');
    Route::put('/', 'putIndex');
});

//Route::controller('match/candidate/jobs/{candidate_id}', MatchCandidateJobsController::class);
Route::controller(Matching\CandidateJobsController::class)->prefix('match/candidate/jobs/{candidate_id}')->group(function () {
    Route::get('/', 'getIndex');
    Route::get('/live', 'getLive');
});
//Route::controller('match/candidate/organizations/{candidate_id}', MatchCandidateOrgsController::class);
Route::controller(Matching\CandidateOrganizationsController::class)->prefix('match/candidate/organizations/{candidate_id}')->group(function () {
    Route::get('/', 'getIndex');
    Route::get('/live', 'getLive');
});
//Route::controller('match/job/candidates/{jobId}', MatchJobCandidatesController::class);
Route::controller(Matching\JobCandidatesController::class)->prefix('match/job/candidates/{jobId}')->group(function () {
    Route::get('/', 'getIndex');
    Route::get('/live', 'getLive');
});
//Route::controller('match/organization/candidates/{organizationId}', MatchOrgCandidatesController::class);
Route::controller(Matching\OrganizationCandidatesController::class)->prefix('match/organization/candidates/{organizationId}')->group(function () {
    Route::get('/', 'getIndex');
    Route::get('/live', 'getLive');
});

Route::pattern('requester_id', '\d+');
Route::pattern('approver_id', '\d+');
Route::pattern('affiliate_id', '\d+');
Route::pattern('affiliate_type', '(approver|requester|any)');

//Route::controller('affiliate/{requester_id}/to/{approver_id}', AffiliateController::class);
Route::controller(\Affiliate\AffiliateController::class)->prefix('affiliate/{requester_id}/to/{approver_id}')->group(function () {
    Route::get('/', 'getIndex');
    Route::post('/', 'postIndex');
    Route::get('/affiliate', 'getAffiliates');
    Route::get('/agency/jobs', 'getAgencyJobs');
    Route::post('/approve', 'postApprove');
    Route::post('/cancel', 'postCancel');
    Route::post('/pend', 'postPend');
    Route::get('/affiliate/check', 'affiliateCheck');
    Route::get('/requester', 'getRequester');
    Route::get('/approver', 'getApprover');
    Route::get('/role', 'getRole');
});
Route::get('affiliate/{affiliate_id}/as/{affiliate_type}', 'Affiliate\AffiliateController@getAffiliates');
Route::get('affiliate/{affiliate_id}/jobs', 'Affiliate\AffiliateController@getAgencyJobs');

Route::post('user/deactivate/{id}', 'User\UserController@deactivateUser');

Route::resource('user', User\UserController::class, [
    'names' => ['store' => 'mission.next.user.create']
]);

//Route::resource('job', User\JobController::class, [
//    'names' => [
//        'store' => 'mission.next.job.create'
//    ],
//    'except' => ['create', 'edit', 'destroy']
//]);

Route::group(array('prefix' => 'job'), function () {
    Route::post('find', 'User\JobController@find');
    Route::post('find/{organization_id}', 'User\JobController@findByOrgId');
    Route::delete('{id}/{organization_id}', 'User\JobController@delete');
});

Route::controller(User\UserController::class)->prefix('user')->group(function () {
    Route::get('/', 'index');
    Route::post('/', 'store');
    Route::get('/show', 'show');
    Route::post('/update', 'update');
});


Route::delete('completness/profile/{role}', 'Profile\UserController@deleteCompletedProfilesChecks');
Route::get('check/profile/{user_id}', 'Profile\UserController@checkCompletedProfile');

Route::resource('profile', Profile\UserController::class,
    [
        'except' => [
            'index', 'create', 'edit', 'store'
        ]
    ]
);

//            Route::post('search/{searchType}', Profile\SearchController::class.'@search');
Route::delete('search/{searchId}/{forUserId}', 'Profile\SearchController@delete');

//Route::controller('search/{searchType}/for/{userType}/{id}', SearchController::class, []);
Route::controller(Profile\SearchController::class)->prefix('search/{searchType}/for/{userType}/{id}')->group(function () {
    Route::get('/', 'getIndex');
    Route::put('/', 'putIndex');
    Route::post('/', 'postIndex');
    Route::delete('/', 'delete');
});
//NOTES FOLDER CONTROLLER
//Route::controller('meta/notes', NotesController::class, []);
Route::controller(Notes\NotesController::class)->prefix('meta/notes')->group(function () {
    Route::post('/', 'postIndex');
});
//Route::controller('meta/folder', FolderAppsController::class, []);
Route::controller(Folder\FolderAppsController::class)->prefix('meta/folder')->group(function () {
    Route::post('/', 'postIndex');
});

Route::get('meta/for/{user_id}/{role}', 'Meta\MetaController@getMetaForAgency');
Route::resource('folder', Folder\FolderController::class,
    []
);
Route::pattern('role', '(' . BaseDataModel::AGENCY . '|' . BaseDataModel::CANDIDATE . '|' . BaseDataModel::ORGANIZATION . '|' . BaseDataModel::JOB . ')');

Route::group(array('prefix' => 'folder'), function () {
    Route::get('by/{role}/{userId}', 'Folder\FolderController@roleWithUser');
    Route::get('by/{role}', 'Folder\FolderController@role');
});
//END

//FAVOURITE CONTROLLER
Route::pattern('user_id', '\d+');
Route::pattern('favorite_id', '\d+');
Route::post('favorite', 'Favorite\Controller@store');
Route::group(array('prefix' => 'favorite'), function () {
    Route::get('{user_id}/{role}', 'Favorite\Controller@getByRole');
    Route::delete('{favorite_id}', 'Favorite\Controller@delete');
});
//END
Route::group(array('before' => RoleChecker::CHECK), function () {
    //INQUIRE CONTROLLER
    Route::get('inquire/candidates/for/organization/{organization}', 'Inquire\InquireController@getCandidatesForOrganization');
    Route::get('inquire/candidates/for/agency/{agency}', 'Inquire\InquireController@getCandidatesForAgency');
    Route::get('inquire/jobs/for/{candidate}', 'Inquire\InquireController@getJobs');
    Route::post('inquire/cancel/{inquire_id}/by/agency/{agency}', 'Inquire\InquireController@postCancelInquireByAgency');
    Route::post('inquire/cancel/{inquire_id}/by/organization/{organization}', 'Inquire\InquireController@postCancelInquireByOrganization');
//    Route::controller('inquire/{candidate}/for/{job}', InquireController::class);
    Route::controller(Inquire\InquireController::class)->prefix('inquire/{candidate}/for/{job}')->group(function () {
        Route::post('/', 'postIndex');
        Route::post('/cancel', 'postCancel');
        Route::post('/cancel/inquire/by/agency', 'postCancelInquireByAgency');
        Route::post('/cancel/inquire/by/organization', 'postCancelInquireByOrganization');
        Route::get('/jobs', 'getJobs');
        Route::get('/candidates/for/organization', 'getCandidatesForOrganization');
        Route::get('/candidates/for/agency', 'getCandidatesForAgency');
    });
    //END
    Route::get('organization/select/names', 'User\OrganizationController@getOrganizationNames');
//    Route::controller('organization/jobs/{organization}/for/{userId}', OrganizationController::class);
    Route::controller(User\OrganizationController::class)->prefix('organization/jobs/{organization}/for/{userId}')->group(function () {
        Route::get('/', 'getIndex');
        Route::get('/organization/names', 'getOrganizationNames');
    });
});


//Route::controller( Translation\LanguageController::class, []);
Route::controller(Translation\LanguageController::class)->prefix('language')->group(function () {
    Route::get('/', 'getIndex');
    Route::post('/application', 'postApplication');
    Route::get('/application', 'getApplication');
});


//Route::controller('custom/trans', CustomTransController::class);
Route::controller(Translation\CustomTransController::class)->prefix('custom/trans')->group(function () {
    Route::get('/', 'getIndex');
    Route::post('/', 'postIndex');
});


Route::get('form/group/trans/{type}/{formName}', Translation\FormGroupTransController::class . '@getGroupTrans');

Route::controller(Translation\FormGroupTransController::class)->prefix('form/group/trans')->group(function () {
    Route::post('/', 'postIndex');
});

Route::get('folder/trans/{type}', Translation\FolderTransController::class . '@getFolderTrans');

Route::controller('folder/trans', FolderTransController::class);

Route::controller('trans/{type}/field', TransFieldController::class, []);


//Route::get('folder/trans/{type}', [Translation\FolderTransController::class, 'getFolderTrans']);

//Route::controller('folder/trans', FolderTransController::class);
Route::controller(Translation\FolderTransController::class)->prefix('folder/trans')->group(function () {
    Route::post('/', 'postIndex');
    Route::get('/folder/trans', 'getFolderTrans');
});



Route::controller(Field\Controller::class)->prefix('trans/{type}/field')->group(function () {
    Route::get('/', 'getIndex');
    Route::post('/', 'postIndex');
    Route::put('/', 'putIndex');
    Route::delete('/', 'deleteIndex');
    Route::get('/model', 'getModel');
    Route::post('/choices', 'postChoices');
    Route::post('/model', 'postModel');
});
//APP CONFIGS CONTROLLER
//Route::controller('configs', AppConfigController::class, []);
Route::controller(AppConfig\ConfigController::class)->prefix('configs')->group(function () {
    Route::post('/', 'postIndex');
    Route::get('/', 'getIndex');
    Route::get('/key', 'getKey');
});
//Route::controller('uconfigs', UserConfigController::class, []);
Route::controller(AppConfig\UserConfigController::class)->prefix('uconfigs')->group(function () {
    Route::get('/', 'getIndex');
    Route::post('/', 'postIndex');
    Route::get('/current', 'getCurrent');
    Route::get('/key', 'getKey');
});
//Route::controller('gconfigs', GlobalConfigController::class, []);
Route::controller(GlobalConfig\GlobalConfigController::class)->prefix('gconfigs')->group(function () {
    Route::get('/', 'getIndex');
});
//Route::controller('coupon', CouponController::class, []);
Route::controller(Subscription\CouponController::class)->prefix('coupon')->group(function () {
    Route::post('/', 'postCode');
});
Route::group(array('prefix' => 'subscription'), function () {
    Route::controller( Subscription\SubConfigController::class)->prefix('configs')->group(function () {
        Route::get('/', 'getIndex');
    });
    Route::controller( Subscription\SubscriptionController::class)->prefix('manager')->group(function () {
        //Route::post('/', 'postIndex');
    });
    Route::get('for/{userId}', 'Subscription\SubscriptionController@getFor');
});
