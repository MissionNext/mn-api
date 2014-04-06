<?php
namespace Api;

use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use MissionNext\Api\Auth\Token;
use Illuminate\Support\Facades\DB;
use MissionNext\Api\Exceptions\ProfileException;
use MissionNext\Api\Exceptions\ValidationException;
use MissionNext\Facade\SecurityContext as FSecurityContext;
use MissionNext\Api\Auth\SecurityContext;
use MissionNext\Filter\RouteSecurityFilter;
use MissionNext\Models\Application\Application as AppModel;
use MissionNext\Models\Field\FieldFactory;
use MissionNext\Models\Field\FieldType;
use MissionNext\Models\Profile;
use MissionNext\Models\User\User as UserModel;
use MissionNext\Repos\Field\FieldRepository;
use MissionNext\Repos\Field\FieldRepositoryInterface;
use MissionNext\Repos\Form\FormRepository;
use MissionNext\Repos\Form\FormRepositoryInterface;
use MissionNext\Repos\User\UserRepository;
use MissionNext\Repos\User\UserRepositoryInterface;
use MissionNext\Repos\ViewField\ViewFieldRepository;
use MissionNext\Repos\ViewField\ViewFieldRepositoryInterface;
use MissionNext\Validators\ValidatorResolver;


class BaseController extends Controller
{
    /** @var \MissionNext\Repos\Field\FieldRepository */
    private $fieldRepo;
    /** @var \MissionNext\Repos\User\UserRepositoryInterface  */
    private $userRepo;
    /** @var \MissionNext\Repos\ViewField\ViewFieldRepositoryInterface  */
    private $viewFieldRepo;

    private $formRepo;

    /**
     * Set filters
     */
    public function __construct(ValidatorResolver $valResolver,
                                FieldRepositoryInterface $fieldRepo,
                                UserRepositoryInterface $userRepo,
                                ViewFieldRepositoryInterface $viewFieldRepo,
                                FormRepositoryInterface $formRepo
    )
    {
        $this->fieldRepo = $fieldRepo;
        $this->userRepo = $userRepo;
        $this->viewFieldRepo = $viewFieldRepo;
        $this->formRepo = $formRepo;
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
     * @return UserRepository
     */
    protected function userRepo()
    {

        return  $this->userRepo;
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

    /**
     * @param Collection $fields
     *
     * @return Collection
     */
    protected function fieldsChoicesArr(Collection $fields)
    {

        return $fields->each(function ($field) {

            $field->choices = $field->choices ? explode(",", $field->choices) : null;

            return $field;
        });
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

    protected function generateProfile(UserModel $user)
    {
        $profile = new Profile();
        $fields = $this->fieldRepo()->profileFields($user);

        $fields->get()->each(function ($field) use ($profile) {
            $key = $field->symbol_key;
            if (isset($profile->$key)) {
                $profile->$key = array_merge($profile->$key, [$field->pivot->value]);
            } else {
                $profile->$key = FieldType::isMultiple($field->type) ? [$field->pivot->value] : $field->pivot->value;
            }
        });

        return $profile;
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
        /** @var  $fields Collection */
        $fields = $this->fieldRepo()->modelFields()->whereIn('symbol_key', $fieldNames)->get();

        if ($fields->count() !== count($profileData)) {

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
     * @param UserModel $user
     * @param array $profileData
     *
     * @return UserModel
     * @throws \MissionNext\Api\Exceptions\ValidationException
     * @throws \MissionNext\Api\Exceptions\ProfileException
     */
    protected function updateUserProfile(UserModel $user, array $profileData = null)
    {
        if (empty($profileData)) {

            return $user;
        }

        $fields = $this->validateProfileData($profileData);
        $mapping = [];

        foreach ($fields as $field) {
            if (isset($profileData[$field->symbol_key])) {
                $mapping[$field->id] = ["value" => $profileData[$field->symbol_key]];
            }//@TODO if example favourite_movies[] = '', no errors;
        }

        $user->save(); //@TODO SAVE USER other place

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
        }

        return $user;
    }

} 