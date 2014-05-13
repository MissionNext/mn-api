<?php

namespace MissionNext\Routing;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use MissionNext\Controllers\Api\Affiliate\AffiliateController;
use MissionNext\Controllers\Api\FolderNotes\FolderController;
use MissionNext\Controllers\Api\FolderNotes\NoteController;
use MissionNext\Controllers\Api\Matching\ConfigController;
use MissionNext\Controllers\Api\Profile\SearchController;
use MissionNext\Controllers\Api\User\UserController;
use MissionNext\Controllers\Api\Profile\UserController as UserProfileController;
use MissionNext\Controllers\Api\Field\Controller as FieldController;
use MissionNext\Controllers\Api\Form\Controller as FormController;
use MissionNext\Controllers\Api\JobController;
use MissionNext\Controllers\Api\Profile\JobController as JobProfileController;
use MissionNext\Controllers\Api\Matching\CandidateJobsController as MatchCandidateJobsController;
use MissionNext\Controllers\Api\Matching\CandidateOrganizationsController as MatchCandidateOrgsController;
use MissionNext\Controllers\Api\Matching\JobCandidatesController as MatchJobCandidatesController;
use MissionNext\Controllers\Api\Matching\OrganizationCandidatesController as MatchOrgCandidatesController;
use MissionNext\Models\Affiliate\Affiliate;

class Routing
{

    const API_PREFIX = 'api/v1';
    const RESOURCE_USER = 'user';
    const RESOURCE_PROFILE = 'profile';
    const RESOURCE_JOB_PROFILE = 'profile/job';
    const RESOURCE_JOB = 'job';
    const ROUTE_CREATE_USER = 'mission.next.user.create';
    const ROUTE_CREATE_JOB = 'mission.next.job.create';


    public function __construct()
    {

        Route::get('/', function () {

            return View::make('hello');
        });

        Route::group(array('prefix' => static::API_PREFIX), function () {

            Route::pattern('type', '[A-Za-z_-]+');
            Route::pattern('form', '[A-Za-z_-]+');
            Route::pattern('candidate_id', '\d+');

            Route::controller('{type}/field', FieldController::class, [
                'getModel' => 'model.fields.get'
            ]);
            Route::controller('{type}/matching/config', ConfigController::class, [

            ]);

            Route::resource(static::RESOURCE_JOB_PROFILE, JobProfileController::class,
              [  'only' => ['show','update', 'destroy'] ]
            );

            Route::controller('{type}/{form}/form', FormController::class);

            Route::controller('match/candidate/jobs/{candidate_id}', MatchCandidateJobsController::class);
            Route::controller('match/candidate/organizations/{candidate_id}', MatchCandidateOrgsController::class);
            Route::controller('match/job/candidates/{jobId}', MatchJobCandidatesController::class);
            Route::controller('match/organization/candidates/{organizationId}', MatchOrgCandidatesController::class);

            Route::pattern('requester_id', '\d+');
            Route::pattern('approver_id', '\d+');
            Route::pattern('affiliate_id', '\d+');
            Route::pattern('affiliate_type', '('.Affiliate::TYPE_APPROVER.'|'.Affiliate::TYPE_REQUESTER.'|'.Affiliate::TYPE_ANY.')');
            Route::controller('affiliate/{requester_id}/to/{approver_id}', AffiliateController::class);
            Route::get('affiliate/{affiliate_id}/as/{affiliate_type}', AffiliateController::class.'@getAffiliates');
            Route::get('affiliate/{affiliate_id}/jobs', AffiliateController::class.'@getAgencyJobs');

            Route::resource(static::RESOURCE_USER, UserController::class, [
                'names' => ['store' => static::ROUTE_CREATE_USER]
            ]);

            Route::resource(static::RESOURCE_JOB, JobController::class, [
                'names' => ['store' => static::ROUTE_CREATE_JOB], 'except' => ['create','edit', 'destroy']
            ]);

            Route::group(array('prefix' => static::RESOURCE_JOB), function () {
                Route::post('find', JobController::class.'@find');
                Route::delete('{id}/{organization_id}', JobController::class.'@delete');
            });

            Route::group(array('prefix' => static::RESOURCE_USER), function () {
                Route::post('find', UserController::class.'@find');
                Route::post('check', UserController::class.'@check');
            });



            Route::resource(static::RESOURCE_PROFILE, UserProfileController::class,
                [  'except' => ['index','create', 'store', 'edit'] ]
            );

//            Route::post('search/{searchType}', SearchController::class.'@search');
            Route::delete('search/{searchId}', SearchController::class.'@delete');
            Route::controller('search/{searchType}/for/{userType}/{id}', SearchController::class, [

            ]);

            Route::controller('results/folder', FolderController::class, []);
            Route::controller('results/notes', NoteController::class, []);

        });

    }

} 