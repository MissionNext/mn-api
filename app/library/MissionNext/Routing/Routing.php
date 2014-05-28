<?php

namespace MissionNext\Routing;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use MissionNext\Controllers\Api\Affiliate\AffiliateController;
use MissionNext\Controllers\Api\Folder\FolderAppsController;
use MissionNext\Controllers\Api\FolderNotes\FolderController;
use MissionNext\Controllers\Api\FolderNotes\NoteController;
use MissionNext\Controllers\Api\Inquire\InquireController;
use MissionNext\Controllers\Api\Matching\ConfigController;
use MissionNext\Controllers\Api\Notes\NotesController;
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
use MissionNext\Filter\RoleChecker;
use MissionNext\Models\Affiliate\Affiliate;
use MissionNext\Controllers\Api\Folder\FolderController as FolderResource;
use MissionNext\Controllers\Api\Favorite\Controller as FavoriteResource;
use MissionNext\Models\DataModel\BaseDataModel;

class Routing
{

    const API_PREFIX = 'api/v1';
    const RESOURCE_USER = 'user';
    const RESOURCE_PROFILE = 'profile';
    const RESOURCE_JOB_PROFILE = 'profile/job';
    const RESOURCE_JOB = 'job';
    const RESOURCE_FOLDER = 'folder';
    const RESOURCE_FAVORITE = 'favorite';
    const ROUTE_CREATE_USER = 'mission.next.user.create';
    const ROUTE_CREATE_JOB = 'mission.next.job.create';


    public function __construct()
    {

        if(starts_with($_SERVER['REQUEST_URI'], '/login') or starts_with($_SERVER['REQUEST_URI'], '/logout') or starts_with($_SERVER['REQUEST_URI'], '/dashboard')) {
            Config::set('session.driver','file');
        }

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
                [  'except' => ['index','create',  'edit'] ]
            );



//            Route::post('search/{searchType}', SearchController::class.'@search');
            Route::delete('search/{searchId}', SearchController::class.'@delete');
            Route::controller('search/{searchType}/for/{userType}/{id}', SearchController::class, [

            ]);
            //NOTES FOLDER CONTROLLER
            Route::controller('meta/notes', NotesController::class, []);
            Route::controller('meta/folder', FolderAppsController::class, []);

            Route::resource(static::RESOURCE_FOLDER, FolderResource::class,
                [   ]
            );
            Route::pattern('role', '('.BaseDataModel::AGENCY.'|'.BaseDataModel::CANDIDATE.'|'.BaseDataModel::ORGANIZATION.'|'.BaseDataModel::JOB.')');

            Route::group(array('prefix' => static::RESOURCE_FOLDER), function () {
                Route::get('by/{role}', FolderResource::class.'@role');
            });
            //END

            //FAVOURITE CONTROLLER
            Route::pattern('user_id', '\d+');
            Route::pattern('favorite_id', '\d+');
            Route::post(static::RESOURCE_FAVORITE, FavoriteResource::class.'@store');
            Route::group(array('prefix' => static::RESOURCE_FAVORITE), function(){
                Route::get('{user_id}/{role}', FavoriteResource::class.'@getByRole');
                Route::delete('{favorite_id}', FavoriteResource::class.'@delete');
            });
            //END
            Route::group(array('before' => RoleChecker::CHECK),function(){
                //INQUIRE CONTROLLER
                Route::get('inquire/candidates/for/organization/{organization}', InquireController::class.'@getCandidatesForOrganization');
                Route::get('inquire/candidates/for/agency/{agency}', InquireController::class.'@getCandidatesForAgency');
                Route::get('inquire/jobs/for/{candidate}', InquireController::class.'@getJobs');
                Route::controller('inquire/{candidate}/for/{job}', InquireController::class);
                //END
            });


        });

    }

}
