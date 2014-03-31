<?php
namespace Api;

use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use MissionNext\Api\Auth\Token;
use Illuminate\Support\Facades\DB;
use MissionNext\Api\Exceptions\ProfileException;
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
     * @return UserModel
     * @throws \MissionNext\Api\Exceptions\ProfileException
     */
    protected function updateUserProfile(UserModel $user, array $profileData = null)
    {
        if (empty($profileData)){

            return $user;
        }
        $mapping = [];
        $fieldNames = array_keys($profileData);
        $fields = FieldFactory::roleBasedModel()->whereIn('symbol_key', $fieldNames)->get();
        if ($fields->count() !== count($profileData)) {

            throw new ProfileException("Wrong field name(s)", ProfileException::ON_UPDATE);
        }
        foreach ($fields as $field) {
            if (isset($profileData[$field->symbol_key])) {
                $mapping[$field->id] = ["value" => $profileData[$field->symbol_key]];
            }
        }
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