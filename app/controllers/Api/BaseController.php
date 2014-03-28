<?php
namespace Api;

use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use MissionNext\Api\Auth\Token;
use Illuminate\Support\Facades\DB;
use MissionNext\Facade\SecurityContext as FSecurityContext;
use MissionNext\Api\Auth\SecurityContext;
use MissionNext\Filter\RouteSecurityFilter;
use MissionNext\Models\Application\Application as AppModel;
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

} 