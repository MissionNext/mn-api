<?php

namespace App\Modules\Api\MissionNext\Controllers;

use App\Modules\Api\Service\Payment\AuthorizeNet;
use App\Repos\RepositoryContainer;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Route as Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Modules\Api\Exceptions\ProfileException;
use App\Modules\Api\Exceptions\ValidationException;
use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\Service\Payment\PaymentGatewayInterface;
use App\Modules\Api\Filter\RouteSecurityFilter;
use App\Models\DataModel\BaseDataModel;
use App\Models\Job\Job;
use App\Models\ProfileInterface;
use App\Models\User\User;
use App\Repos\Field\FieldRepository;
use App\Repos\Field\FieldRepositoryInterface;
use App\Repos\Form\FormRepository;
use App\Repos\Form\FormRepositoryInterface;
use App\Repos\FormGroup\FormGroupRepository;
use App\Repos\FormGroup\FormGroupRepositoryInterface;
use App\Repos\Matching\ConfigRepository;
use App\Repos\Matching\ConfigRepositoryInterface;
use App\Repos\Matching\ResultsRepository;
use App\Repos\Matching\ResultsRepositoryInterface;
use App\Repos\RepositoryContainerInterface;
use App\Repos\User\JobRepository;
use App\Repos\User\JobRepositoryInterface;
use App\Repos\User\ProfileRepositoryFactory;
use App\Repos\User\UserRepository;
use App\Repos\User\UserRepositoryInterface;
use App\Repos\ViewField\ViewFieldRepository;
use App\Repos\ViewField\ViewFieldRepositoryInterface;
use App\Modules\Api\Validators\ValidatorResolver;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Modules\Api\Traits\Controller as SecurityTraits;

class BaseController extends Controller
{

    use SecurityTraits;

    /** @var FieldRepository */
    private $fieldRepo;
    /** @var UserRepositoryInterface */
    private $userRepo;
    /** @var ViewFieldRepositoryInterface */
    private $viewFieldRepo;
    /** @var FormRepositoryInterface */
    private $formRepo;
    /** @var FormGroupRepositoryInterface */
    private $formGroupRepo;
    /** @var JobRepositoryInterface */
    private $jobRepo;
    /** @var ConfigRepositoryInterface */
    private $matchingConfigRepo;

    /** @var  ResultsRepositoryInterface */
    private $matchResultsRepo;
    /**
     * @var Request
     */
    protected $request;

    /** @var RepositoryContainer */
    protected $repoContainer;
//    /** @var AuthorizeNet  */
//    protected $paymentGateway;

    /**
     * Set filters
     */
    public function __construct(
        ValidatorResolver            $valResolver,
        FieldRepositoryInterface     $fieldRepo,
        UserRepositoryInterface      $userRepo,
        ViewFieldRepositoryInterface $viewFieldRepo,
        FormRepositoryInterface      $formRepo,
        FormGroupRepositoryInterface $formGroupRepo,
        JobRepositoryInterface       $jobRepo,
        ConfigRepositoryInterface    $matchingConfigRepo,
        ResultsRepositoryInterface   $matchResultsRepo,
        Request                      $request,
        Router                       $route,
        RepositoryContainerInterface $repoContainer
//                                PaymentGatewayInterface $paymentGateway
    )

    {
//dd($formGroupRepo);
        // dd($matchingConfigRepo);
        $this->request = $request;
        $this->fieldRepo = $fieldRepo;
        $this->userRepo = $userRepo;
        $this->viewFieldRepo = $viewFieldRepo;
        $this->formRepo = $formRepo;
        $this->formGroupRepo = $formGroupRepo;
        $this->jobRepo = $jobRepo;
        $this->matchingConfigRepo = $matchingConfigRepo;
        $this->matchResultsRepo = $matchResultsRepo;

//        $this->paymentGateway = $paymentGateway;

        $this->repoContainer = $repoContainer;

        (new \App\Modules\Api\Filter\RouteSecurityFilter)->authorize($route, $request);
        (new \App\Modules\Api\Filter\RouteSecurityFilter)->role($route, $request);
//        $this->beforeFilter(RouteSecurityFilter::AUTHORIZE);
//        $this->beforeFilter(RouteSecurityFilter::ROLE);

    }

    /**
     * @param string $tube
     */
    protected function clearTube(string $tube = 'default')
    {
        try {
            while ($job = Queue::getPheanstalk()->peekReady($tube)) {
                Queue::getPheanstalk()->delete($job);
            }
        } catch (\Pheanstalk_Exception_ServerException $e) {
        }
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
     * @return ConfigRepositoryInterface
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

        return $this->userRepo;
    }

    /**
     * @return JobRepository
     */
    protected function jobRepo()
    {
        return $this->jobRepo;
    }

