<?php
namespace Api;

use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use MissionNext\Api\Auth\Token;
use Illuminate\Support\Facades\DB;
use MissionNext\Api\Exceptions\ProfileException;
use MissionNext\Api\Exceptions\ValidationException;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Facade\SecurityContext as FSecurityContext;
use MissionNext\Api\Auth\SecurityContext;
use MissionNext\Filter\RouteSecurityFilter;
use MissionNext\Models\Application\Application as AppModel;
use MissionNext\Models\Field\FieldFactory;
use MissionNext\Models\Profile;
use MissionNext\Models\User\User as UserModel;
use MissionNext\Repos\User\UserRepository;


class BaseController extends Controller
{
    /**
     * Set filters
     */
    public function __construct()
    {
        $this->beforeFilter(RouteSecurityFilter::AUTHORIZE);
        $this->beforeFilter(RouteSecurityFilter::ROLE);
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

    /**
     * @return UserRepository
     */
    protected function userRepository()
    {

        return new UserRepository();
    }

    protected function generateProfile(UserModel $user)
    {
        $profile = new Profile();
        $fields = FieldFactory::fieldsOfModel($user);
        $fields->get()->each(function ($field) use ($profile) {
            $key = $field->symbol_key;
            $profile->$key = $field->pivot->value;
        });

        return $profile;
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
        if (empty($profileData)){

            return $user;
        }
        $mapping = [];
        $fieldNames = array_keys($profileData);
        $fields = $this->getApp()->modelFields()->whereIn('symbol_key', $fieldNames)->get();
        if ($fields->count() !== count($profileData)) {

            throw new ProfileException("Wrong field name(s)", ProfileException::ON_UPDATE);
        }
        $constraints = [];
        $validationData = [];
        foreach ($fields as $field) {
            if (isset($profileData[$field->symbol_key])) {
                $validationData[$field->symbol_key] = $profileData[$field->symbol_key];
                $constraints[$field->symbol_key] = $field->pivot->constraints;
                $mapping[$field->id] = ["value" => $profileData[$field->symbol_key]];
            }
        }
        /** @var  $validator \Illuminate\Validation\Validator */
        $validator = Validator::make(
            $validationData,
            $constraints
        );
        if ($validator->fails())
        {
            throw new ValidationException($validator->messages());
        }
        $user->save();
        foreach ($mapping as $key => $map) {
            FieldFactory::fieldsOfModel($user)->detach($key, $map);
            FieldFactory::fieldsOfModel($user)->attach($key, $map);
        }
        if (!empty($mapping)) {
            $user->touch();
        }

        return $user;
    }

} 