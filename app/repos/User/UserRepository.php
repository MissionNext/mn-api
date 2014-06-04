<?php
namespace MissionNext\Repos\User;


use Illuminate\Support\Collection;
use MissionNext\Api\Exceptions\UserException;
use MissionNext\Api\Service\DataTransformers\UserCachedDataStrategy;
use MissionNext\Api\Service\DataTransformers\UserCachedTransformer;
use MissionNext\Models\Application\Application;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\User\User;
use MissionNext\Repos\Field\Field;

class UserRepository extends AbstractUserRepository implements UserRepositoryInterface
{
    protected $modelClassName = User::class;

    /**
     * @return User
     */
    public function getModel()
    {

        return $this->model;
    }

    /**
     * @param User $user
     *
     * @return $this
     */
    public function setModel(User $user)
    {
        $this->model = $user;

        return $this;
    }

    /**
     * @param Application $app
     *
     * @return bool
     */
    public function addApp(Application $app)
    {
        if (!$this->getModel()->hasApp($app)) {
            $this->getModel()->apps()->attach($app->id);

            return true;
        }

        return false;
    }

    /**
     * @param User $organization
     *
     * @return Collection
     */
    public function organizationJobsForUser(User $organization, User $user)
    {
        /** @var  $jobRepo JobRepository */
        $jobRepo = $this->repoContainer[JobRepositoryInterface::KEY];


        $builder =  $jobRepo->getModel()
                     ->select("job_cached_profile.data", "notes.notes", "folder_apps.folder")
                     ->leftJoin("job_cached_profile", "job_cached_profile.id", "=", "jobs.id")
                     ->leftJoin("folder_apps", function($join) use ($user){
                            $join->on("folder_apps.user_id", "=", "jobs.id")
                                ->where("folder_apps.for_user_id", "=", $user->id)
                                ->where("folder_apps.user_type", "=", BaseDataModel::JOB)
                                ->where("folder_apps.app_id", "=", $this->securityContext->getApp()->id());


                    })
                    ->leftJoin("notes", function($join) use ($user){
                              $join->on("notes.user_id", "=", "jobs.id")
                                    ->where("notes.for_user_id", "=", $user->id)
                                    ->where("notes.user_type", "=", BaseDataModel::JOB);
                    })
                     ->where("jobs.organization_id", "=", $organization->id)
                     ->where("jobs.app_id", "=", $this->securityContext->getApp()->id());

        return
            (new UserCachedTransformer($builder, new UserCachedDataStrategy()))->get();

    }

} 