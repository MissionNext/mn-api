<?php
namespace MissionNext\Controllers\Api;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Queue\BeanstalkdQueue;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;
use MissionNext\Api\Auth\Token;
use Illuminate\Support\Facades\DB;
use MissionNext\Api\Exceptions\ProfileException;
use MissionNext\Api\Exceptions\ValidationException;
use MissionNext\Api\Service\Matching\Queue\MasterMatching;
use MissionNext\Facade\SecurityContext as FSecurityContext;
use MissionNext\Api\Auth\SecurityContext;
use MissionNext\Filter\RouteSecurityFilter;
use MissionNext\Models\Application\Application as AppModel;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Field\FieldType;
use MissionNext\Models\Job\Job;
use MissionNext\Models\Profile;
use MissionNext\Models\ProfileInterface;
use MissionNext\Models\User\User as UserModel;
use MissionNext\Models\User\User;
use MissionNext\Repos\Field\FieldRepository;
use MissionNext\Repos\Field\FieldRepositoryInterface;
use MissionNext\Repos\Form\FormRepository;
use MissionNext\Repos\Form\FormRepositoryInterface;
use MissionNext\Repos\FormGroup\FormGroupRepository;
use MissionNext\Repos\FormGroup\FormGroupRepositoryInterface;
use MissionNext\Repos\Matching\ConfigRepository;
use MissionNext\Repos\Matching\ConfigRepositoryInterface;
use MissionNext\Repos\Matching\ResultsRepository;
use MissionNext\Repos\Matching\ResultsRepositoryInterface;
use MissionNext\Repos\User\JobRepository;
use MissionNext\Repos\User\JobRepositoryInterface;
use MissionNext\Repos\User\UserRepository;
use MissionNext\Repos\User\UserRepositoryInterface;
use MissionNext\Repos\ViewField\ViewFieldRepository;
use MissionNext\Repos\ViewField\ViewFieldRepositoryInterface;
use MissionNext\Validators\ValidatorResolver;
use MissionNext\Api\Service\Matching\Queue\CandidateJobs as CanJobsQueue;
use MissionNext\Api\Service\Matching\Queue\CandidateOrganizations as CanOrgsQueue;
use MissionNext\Api\Service\Matching\Queue\OrganizationCandidates as OrgCandidatesQueue;
use MissionNext\Api\Service\Matching\Queue\JobCandidates as JobCandidatesQueue;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Illuminate\Queue\Queue as AQueue;


class BaseController extends Controller
{
    /** @var \MissionNext\Repos\Field\FieldRepository */
    private $fieldRepo;
    /** @var \MissionNext\Repos\User\UserRepositoryInterface  */
    private $userRepo;
    /** @var \MissionNext\Repos\ViewField\ViewFieldRepositoryInterface  */
    private $viewFieldRepo;
    /** @var \MissionNext\Repos\Form\FormRepositoryInterface  */
    private $formRepo;
    /** @var \MissionNext\Repos\FormGroup\FormGroupRepositoryInterface  */
    private $formGroupRepo;
    /** @var \MissionNext\Repos\User\JobRepositoryInterface  */
    private $jobRepo;
    /** @var \MissionNext\Repos\Matching\ConfigRepositoryInterface  */
    private $matchingConfigRepo;

    /** @var  ResultsRepositoryInterface */
    private $matchResultsRepo;
    /** @var  \Illuminate\Queue\Queue */
    protected $queue;
    /** @var  \Pheanstalk_Pheanstalk */
    protected $beanstalk;
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Set filters
     */
    public function __construct(ValidatorResolver $valResolver,
                                FieldRepositoryInterface $fieldRepo,
                                UserRepositoryInterface $userRepo,
                                ViewFieldRepositoryInterface $viewFieldRepo,
                                FormRepositoryInterface $formRepo,
                                FormGroupRepositoryInterface $formGroupRepo,
                                JobRepositoryInterface $jobRepo,
                                ConfigRepositoryInterface $matchingConfigRepo,
                                ResultsRepositoryInterface $matchResultsRepo,
                                Request $request
    )

    {
        $this->beanstalk = Queue::getPheanstalk();
        $this->request = $request;
        $this->fieldRepo = $fieldRepo;
        $this->userRepo = $userRepo;
        $this->viewFieldRepo = $viewFieldRepo;
        $this->formRepo = $formRepo;
        $this->formGroupRepo = $formGroupRepo;
        $this->jobRepo = $jobRepo;
        $this->matchingConfigRepo = $matchingConfigRepo;
        $this->matchResultsRepo = $matchResultsRepo;

        $this->beforeFilter(RouteSecurityFilter::AUTHORIZE);
        $this->beforeFilter(RouteSecurityFilter::ROLE);
    }

    /**
     * @return FieldRepository
     */
    protected function fieldRepo()
    {

        return $this->fieldRepo;
    }

