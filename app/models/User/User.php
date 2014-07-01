<?php
namespace MissionNext\Models\User;

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
use MissionNext\Repos\RepositoryContainerInterface;
use MissionNext\Repos\User\UserRepository;
use MissionNext\Repos\User\UserRepositoryInterface;

class User extends ModelObservable implements UserInterface, RemindableInterface, ProfileInterface
{
    const STATUS_PENDING_APPROVAL = 'pending_approval',
          STATUS_ACCESS_DENIED = 'access_denied',
          STATUS_ACCESS_GRANTED = 'access_granted',
          STATUS_ACTIVE = 'active',
          STATUS_EXPIRED = 'expired';

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


    public function setRole(Role $role)
    {
        $this->onCreated(function ($user) use ($role) {
            /** @var $user User */
            $user->roles()->sync([$role->id]);
        });

        return $this;
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
     * @return string
     */
    public function role()
    {

        return $this->roles()->first()->role;
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

}