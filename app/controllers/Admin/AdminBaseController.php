<?php
namespace MissionNext\Controllers\Admin;

use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Session\SessionManager;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Form;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Cartalyst\Sentry\Users\LoginRequiredException as LoginRequired;
use Cartalyst\Sentry\Users\PasswordRequiredException as PasswordRequired;
use Cartalyst\Sentry\Users\WrongPasswordException as WrongPass;
use Cartalyst\Sentry\Users\UserNotFoundException as UserNotFound;
use Cartalyst\Sentry\Users\UserNotActivatedException as UserNotActivated;
use Cartalyst\Sentry\Throttling\UserSuspendedException as UserSuspended;
use Cartalyst\Sentry\Throttling\UserBannedException as UserBanned;
use Cartalyst\Sentry\Users\UserExistsException as UserExist;
use Cartalyst\Sentry\Users\UserAlreadyActivatedException as UserAlreadyActivated;
use Illuminate\View\Factory;
use MissionNext\Api\Service\Payment\PaymentGatewayInterface;
use MissionNext\Filter\RouteSecurityFilter;
use MissionNext\Repos\RepositoryContainerInterface;
use MissionNext\Controllers\traits\Controller as SecurityTraits;
use Cartalyst\Sentry\Sentry as MainSentry;

use Illuminate\Support\Facades\Queue;
use MissionNext\Models\Matching\Results;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Facade\SecurityContext;
use MissionNext\Api\Auth\SecurityContext as SC;
use MissionNext\Models\Subscription\Partnership;
use MissionNext\Models\Subscription\Subscription;
use MissionNext\Api\Service\DataTransformers\UserCachedDataStrategy;
use MissionNext\Api\Service\DataTransformers\UserCachedTransformer;
use MissionNext\Api\Service\Matching\TransData;
use MissionNext\Api\Service\Matching\Queue\Master\ProfileUpdateMatching;


class AdminBaseController extends Controller
{

    use SecurityTraits;

    const PAGINATE = 30;

    const VIEW_PREFIX = '';
    const ROUTE_PREFIX = '';

    protected $request;
    protected $repoContainer;
    /** @var \Illuminate\View\Factory */
    protected $view;

    protected $redirect;
    protected $session;

    /** @var \MissionNext\Api\Service\Payment\AuthorizeNet */
    protected $paymentGateway;

    /** @var  MainSentry */
    protected $sentry;


    public function __construct(PaymentGatewayInterface $paymentGateway, Store $session, Redirector $redirector, Request $request, RepositoryContainerInterface $containerInterface, Factory $viewFactory)
    {
        //$this->beforeFilter('csrf', array('on'=>'post'));
        $this->beforeFilter(RouteSecurityFilter::ROLE_ADMIN_AREA);
        $this->sentry = Sentry::getFacadeRoot();
        $this->request = $request;
        $this->repoContainer = $containerInterface;
        $this->view = $viewFactory;
        $this->redirect = $redirector;
        $this->session = $session;
        $this->paymentGateway = $paymentGateway;
    }


    /**
     *
     * @return \Illuminate\View\View
     */
    public function login()
    {
        if ($this->request->isMethod('post')) {
            Input::flash();
            $input = Input::only('username', 'password');
            $rules = array(
                'username' => 'required|min:3|max:200',
                'password' => 'required|min:4'
            );
            $validator = Validator::make($input, $rules);
            if ($validator->fails()) {

                return Redirect::route('login')->withInput()->withErrors($validator);
            }
            try {
                $user = Sentry::authenticate($input, false);

                return Redirect::route('adminHomepage');
            } catch (LoginRequired $e) {
                Session::flash('info', 'Login field is required.');
            } catch (PasswordRequired $e) {
                Session::flash('info', 'Password field is required.');
            } catch (WrongPass $e) {
                Session::flash('info', 'Wrong password, try again.');
            } catch (UserNotFound $e) {
                Session::flash('info', 'User was not found.');
            } catch (UserNotActivated $e) {
                Session::flash('info', 'User is not activated.');
            } // The following is only required if the throttling is enabled
            catch (UserSuspended $e) {
                Session::flash('info', 'User is suspended.');
            } catch (UserBanned $e) {
                Session::flash('info', 'User is banned.');
            }
        }

        return View::make('admin.loginForm');
    }

    /**
     * @param $name
     *
     * @return string
     */
    protected function viewTemplate($name)
    {

//        print_r($this->securityContext()->role());

//        $queueData = ["userId"=> 300, "appId"=>3, "role" => BaseDataModel::CANDIDATE];
//        ProfileUpdateMatching::run($queueData);

//        try
//        {
//            if ($job = Queue::getPheanstalk()->peekReady('default')) {
//
//
//
////                echo "<pre>";
////                print_r(json_decode($job->getData()));
////                echo "</pre>";
//
//                echo json_decode($job->getData())->data->userId;
//
//            }
//        }
//        catch(\Pheanstalk_Exception_ServerException $e){}

//        $test = $this->matchingResults(BaseDataModel::ORGANIZATION, BaseDataModel::CANDIDATE, 192);
//        $test = $this->matchingResults(BaseDataModel::CANDIDATE, BaseDataModel::ORGANIZATION, 300);


//        echo mb_strlen($test, '8bit');

//        echo "<pre>";
//        print_r($test);
//        echo "</pre>";

        return static::VIEW_PREFIX . ".{$name}";
    }

