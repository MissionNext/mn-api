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
use MissionNext\Api\Response\RestResponse;
use MissionNext\Api\Service\Matching\Queue\Master\ProfileUpdateMatching;
use MissionNext\Api\Service\Payment\PaymentGatewayInterface;
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
use MissionNext\Repos\Inquire\InquireRepository;
use MissionNext\Repos\Matching\ConfigRepository;
use MissionNext\Repos\Matching\ConfigRepositoryInterface;
use MissionNext\Repos\Matching\ResultsRepository;
use MissionNext\Repos\Matching\ResultsRepositoryInterface;
use MissionNext\Repos\RepositoryContainer;
use MissionNext\Repos\RepositoryContainerInterface;
use MissionNext\Repos\User\JobRepository;
use MissionNext\Repos\User\JobRepositoryInterface;
use MissionNext\Repos\User\ProfileRepositoryFactory;
use MissionNext\Repos\User\UserRepository;
use MissionNext\Repos\User\UserRepositoryInterface;
use MissionNext\Repos\ViewField\ViewFieldRepository;
use MissionNext\Repos\ViewField\ViewFieldRepositoryInterface;
use MissionNext\Custom\Validators\ValidatorResolver;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use MissionNext\Controllers\traits\Controller as SecurityTraits;

class BaseController extends Controller
{

    use SecurityTraits;
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
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /** @var \MissionNext\Repos\RepositoryContainer  */
    protected $repoContainer;
    /** @var \MissionNext\Api\Service\Payment\AuthorizeNet  */
    protected $paymentGateway;

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
                                Request $request,
                                RepositoryContainerInterface $repoContainer,
                                PaymentGatewayInterface $paymentGateway
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
        $this->matchResultsRepo = $matchResultsRepo;

        $this->paymentGateway = $paymentGateway;

        $this->repoContainer = $repoContainer;

        $this->beforeFilter(RouteSecurityFilter::AUTHORIZE);
        $this->beforeFilter(RouteSecurityFilter::ROLE);

    }

    /**
     * @param string $tube
     */
    protected  function clearTube($tube = 'default')
    {
        try
        {
            while($job =  Queue::getPheanstalk()->peekReady($tube))
            {
                Queue::getPheanstalk()->delete($job);
            }
        }
        catch(\Pheanstalk_Exception_ServerException $e){}
    }

    public function checkQueue($tube = 'default')
    {
        try
        {
            if ($job = Queue::getPheanstalk()->peekReady($tube)) {
                return 1;
            }
        }
        catch(\Pheanstalk_Exception_ServerException $e){}

        return 0;
    }

    /**
     * @return RestResponse
     */
    public function testApi()
    {

        return new RestResponse(true);
    }

    /**
     * @return FieldRepository
     */
    protected function fieldRepo()
    {

        return $this->repoContainer[FieldRepositoryInterface::KEY];
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
        //dd($dependentFields->toArray());
        foreach($dependentFields as $field){
            $ownerField = $field->depends_on;
            if (isset($profileData[$ownerField])){
                $ownerFieldValue = $profileData[$ownerField]['value'];
                if (!$ownerFieldValue) {
                    $fieldNames = array_diff($fieldNames, $field->symbol_keys);
                }
            }
        }
        /** @var  $fields Collection */
        $fields = $this->fieldRepo()->modelFields()->whereIn('symbol_key', $fieldNames)->get();

        if ($fields->count() !== count($fieldNames)) {

            throw new ProfileException("Wrong field name(s)", ProfileException::ON_UPDATE);
        }

        $constraints = [];
        $validationData = [];

        foreach ($fields as $field) {
            if (isset($profileData[$field->symbol_key])) {
                $validationData[$field->symbol_key] = $profileData[$field->symbol_key]['value'];//@TODO can be array
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
//        $this->userRepo()->updateUserCachedData($user);
//        return true;
        //=========
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
                $mapping[$field->id] = ["value" => $profileData[$field->symbol_key]['value'], "dictionary_id" => $profileData[$field->symbol_key]['dictionary_id'] ? : null  ];
                $sKeys[$field->id] = $field->symbol_key;
            }
        }
        foreach ($mapping as $key => $map) {
            $this->fieldRepo()->profileFields($user)->detach($key, true);
            if (is_array($map['value'])) {
                foreach ($map['value'] as $k => $val) {
                    $this->fieldRepo()->profileFields($user)->attach($key, ["value" => $val, "dictionary_id" => $map['dictionary_id'][$k] ? : null]);
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
            /** @var  $userRepo UserRepository|JobRepository */
            $userRepo = $this->repoContainer[ProfileRepositoryFactory::KEY]->profileRepository();
            $userRepo->addUserCachedData($user);
            $queueData = ["userId"=>$user->id, "appId"=>$this->getApp()->id(), "role" => $this->securityContext()->role()];
            ProfileUpdateMatching::run($queueData);
        }

        return $user;
    }

    /**
     * @param array $files
     * @param array $hash
     */
    protected function checkFile(array $files, array &$hash)
    {
        if (!empty($files)){
            foreach($files as $symbolKey => $file){
                $hash[$symbolKey]['value'] = $file;
                $hash[$symbolKey]['dictionary_id'] = null;
                $hash[$symbolKey]['type'] = 'field';
            }
        }
    }

} 