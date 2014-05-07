<?php
namespace MissionNext\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;
use MissionNext\Api\Auth\Token;
use Illuminate\Support\Facades\DB;
use MissionNext\Api\Exceptions\ProfileException;
use MissionNext\Api\Exceptions\ValidationException;
use MissionNext\Facade\SecurityContext as FSecurityContext;
use MissionNext\Api\Auth\SecurityContext;
use MissionNext\Filter\RouteSecurityFilter;
use MissionNext\Models\Application\Application as AppModel;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Field\FieldType;
use MissionNext\Models\Profile;
use MissionNext\Models\ProfileInterface;
use MissionNext\Models\User\User as UserModel;
use MissionNext\Repos\Field\FieldRepository;
use MissionNext\Repos\Field\FieldRepositoryInterface;
use MissionNext\Repos\Form\FormRepository;
use MissionNext\Repos\Form\FormRepositoryInterface;
use MissionNext\Repos\FormGroup\FormGroupRepository;
use MissionNext\Repos\FormGroup\FormGroupRepositoryInterface;
use MissionNext\Repos\Matching\ConfigRepository;
use MissionNext\Repos\Matching\ConfigRepositoryInterface;
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
                                Request $request
    )

    {
        $this->request = $request;
        $this->fieldRepo = $fieldRepo;
        $this->userRepo = $userRepo;
        $this->viewFieldRepo = $viewFieldRepo;
        $this->formRepo = $formRepo;
        $this->formGroupRepo = $formGroupRepo;
        $this->jobRepo = $jobRepo;
        $this->matchingConfigRepo = $matchingConfigRepo;

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
        if (empty($profileData)) {

            $user->save();

            return $user;
        }

        $fields = $this->validateProfileData($profileData);

        $user->save();

        $mapping = [];

        foreach ($fields as $field) {
            if (isset($profileData[$field->symbol_key])) {
                $mapping[$field->id] = ["value" => $profileData[$field->symbol_key]];
            }//@TODO if example favourite_movies[] = '', no errors;
        }

        foreach ($mapping as $key => $map) {
            $this->fieldRepo()->profileFields($user)->detach($key, $map);
            if (is_array($map['value'])) {
                foreach ($map['value'] as $val) {
                    $this->fieldRepo()->profileFields($user)->attach($key, ["value" => $val]);
                }
            } else {
                $this->fieldRepo()->profileFields($user)->attach($key, $map);
            }
        }
        if (!empty($mapping)) {
            $user->touch();

            $this->userRepo()->updateUserCachedData($user);

            switch($this->getToken()->getRoles()[0]){

                case BaseDataModel::CANDIDATE:
                     Queue::push(CanJobsQueue::class, ["userId"=>$user->id, "appId"=>$this->getApp()->id]);
                     Queue::push(CanOrgsQueue::class, ["userId"=>$user->id, "appId"=>$this->getApp()->id]);
                    break;
                case BaseDataModel::ORGANIZATION:
                     Queue::push(OrgCandidatesQueue::class, ["userId"=>$user->id, "appId"=>$this->getApp()->id]);
                    break;
            }

        }

        return $user;
    }

} 