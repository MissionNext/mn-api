<?php


namespace MissionNext\Controllers\Api;


use Illuminate\Http\Request;
use MissionNext\Filter\RoleChecker;
use MissionNext\Repos\Field\FieldRepositoryInterface;
use MissionNext\Repos\Form\FormRepositoryInterface;
use MissionNext\Repos\FormGroup\FormGroupRepositoryInterface;
use MissionNext\Repos\Matching\ConfigRepositoryInterface;
use MissionNext\Repos\Matching\ResultsRepositoryInterface;
use MissionNext\Repos\User\JobRepositoryInterface;
use MissionNext\Repos\User\UserRepositoryInterface;
use MissionNext\Repos\ViewField\ViewFieldRepositoryInterface;
use MissionNext\Validators\ValidatorResolver;

class RoleCheckerController extends BaseController
{
    public function __construct(ValidatorResolver $valResolver,
                                FieldRepositoryInterface $fieldRepo,
                                UserRepositoryInterface $userRepo,
                                ViewFieldRepositoryInterface $viewFieldRepo,
                                FormRepositoryInterface $formRepo,
                                FormGroupRepositoryInterface $formGroupRepo,
                                JobRepositoryInterface $jobRepo,
                                ConfigRepositoryInterface $matchingConfigRepo,
                                ResultsRepositoryInterface $matchResultsRepo,
                                Request $request){


        parent::__construct( $valResolver,
                                $fieldRepo,
                                 $userRepo,
                                 $viewFieldRepo,
                                 $formRepo,
                                 $formGroupRepo,
                                 $jobRepo,
                                 $matchingConfigRepo,
                                 $matchResultsRepo,
                                 $request);

        $this->beforeFilter(RoleChecker::CHECK);
    }
} 