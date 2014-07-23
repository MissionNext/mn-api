<?php
namespace MissionNext\Models\User;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use MissionNext\Facade\SecurityContext;
use MissionNext\Models\Application\Application;
use MissionNext\Models\EloquentObservable;
use MissionNext\Models\Job\Job;
use MissionNext\Models\ModelInterface;
use MissionNext\Models\ModelObservable;
use MissionNext\Models\Observers\UserObserver;
use MissionNext\Models\ProfileInterface;
use MissionNext\Models\Role\Role as RoleModel;
use MissionNext\Models\Field\Candidate as CandidateField;
use MissionNext\Models\Field\Organization as OrganizationField;
use MissionNext\Models\Field\Agency as AgencyField;
use MissionNext\Models\Role\Role;
use MissionNext\Models\Subscription\GlobalSubscription;
use MissionNext\Models\Subscription\Subscription;
use MissionNext\Repos\RepositoryContainerInterface;
use MissionNext\Repos\User\UserRepository;
use MissionNext\Repos\User\UserRepositoryInterface;

class User extends ModelObservable implements UserInterface, RemindableInterface, ProfileInterface
{
    const STATUS_PENDING_APPROVAL = 1;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('password');

    protected $guarded = array('id', 'password');

    protected $fillable = array('username', 'email', 'is_active', 'status');

    protected $userRole;

    /**
     * @param $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;

    }

    /**
     * @param $password
     *
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @param $isActive
     *
     * @return $this
     */
    public function setIsActive($isActive)
    {
        $this->is_active = (boolean)$isActive;

        return $this;
    }

    /**
     * @param $statusCode
     *
     * @return $this
     */
    public function setStatus($statusCode)
    {
        $this->status = intval($statusCode);

        return $this;
    }


    public function setRole(Role $role)
    {
        $this->onCreated(function ($user) use ($role) {
            /** @var $user User */
            $user->roles()->sync([$role->id]);
        });

        return $this;
    }

    /**
     * @param Application $application
     *
     * @throws \MissionNext\Api\Exceptions\ModelObservableException
     */
    public function setActiveOnApps(Application $application)
    {
         $this->onCreated(function($user) use ($application){
             /** @var $user User */
             $appIds = array_diff(Application::all()->lists('id'), [$application->id()]);

             foreach($appIds as $id){
                 $user->appsStatuses()->attach($id, ['is_active' => false]);
             }

             $user->appsStatuses()->attach($application->id(), ['is_active' => true]);
         });
    }

    /**
     * @return UserRepository
     */
    public function getRepo()
    {
        $repoContainer = App::make(RepositoryContainerInterface::class);

        return $repoContainer[UserRepositoryInterface::KEY];
    }

    public function addApp(Application $app)
    {
        $this->onSaved(function ($user) use ($app) {
            /** @var $user User */
            if (!$user->hasApp($app)) {
                $user->apps()->attach($app->id);

                return true;
            }

            return false;
        });

        $this->onCreated(function ($user) use ($app) {
            /** @var $user User */
                $user->apps()->attach($app->id);
        });

        return $this;
    }

    public function setUserPassword($value)
    {

        $this->attributes['password'] = Hash::make($value);
    }

    public function setPasswordAttribute($value)
    {

        $this->attributes['password'] = Hash::make($value);
    }

    public function getUserRolesViewAttribute() {

        $roles = $this->roles()->get();

        $strOut = '';
        foreach ($roles as $role) {
            $strOut .= '<p>'.$role->role.'</p>';
        }

        return $strOut;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {

        return $this->belongsToMany(RoleModel::class, 'user_roles', 'user_id', 'role_id');
    }



    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function apps()
    {

        return $this->belongsToMany(Application::class, 'user_apps', 'user_id', 'app_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function candidateFields()
    {

        return $this->belongsToMany(CandidateField::class, 'candidate_profile', 'user_id', 'field_id')->withPivot('value', 'dictionary_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function organizationFields()
    {

        return $this->belongsToMany(OrganizationField::class, 'organization_profile', 'user_id', 'field_id')->withPivot('value');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function agencyFields()
    {

        return $this->belongsToMany(AgencyField::class, 'agency_profile', 'user_id', 'field_id')->withPivot('value');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function jobs()
    {

       return $this->hasMany(Job::class, 'organization_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function appData()
    {

        return $this->apps()->get();
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    /**
     * Find out if user has a specific role
     *
     * $return boolean
     */
    public function hasRole($check)
    {

        return in_array($check, array_fetch($this->roles->toArray(), 'role'));
    }

    /**
     * @param Application $application
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function appStatus(Application $application)
    {

        return $this->belongsToMany(Application::class, 'user_apps_status', 'user_id', 'app_id')->withPivot('is_active')
                ->wherePivot('app_id', $application->id());
    }

    /**
     * @param Application $application
     *
     * @return boolean
     */
    public function isActiveInApp(Application $application)
    {

        return $this->appStatus($application)->firstOrFail()->pivot->is_active;
    }

    /**
     *
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function appsStatuses()
    {

        return $this->belongsToMany(Application::class, 'user_apps_status', 'user_id', 'app_id')->withPivot('is_active');
    }

    /**
     * @return string
     */
    public function role()
    {
        if(!$this->userRole){
            $this->userRole = $this->roles()->first()->role;
        }

        return $this->userRole;
    }

    /**
     * @param Application $app
     *
     * @return bool
     */
    public function hasApp(Application $app)
    {

        return in_array($app->id, array_fetch($this->apps->toArray(), 'id'));
    }

    /**
     * @return array
     */
    public function appIds()
    {

        return $this->apps()->get()->lists("id");
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken(){}

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value){}

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName(){}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {

        return $this->hasMany(Subscription::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function globalSubscription()
    {

        return $this->hasOne(GlobalSubscription::class, 'user_id', 'id');
    }

    /**
     * @param $userId
     *
     * @return Collection
     */
    public function transactions($userId)
    {
        $user = static::findOrFail($userId);
        $transactions = new Collection();

        $user->subscriptions()->getEager()->each(function(Subscription $subscription) use($transactions){
            $subTrans = $subscription->transactions()->getEager();
            if ($subTrans->count()){
                $subTrans->each(function($transaction) use ($transactions){
                    $transactions->add($transaction);
                });
            }
        });
        $transactions->sort(function($trans1, $trans2){
            dd($trans1->created_at, $trans2->created_at);

        });
//        $transactions = $transactions->toBase();
//
//        $transactions = $transactions->sortBy(function($transaction)
//        {
//            return $transaction->created_at;
//        });

        return $transactions;
    }

}