    /**
     * @param $name
     *
     * @return string
     */
    protected function routeName($name)
    {

        return static::ROUTE_PREFIX . ".{$name}";
    }






//    const PAGINATION = 100;
//
//    /**
//     * @return SC
//     */
//    public function securityContext()
//    {
//
//        return SecurityContext::getInstance();
//    }
//
//
//    /**
//     * @param $forUserType
//     * @param $userType
//     * @param $forUserId
//     *
//     * @return array
//     */
//    public function matchingResults($forUserType, $userType, $forUserId)
//    {
//        $start = microtime(true);
//
//        $org_select = '';
//        if ($userType === BaseDataModel::JOB) {
//            $org_select = ", organization_cached_profile.data->'profileData'->>'organization_name' as org_name";
//        }
//
//        $builder =
//            Results::select(DB::raw("distinct on (matching_results.user_type, matching_results.user_id, matching_results.for_user_id, matching_results.for_user_type, matching_results.matching_percentage) matching_results.data, folder_apps.folder, notes.notes, subscriptions.partnership, subscriptions.id as sub_id $org_select") )
//                ->leftJoin("folder_apps", function($join) use ($forUserId, $forUserType, $userType){
//                    $join->on("folder_apps.user_id", "=", "matching_results.user_id")
//                        ->where("matching_results.for_user_type", "=", $forUserType)
//                        ->where("folder_apps.for_user_id", "=", $forUserId)
//                        ->where("folder_apps.user_type", "=", $userType)
//                        ->where("folder_apps.app_id", "=", 3);
//                })
//                ->leftJoin("notes", function($join) use ($forUserId, $forUserType, $userType){
//                    $join->on("notes.user_id", "=", "matching_results.user_id")
//                        ->where("notes.for_user_id", "=", $forUserId)
//                        ->where("notes.user_type", "=", $userType);
//                })
//                ->where("matching_results.for_user_type","=", $forUserType)
//                ->where("matching_results.user_type", "=", $userType)
//                ->where("matching_results.for_user_id", "=",  $forUserId)
//                ->whereRaw("ARRAY[?] <@ json_array_text(matching_results.data->'app_ids')", [3]);
//
//
//        if ($userType === BaseDataModel::JOB ) {
//            $builder->leftJoin("organization_cached_profile", "organization_cached_profile.id", "=", DB::raw("(matching_results.data->'organization'->>'id')::int"));
//        }
//
//        $updates = '2015-01-01 00:00:00';
//        $builder->leftJoin("users", "users.id", "=", 'matching_results.user_id')
//                ->where('users.updated_at', '>=', $updates);
//
//
//        $builder = $userType === BaseDataModel::JOB ? $builder->leftJoin("subscriptions", "subscriptions.user_id", "=",  DB::raw("(matching_results.data->'organization'->>'id')::int"))
//            : $builder->leftJoin("subscriptions", "subscriptions.user_id", "=", "matching_results.user_id");
//
//        $builder->where('subscriptions.app_id', '=', 3 )
//            ->where('subscriptions.status', '<>', Subscription::STATUS_CLOSED)
//            ->where(function($query){
//                $query->where('subscriptions.partnership', "<>", Partnership::LIMITED)
//                    ->orWhereNull('subscriptions.partnership');
//            })
////            ->where('subscriptions.partnership', "<>", Partnership::LIMITED)
//            ->whereNotNull('subscriptions.id')
//            ->where(function($query){
//                $query->where('subscriptions.status', '<>', Subscription::STATUS_EXPIRED)
//                    ->orWhere('subscriptions.price', "=", 0);
//            });
//
//        $builder->orderBy('matching_results.matching_percentage');
//
////        $builder->whereRaw("to_date(matching_results.data->>'created_at', 'YYYY-MM-DD') >= ?", [$updates . '-01-01']);
//
////            ->where("ARRAY[?] <@ json_array_text(matching_results.data->'created_at')", [2013]);
//
////        echo "<pre>";
////        print_r($builder);
////        echo "</pre>";
//
//        $result =
//            (new UserCachedTransformer($builder, new UserCachedDataStrategy()))->paginate(static::PAGINATION);
//
//        echo '<br>' . count($result) . '<br>';
//
//        $end = microtime(true);
//
//        echo $start . '<br>';
//        echo $end;
//
//        return $result;
////        return (new TransData($this->securityContext()->getToken()->language(), $userType, $result->toArray()))->get();
//
//    }

}