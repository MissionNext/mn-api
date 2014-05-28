<?php

namespace MissionNext\Repos\Inquire;


use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use MissionNext\Api\Auth\ISecurityContextAware;
use MissionNext\Api\Auth\SecurityContext;
use MissionNext\Api\Service\DataTransformers\UserCachedDataStrategy;
use MissionNext\Api\Service\DataTransformers\UserCachedTransformer;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Inquire\Inquire;
use MissionNext\Models\Job\Job;
use MissionNext\Models\User\User;
use MissionNext\Repos\AbstractRepository;
use MissionNext\Repos\Affiliate\AffiliateRepository;
use MissionNext\Repos\Affiliate\AffiliateRepositoryInterface;
use MissionNext\Repos\CachedData\UserCachedRepositoryInterface;
use MissionNext\Repos\User\JobRepository;
use MissionNext\Repos\User\JobRepositoryInterface;
use MissionNext\Repos\User\UserRepository;
use MissionNext\Repos\User\UserRepositoryInterface;

class InquireRepository extends AbstractRepository implements ISecurityContextAware, InquireRepositoryInterface
{
    protected $modelClassName = Inquire::class;
    /** @var  SecurityContext */
    public  $securityContext;

    /**
     * @return Inquire
     */
    public function getModel()
    {

        return $this->model;
    }

    /**
     * @param SecurityContext $securityContext
     *
     * @return $this
     */
    public function setSecurityContext(SecurityContext $securityContext)
    {
        $this->securityContext = $securityContext;

        return $this;
    }

    /**
     * @param User $user
     * @param Job $job
     * @param $status
     * @return Builder
     */
    public function checkInquire(User $user, Job $job, $status = null)
    {
        $query =  $this->where("app_id", "=", $this->repoContainer->securityContext()->getApp()->id())
            ->where("candidate_id", "=", $user->id)
            ->where("job_id", "=", $job->id);

        return $status ?  $query->where("status", "=", $status) : $query;
    }

    /**
     * @param User $user
     * @param Job $job
     *
     * @return Inquire
     */
    public function inquire(User $user, Job $job)
    {

         return  $this->checkInquire($user, $job, Inquire::STATUS_INQUIRED)->first() ? :  $this->create([
             "app_id" => $this->repoContainer->securityContext()->getApp()->id(),
             "candidate_id" => $user->id,
             "job_id" => $job->id,
             "status" => Inquire::STATUS_INQUIRED,
         ]);
    }

    /**
     * @param User $user
     * @param Job $job
     *
     * @return Inquire
     */
    public function cancel(User $user, Job $job)
    {

        return  $this->checkInquire($user, $job)->delete();
    }

    /**
     * @param User $user
     * @return Collection
     */
    public function jobs(User $user)
    {
       /** @var  $jobRepo JobRepository */
       $jobRepo = $this->repoContainer[JobRepositoryInterface::KEY];

       return $jobRepo->getModel()
                ->leftJoin("inquires", "inquires.job_id",'=', 'jobs.id')
                ->with("organization")
                ->where("inquires.candidate_id","=", $user->id)
                ->where("jobs.app_id", "=", $this->repoContainer->securityContext()->getApp()->id())
                ->where("inquires.app_id","=", $this->repoContainer->securityContext()->getApp()->id())
                ->select("jobs.id", "jobs.name", "jobs.symbol_key", "jobs.organization_id", "jobs.app_id", "inquires.status")
                ->get();
    }

    /**
     * @param array $jobIds
     *
     * @return Collection
     */
    private function candidateByJobs(array $jobIds)
    {
        $builder =   $this->getModel()
            ->leftJoin("candidate_cached_profile", "candidate_cached_profile.id", "=", "inquires.candidate_id")
            ->whereIn("job_id", $jobIds)
            ->where("app_id", "=", $this->repoContainer->securityContext()->getApp()->id() )
            ->select(DB::raw("distinct on (candidate_cached_profile.id) candidate_cached_profile.id, candidate_cached_profile.data ") );

        return
            (new UserCachedTransformer($builder, new UserCachedDataStrategy()))->get();

    }

    /**
     * @param User $user
     *
     * @return Collection|array
     */
    public function candidatesForOrganization(User $user)
    {
        /** @var  $jobRepo JobRepository */
        $jobRepo = $this->repoContainer[JobRepositoryInterface::KEY];


        $jobIds =  $jobRepo->getModel()
                    ->where("organization_id", "=", $user->id)
                    ->where("app_id", "=", $this->repoContainer->securityContext()->getApp()->id())
                    ->get()
                    ->lists("id");

        return  $jobIds ?
                  $this->candidateByJobs($jobIds)
               : [];
    }

    /**
     * @param User $user
     *
     * @return Collection|array
     */
    public function candidatesForAgency(User $user)
    {
        /** @var  $affilRepo AffiliateRepository */
        $affilRepo = $this->repoContainer[AffiliateRepositoryInterface::KEY];

        $orgIdsR = $affilRepo->where("affiliate_approver","=", $user->id)
                             ->where("app_id", "=", $this->repoContainer->securityContext()->getApp()->id())
                             ->get()
                             ->lists("affiliate_requester");

        $orgIdsA = $affilRepo->where("affiliate_requester","=", $user->id)
                             ->where("app_id", "=", $this->repoContainer->securityContext()->getApp()->id())
                             ->get()
                             ->lists("affiliate_approver");

        $orgIds = array_merge($orgIdsA, $orgIdsR);

        /** @var  $jobRepo JobRepository */
        $jobRepo = $this->repoContainer[JobRepositoryInterface::KEY];

        $jobIds = $orgIds ?  $jobRepo->getModel()
            ->whereIn("organization_id",  $orgIds)
            ->where("app_id", "=", $this->repoContainer->securityContext()->getApp()->id())
            ->get()
            ->lists("id") : [];

        return  $jobIds ?
             $this->candidateByJobs($jobIds)
            : [];
    }

}