    /**
     * @return ConfigRepository
     */
    protected function matchingConfigRepo()
    {

        return $this->matchingConfigRepo;
    }

    /**
     * @return UserRepository
     */
    protected function userRepo()
    {

        return  $this->userRepo;
    }
    /**
     * @return JobRepository
     */
    protected function jobRepo()
    {

        return  $this->jobRepo;
    }

    /**
     * @return ResultsRepository
     */
    protected function matchingResultsRepo()
    {

        return  $this->matchResultsRepo;
    }

    /** @return ViewFieldRepository */
    protected function viewFieldRepo()
    {

        return $this->viewFieldRepo;
    }

    /** @return FormRepository */
    protected function formRepo()
    {

        return $this->formRepo;
    }

    /** @return FormGroupRepository */
    protected function formGroupRepo()
    {

        return $this->formGroupRepo;
    }

    /**
     * @return SecurityContext
     */
    protected function securityContext()
    {

        return FSecurityContext::getInstance();
    }

    /**
     * @return Token
     */
    protected function getToken()
    {

        return $this->securityContext()->getToken();
    }

    /**
     * @return AppModel
     */
    protected function getApp()
    {

        return $this->getToken()->getApp();
    }

    /**
     * @return  []
     */
    protected function getLogQueries()
    {

        return DB::getQueryLog();
    }

    /**
     * @param array $profileData
     *
     * @return Collection
     * @throws \MissionNext\Api\Exceptions\ValidationException
     * @throws \MissionNext\Api\Exceptions\ProfileException
     */
    protected function validateProfileData(array $profileData)
    {
        $fieldNames = array_keys($profileData);
        $dependentFields = $this->formGroupRepo()->dependentFields()->get();

     // dd($dependentFields->toArray());
        foreach($dependentFields as $field){
            $ownerField = $field->depends_on;
            if (isset($profileData[$ownerField])){
                $ownerFieldValue = $profileData[$ownerField];
                if (!$ownerFieldValue) {
                    $fieldNames = array_diff($fieldNames, $field->symbol_keys);
                }
            }
        }
       // dd($fieldNames);
        /** @var  $fields Collection */
        $fields = $this->fieldRepo()->modelFields()->whereIn('symbol_key', $fieldNames)->get();

        if ($fields->count() !== count($fieldNames)) {

            throw new ProfileException("Wrong field name(s)", ProfileException::ON_UPDATE);
        }

        $constraints = [];
        $validationData = [];

        foreach ($fields as $field) {
            if (isset($profileData[$field->symbol_key])) {
                $validationData[$field->symbol_key] = $profileData[$field->symbol_key];
                $constraints[$field->symbol_key] = $field->pivot->constraints ? : "";
            }
        }

       // dd($validationData, $constraints);
        /** @var  $validator \Illuminate\Validation\Validator */
        $validator = Validator::make(
            $validationData,
            $constraints
        );

        if ($validator->fails()) {

            throw new ValidationException($validator->messages());
        }
        return $fields;
    }

    /**
     * @param ProfileInterface $user
     * @param array $profileData
     *
     * @return ProfileInterface
     */
    protected function updateUserProfile(ProfileInterface $user, array $profileData = null)
    {
        /** @var $user User|Job */
        if (empty($profileData)) {

            $user->touch();

            return $user;
        }

        $fields = $this->validateProfileData($profileData);

        $user->touch();


        $mapping = [];
        $sKeys = [];

        foreach ($fields as $field) {
            if (isset($profileData[$field->symbol_key])) {
                $mapping[$field->id] = ["value" => $profileData[$field->symbol_key] ];
                $sKeys[$field->id] = $field->symbol_key;
            }//@TODO if example favourite_movies[] = '', no errors;
        }

        foreach ($mapping as $key => $map) {
            $this->fieldRepo()->profileFields($user)->detach($key, true);
            if (is_array($map['value'])) {
                foreach ($map['value'] as $val) {
                    $this->fieldRepo()->profileFields($user)->attach($key, ["value" => $val]);
                }
            } elseif( $map['value'] instanceof UploadedFile) {
                /** @var  $file UploadedFile  */
                $file = $map['value'];
                $fileName = $this->securityContext()->role().$user->id."_".$sKeys[$key].".".$file->getClientOriginalExtension();
                $file->move(public_path()."/uploads", $fileName );
                $this->fieldRepo()->profileFields($user)->attach($key, ["value" => $fileName]);
            } else{
                $this->fieldRepo()->profileFields($user)->attach($key, $map);

            }
        }
        if (!empty($mapping)) {
             //$user->touch();

            $this->userRepo()->updateUserCachedData($user);
            $queueData = ["userId"=>$user->id, "appId"=>$this->getApp()->id, "role" => $this->securityContext()->role()];
            MasterMatching::run($queueData);
        }

        return $user;
    }

} 