    /**
     * @return ResultsRepository
     */
    protected function matchingResultsRepo()
    {

        return $this->matchResultsRepo;
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
     * @throws ValidationException
     * @throws ProfileException
     */
    protected function validateProfileData(array $profileData, $saveLater = null)
    {
        $fieldNames = array_keys($profileData);

        $dependentFields = $this->formGroupRepo()->dependentFields()->get();
        if (isset($dependentFields) && !empty($dependentFields)) {
            foreach ($dependentFields as $field) {
                $ownerField = $field->depends_on;
                $ownerFieldOption = $field->depends_on_option;
                if (isset($profileData[$ownerField])) {
                    $ownerFieldType = $profileData[$ownerField]['type'];
                    $ownerFieldValue = $profileData[$ownerField]['value'];
                    if (!$ownerFieldValue ||
                        ("radio_yes_no" === $ownerFieldType && "No" === $ownerFieldValue) ||
                        ("custom_marital" === $ownerFieldType && "Married" !== $ownerFieldValue) ||
                        (!empty($ownerFieldOption) && $ownerFieldValue != $ownerFieldOption)
                    ) {
                        $fieldNames = array_diff($fieldNames, $field->symbol_keys);
                    }
                }
            }
        }

        $fields = $this->fieldRepo()->modelFields()->whereIn('symbol_key', $fieldNames)->get();

//        if ($fields->count() !== count($fieldNames)) {
//            throw new ProfileException("Wrong field name(s)", ProfileException::ON_UPDATE);
//        }

        $constraints = [];
        $validationData = [];

        foreach ($fields as $field) {
            if (isset($profileData[$field->symbol_key])) {
                $validationData[$field->symbol_key] = $profileData[$field->symbol_key]['value'];//@TODO can be array
                if ($field->pivot->constraints) {
                    $requiredRule = strpos($field->pivot->constraints, 'required');
                    if ($requiredRule === false) {
                        $constraints[$field->symbol_key] = $field->pivot->constraints;
                    } elseif ($saveLater) {
                        $rulesArray = explode("|", $field->pivot->constraints);
                        $newRules = [];
                        foreach ($rulesArray as $rule) {
                            if ('required' != $rule) {
                                $newRules[] = $rule;
                            }
                        }
                        $rules = implode("|", $newRules);
                        $constraints[$field->symbol_key] = $rules;
                    } else {
                        $constraints[$field->symbol_key] = $field->pivot->constraints;
                    }
                } else {
                    $constraints[$field->symbol_key] = '';
                }
            }
        }

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
    protected function updateUserProfile(ProfileInterface $user, array $profileData = null, $changedFields = null, $saveLater = null, $newUser = false)
    {

        /** @var $user User|Job */
        if (empty($profileData)) {

            $user->touch();

            return $user;
        }

        $profileData = $this->uploadFiles($user, $profileData);

        $fields = $this->validateProfileData($profileData, $saveLater);
//dd($fields);
        if (
            !isset($changedFields)
            || (isset($changedFields, $changedFields['changedFields'])
                && count($changedFields['changedFields']) > 0)
        ) {
            $user->touch();
        }

        $mapping = [];
        $sKeys = [];

        foreach ($fields as $field) {
            if (isset($profileData[$field->symbol_key])) {
                $mapping[$field->id] = ["value" => $profileData[$field->symbol_key]['value'], "dictionary_id" => $profileData[$field->symbol_key]['dictionary_id'] ?: null];
                $sKeys[$field->id] = $field->symbol_key;
            }
        }

        $profileDataKeys = array_keys($profileData);
        $unsavedFieldKeys = array_diff($profileDataKeys, $sKeys);
        if (count($unsavedFieldKeys) > 0) {
            $unsavedFields = $this->fieldRepo()->modelFields()->whereIn('symbol_key', $unsavedFieldKeys)->get();

            if ($unsavedFields) {
                foreach ($unsavedFields as $field) {
                    if (isset($profileData[$field->symbol_key])) {
                        $mapping[$field->id] = ["value" => $profileData[$field->symbol_key]['value'], "dictionary_id" => $profileData[$field->symbol_key]['dictionary_id'] ?: null];
                        $sKeys[$field->id] = $field->symbol_key;
                    }
                }
            }
        }

        foreach ($mapping as $key => $map) {
            $this->fieldRepo()->profileFields($user)->detach($key, true);
            if (is_array($map['value'])) {
                foreach ($map['value'] as $k => $val) {
                    $this->fieldRepo()->profileFields($user)->attach($key, ["value" => $val, "dictionary_id" => $map['dictionary_id'][$k] ?? null]);
                }
            } else {
                $this->fieldRepo()->profileFields($user)->attach($key, $map);
            }
        }


        if (!empty($mapping)) {
            //$user->touch();
            /** @var  $userRepo UserRepository|JobRepository */
            $userRepo = $this->repoContainer[ProfileRepositoryFactory::KEY]->profileRepository();
            $userRepo->addUserCachedData($user);
            $queueData = ["userId" => $user->id, "appId" => $this->getApp()->id(), "role" => $this->securityContext()->role()];

            if (!$newUser) {
                $checkRecord = DB::table('user_profile_completed')
                    ->where('user_id', $user->id)
                    ->where('app_id', $this->getApp()->id())->first();

                if (!$checkRecord) {
                    DB::table('user_profile_completed')->insert([
                        'user_id' => $user->id,
                        'app_id' => $this->getApp()->id(),
                        'role' => $user->role(),
                        'completed' => true
                    ]);
                }
            }


            if (!isset($changedFields) || ('checked' === $changedFields['status'] && $this->checkMatchingFields($queueData, $changedFields))) {
                $queueRecord = DB::table('queue_users_list')
                    ->where('user_id', $user->id)
                    ->where('app_id', $this->getApp()->id())
                    ->where('role', $user->role())->first();

                if (!$queueRecord) {
                    DB::table('queue_users_list')
                        ->insert(array(
                            'user_id' => $user->id,
                            'app_id' => $this->getApp()->id(),
                            'role' => $user->role()
                        ));
                }
            }
        }

        return $user;
    }

    /**
     * @param array $files
     * @param array $hash
     */
    protected function checkFile(array $files, array &$hash)
    {
        if (!empty($files)) {
            foreach ($files as $symbolKey => $file) {
                $hash[$symbolKey]['value'] = $file;
                $hash[$symbolKey]['dictionary_id'] = null;
                $hash[$symbolKey]['type'] = 'field';
            }
        }
    }

    protected function checkMatchingFields($queueData, $changedFields)
    {
        $matchedFlag = false;

        if (!isset($changedFields['changedFields'])) {
            return $matchedFlag;
        }

        switch ($queueData['role']) {
            case 'candidate':

                $this->securityContext()->getToken()->setRoles([BaseDataModel::ORGANIZATION]);
                $configRepo = (new ConfigRepository())->setSecurityContext($this->securityContext());
                $candidateOrg = $configRepo->configByCandidateOrganizations()->get();

                $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);
                $configRepo = (new ConfigRepository())->setSecurityContext($this->securityContext());
                $canJob = $configRepo->configByCandidateJobs()->get();

                foreach ($candidateOrg as $item) {
                    if (in_array($item['candidate_key'], $changedFields['changedFields'])) {
                        $matchedFlag = true;
                        break 2;
                    }
                }
                foreach ($canJob as $item) {
                    if (in_array($item['candidate_key'], $changedFields['changedFields'])) {
                        $matchedFlag = true;
                        break 2;
                    }
                }
                break;
            case 'organization':
                $this->securityContext()->getToken()->setRoles([BaseDataModel::ORGANIZATION]);
                $configRepo = (new ConfigRepository())->setSecurityContext($this->securityContext());
                $orgCandidate = $configRepo->configByOrganizationCandidates()->get();

                foreach ($orgCandidate as $item) {
                    if (in_array($item['organization_key'], $changedFields['changedFields'])) {
                        $matchedFlag = true;
                        break 2;
                    }
                }
                break;
            case 'job':
                $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);
                $configRepo = (new ConfigRepository())->setSecurityContext($this->securityContext());
                $jobCandidate = $configRepo->configByJobCandidates()->get();

                foreach ($jobCandidate as $item) {
                    if (in_array($item['job_key'], $changedFields['changedFields'])) {
                        $matchedFlag = true;
                        break 2;
                    }
                }
                break;
        }

        return $matchedFlag;
    }

