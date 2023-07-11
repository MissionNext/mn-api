<?php
namespace App\Modules\Api\MissionNext\Controllers\User;

use App\Modules\Api\Auth\Token;
use App\Modules\Api\Exceptions\ModelObservableException;
use Illuminate\Support\Facades\Hash;
use App\Modules\Api\Exceptions\UserException;
use App\Modules\Api\Exceptions\ValidationException;
use App\Modules\Api\Response\RestResponse;
use Illuminate\Support\Facades\Request;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Modules\Api\Filter\RouteSecurityFilter;
use App\Models\DataModel\BaseDataModel;
use App\Models\Observers\UserObserver;
use App\Models\Role\Role;
use App\Models\User\User;
use App\Repos\CachedData\UserCachedRepository;
use App\Repos\CachedData\UserCachedRepositoryInterface;
use App\Repos\User\ProfileRepositoryFactory;
use App\Modules\Api\Validators\User as UserValidator;
use Input;
/**
 * Class UserController
 * @package App\Modules\Api\Controllers\User
 */
class UserController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return RestResponse
     */
    public function index()
    {
//        $this->securityContext()->getToken()->setRoles(['agency']);
//        foreach(['organization', 'candidate', 'job'] as $role){
//            $cacheRep = new UserCachedRepository($role);
//            $ids = $cacheRep->all()->pluck("id");
//            var_dump($ids);
//        }
//        UserCachedData::$tablePrefix = null;
//        dd((new UserCachedData)->getTable());

        //$this->clearTube(); exit;
       // $queueData = ["appId"=>$this->getApp()->id(), "role" => 'candidate', "userId" => 0];
        //ConfigUpdateMatching::run($queueData);

//        /** @var  $phn \Pheanstalk_Pheanstalk */
//        $phn = Queue::getPheanstalk();
//        $phn->clearTube('default');
//
//        $sc = SecurityContext::getInstance();
//        $sc->getToken()->setRoles(['organization']);
//        $configRepo = (new ConfigRepository())->setSecurityContext($sc);
//        $ss = $configRepo->configByOrganizationCandidates(BaseDataModel::CANDIDATE, 5)->get();
//
//        $cacheRep = new UserCachedRepository(BaseDataModel::ORGANIZATION);
//        $ids = $cacheRep->all()->pluck("id");
//        dd($ids);

        return new RestResponse($this->userRepo()->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return RestResponse
     */
    public function create()
    {
        //
    }

    /**
     * @return RestResponse
     * @throws UserException
     * @throws ValidationException|ModelObservableException
     */
    public function store()
    {
        $userValidator = new UserValidator( Request::instance() );

        if (!Input::get('profile'))
        {
            throw new ValidationException($userValidator->getErrors());
        }

        /** @var  $req \Symfony\Component\HttpFoundation\Request */
        $profileData = Input::get('profile');

        if ($files = Input::file()){
            $this->checkFile($files['profile'], $profileData);
        }

        $roleName = Input::get('role');

        if (!RouteSecurityFilter::isAllowedRole($roleName)){
            throw new UserException("Role '{$roleName}' doesn't exists", UserException::ON_CREATE);
        }
        $this->securityContext()->getToken()->setRoles([$roleName]);

        $role = Role::whereRole( $roleName )->firstOrFail();
        $userRep = $this->userRepo();
        $user = $userRep->getModel();
        $user->setObserver(new UserObserver());
        $user->setPassword(Input::get('password'));
        $user->setEmail(Input::get('email'));
        $user->setUsername(Input::get('username'));
        $user->setLastLogin();
        $user->setRole($role);
        $user->setActiveOnApps($this->getApp());

        if ($roleName === BaseDataModel::CANDIDATE){
            $user->setIsActive(true);
            $user->setStatus(0);
        }else{
            $user->setIsActive(false);
            $user->setStatus(User::STATUS_PENDING_APPROVAL);
        }

        $user->addApp($this->getApp());

       $newUser =  $this->updateUserProfile($user, $profileData, null, null, true);

        return new RestResponse($user);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return RestResponse
     */
    public function show($id)
    {
        /** @var  $cacheData UserCachedRepository */
        $cacheData = $this->repoContainer[UserCachedRepositoryInterface::KEY];
        $cacheData->findOrFail($id);

        return new RestResponse($cacheData->transData($this->getToken()->language()));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return RestResponse
     */
    public function edit($id)
    {
        //
    }

    /**
     * @param $id
     *
     * @return RestResponse
     *
     * @throws ValidationException
     */
    public function update($id)
    {
        /** @var  $user User  */
        $user = $this->userRepo()->find($id);

        $data = Request::only(["username", "email", "password"]);
        $filteredData = array_filter($data);

        $modelValidator = new UserValidator(Request::instance(), $user);

        if (!$modelValidator->passes()){

            throw new ValidationException($modelValidator->getErrors());
        }

        $user->timestamps = false;
        foreach ($filteredData as $prop => $val) {
            if ($user->$prop !== $val) {
                $user->timestamps = true;
            }
            $user->$prop = $val;
        }

        $user->save();

        return new RestResponse($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return RestResponse
     */
    public function destroy($id)
    {
        $user = $this->userRepo()->find($id);
        $user->delete();

        return new RestResponse($user);
    }

    /**
     * @return RestResponse
     */
    public function find()
    {
        $searchByData = Request::only(["username", "email"]);
        $searchByData = array_filter($searchByData);
        $str = '';
        $arrV = [];
        for ($c = count($searchByData), $i = 0; $i < $c; $i++) {
            $isAnd = $i !== ($c - 1) ? ' and ' : '';
            $str .= key($searchByData) . " = ?" . $isAnd;
            $arrV[] = current($searchByData);
            next($searchByData);
        }

        return new RestResponse($this->userRepo()->getModel()->whereRaw($str, $arrV)->get());
    }

    /**
     * @return RestResponse
     */
    public function check()
    {
        $password = Request::input('password');
        $username = Request::input('username');
        $user = $this->userRepo()->getModel()->with('roles')->whereUsername($username)->first();
        $user && !Hash::check($password, $user->password) && $user = null;
        if($user){
            $user->timestamps = false;
            $user->setLastLogin();
            $user->save();
            $this->logger('user', 'login', 'User: '.$user->username.'Last login: '.$user->last_login);
            $user = $user->toArray();
            $user['roles'] = $user['roles'][0]['role'];
        }

        return new RestResponse($user);
    }

    /**
     * @return RestResponse
     */
    public function passwordReset()
    {
        $status = 'error';
        $message = 'Error occured on password reset.';
        $password = Request::input('password');
        $username = Request::input('username');
        $user = $this->userRepo()->getModel()->with('roles')->whereRaw("lower(username)=?", array(strtolower($username)))->first();
        if($user){
            $user->setPassword($password);
            $user->save();
            $status = 'success';
            $message = 'Password reseted successfully.';
        }

        return new RestResponse([
            'status'    => $status,
            'message'   => $message
        ]);
    }

    public function deactivateUser($id)
    {
        $user = $this->userRepo()->find($id);
        $user->removeApp($this->getApp());
        $user->save();

        $role = $user->role();
        $this->securityContext()->getToken()->setRoles([$role]);
        $userRepo = $this->repoContainer[ProfileRepositoryFactory::KEY]->profileRepository();
        $userRepo->addUserCachedData($user);

        return new RestResponse([
            'status'    => 'success',
            'message'   => 'User deactivated on current application',
        ]);
    }

}