    /**
     * @param $forUserId
     * @param $userId
     *
     * @return RestResponse
     */
    protected function getOneMatch($forUserId, $userId)
    {
        $role = $this->request->get('role');

        $orgAppIds = $this->securityContext()->getToken()->currentUser()->appIds();
        if (in_array($this->securityContext()->getApp()->id, $orgAppIds)) {

            $result = $this->matchingResultsRepo()->oneMatchingResult($forUserId, $userId, $role);

            return new RestResponse($result);
        }
        return new RestResponse([]);
    }

    private function uploadFiles($user, $profileData)
    {
        $fileFields = [];
        $profileToValidate = [];

        foreach ($profileData as $profileKey => $profileValue) {
            if ($profileValue['value'] instanceof UploadedFile) {
                $profileToValidate[$profileKey] = $profileValue;
            }
        }
        if (count($profileToValidate) > 0) {
            $this->validateProfileData($profileToValidate);

            foreach ($profileToValidate as $profileKey => $profileValue) {
                $fileField = $this->fieldRepo()->modelFields()->where('symbol_key', $profileKey)->first();
                $this->fieldRepo()->profileFields($user)->detach($fileField->id, true);
                $file = $profileValue['value'];
                $fileName = $this->securityContext()->role() . $user->id . "_" . $profileKey . "." . $file->getClientOriginalExtension();
                $file->move(app_path() . "/storage/uploads", $fileName);
                $this->fieldRepo()->profileFields($user)->attach($fileField->id, ["value" => $fileName]);
                $fileFields[] = $profileKey;
            }

            foreach ($fileFields as $unsetField) {
                unset($profileData[$unsetField]);
            }
        }

        return $profileData;
    }

    protected function logger($log_type, $action, $message)
    {
        $view_log = new Logger('View Logs');
        $view_log->pushHandler(new StreamHandler(storage_path() . '/logs/custom_logs/' . $log_type . '/' . date('Y-m-d') . '.txt', Logger::INFO));
        $view_log->info('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
        $view_log->info('Action: ' . $action);
        $view_log->addInfo($message);
        $view_log->info('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
    }
}
