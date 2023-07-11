<?php

namespace App\Models\My;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    public const STATUS_PENDING_APPROVAL = 1;

    protected $userRole;

    protected $userRoleId;

    protected $fillable = [
        'username',
        'email',
        'password',
        'is_active',
        'status',
        'last_login'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @return hasMany
     */
    public function app(): hasMany
    {
        return $this->hasMany(UserApp::class);
    }


    public function setEmail($email)
    {
        return $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setUsername($username)
    {
        return $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setPassword($password)
    {
        return $this->password = $password;
    }

    public function setIsActive($isActive): bool
    {
        return $this->is_active = (boolean)$isActive;
    }

    public function IsActive(): bool
    {
        return $this->is_active;
    }

    public function setStatus($statusCode): int
    {
        return $this->status = intval($statusCode);
    }


    public function setRole(Roles $role): User
    {
        $this->onCreated(function ($user) use ($role) {
            $user->roles()->sync([$role->id]);
        });
        return $this;
    }

    public function setLastLogin()
    {
        return $this->last_login = date('Y-m-d H:i:s');
    }

    public function setActiveOnApps(Applications $application)
    {
        $this->onCreated(function ($user) use ($application) {
            $appIds = array_diff(Applications::all()->pluck('id'), [$application->id()]);
            foreach ($appIds as $id) {
                $user->appsStatuses()->attach($id, ['is_active' => true]);
            }
            $user->appsStatuses()->attach($application->id(), ['is_active' => true]);
        });
    }


    public function addApp(Applications $app)
    {
        $this->onSaved(function ($user) use ($app) {
            if (!$user->hasApp($app)) {
                $user->apps()->attach($app->id);

                return true;
            }
            return false;
        });

        $this->onCreated(function ($user) use ($app) {
            $user->apps()->attach($app->id);
        });
        return $this;
    }

    public function removeApp(Applications $app)
    {
        $this->apps()->detach($app->id);
    }

    public function setUserPassword($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    public function getUserRolesViewAttribute(): string
    {
        $roles = $this->roles()->get();
        $strOut = '';
        foreach ($roles as $role) {
            $strOut .= '<p>' . $role->role . '</p>';
        }
        return $strOut;
    }

    /**
     * @return BelongsToMany
     */
    public function roles():BelongsToMany
    {
        return $this->belongsToMany(Roles::class, 'user_roles', 'user_id', 'role_id');
    }


    /**
     * @return BelongsToMany
     */
    public function apps():BelongsToMany
    {
        return $this->belongsToMany(Applications::class, 'user_apps', 'user_id', 'app_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function candidateFields()
    {
        return $this->belongsToMany(CandidateField::class, 'candidate_profile', 'user_id', 'field_id')->withPivot('value', 'dictionary_id');
    }

    /**
     * @return BelongsToMany
     */
    public function organizationFields():BelongsToMany
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

        return in_array($check, array_pluck($this->roles->toArray(), 'role'));
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
        $model = $this->appStatus($application)->first();
        return !(is_null($model)) ? $model->pivot->is_active : false;
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
        if (!$this->userRole) {
            $this->userRole = $this->roles()->first()->role;
        }
        return $this->userRole;
    }

    /**
     * @return mixed
     */
    public function roleId()
    {
        if (!$this->userRoleId) {
            $this->userRoleId = $this->roles()->first()->id;
        }

        return $this->userRoleId;
    }

    /**
     * @param Application $app
     *
     * @return bool
     */
    public function hasApp(Application $app)
    {

        return in_array($app->id, array_pluck($this->apps->toArray(), 'id'));
    }

    /**
     * @return array
     */
    public function appIds()
    {

        return $this->apps()->get()->pluck("id");
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     * @return void
     */
    public function setRememberToken($value)
    {
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
    }

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
     * @return array
     */
    public function transactions($userId)
    {
        $user = static::findOrFail($userId);
        $transactions = new Collection();

        $user->subscriptions()->getEager()->each(function (Subscription $subscription) use ($transactions) {
            $subTrans = $subscription->transactions()->getEager();
            if ($subTrans->count()) {
                $subTrans->each(function ($transaction) use ($transactions) {
                    $transactions->contains($transaction) ?: $transactions->add($transaction);
                });
            }
        });

        $transactions = $transactions->sortBy('created_at');

        return array_values($transactions->toArray());
    }

    public function delete()
    {

        $ch = curl_init(Config::get('app.wp_remote_url') . '/wp-admin/admin-ajax.php?action=user_deleting_function&username=' . trim($this->getUsername()) . '&secret=' . md5('Secret key for deleting wp user.'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = $this->curl_exec_follow($ch);

        if (FALSE === $data) {
            Session::flash('warning', 'CURL error. Error #' . curl_errno($ch) . ' with message: ' . curl_error($ch));
            curl_close($ch);

            return false;
        }

        if (10 == $data || "User does not exist" == substr($data, 0, strlen($data) - 1)) {
            $user_id = $this->id;
            $user_role = $this->userRole;

            Favorite::where('user_id', '=', $user_id)
                ->orWhere('target_id', $user_id)
                ->where('target_type', $user_role)->delete();

            Inquire::where('candidate_id', '=', $user_id)->delete();

            Notes::where('user_id', $user_id)
                ->where('user_type', $user_role)
                ->orWhere('for_user_id', $user_id)->delete();

            FolderApps::where('user_id', $user_id)
                ->where('user_type', $user_role)
                ->orWhere('for_user_id', $user_id)->delete();

            Affiliate::where('affiliate_approver', $user_id)
                ->orWhere('affiliate_requester')->delete();

            SearchData::where('user_id', $user_id)->delete();

            $organization_flag = false;
            if (count($this->roles)) {
                foreach ($this->roles as $role) {
                    if (2 == $role->id) {
                        $organization_flag = true;
                        break;
                    }
                }
            }

            if ($organization_flag && count($this->jobs)) {
                foreach ($this->jobs as $job) {
                    Notes::where('user_id', $job->id)
                        ->where('user_type', BaseDataModel::JOB)->delete();

                    Favorite::where('target_id', $job->id)
                        ->where('target_type', BaseDataModel::JOB)->delete();

                    Inquire::where('job_id', $job->id)->delete();

                    FolderApps::where('user_id', $job->id)
                        ->where('user_type', BaseDataModel::JOB)->delete();

                    Results::where('user_id', $job->id)->orWhere('for_user_id', $job->id)->delete();

                    $this->logger('user', 'delete', "User $user_id deleted job with id $job->id");

                    $job->delete();
                }
            }

            UserCachedData::table($this->role())->where('id', $this->id)->delete();
            UserCachedDataTrans::table($this->role())->where('id', $this->id)->delete();
            DB::table($this->role() . '_profile')->where('user_id', $this->id)->delete();

            Results::where('user_id', $user_id)->orWhere('for_user_id', $user_id)->delete();

            $subs = $this->subscriptions()->get();

            $forceClose = true;
            foreach ($subs as $initSub) {
                $authorizeCode = null;
                if ($initSub->is_recurrent && $initSub->authorize_id && $forceClose) {
                    $subscription = Subscription::where('authorize_id', '=', $initSub->authorize_id)
                        ->where('status', '<>', Subscription::STATUS_CLOSED)
                        ->get();
                    $response = $this->paymentGateway->getRecurringBilling()->cancelSubscription($initSub->authorize_id);
                    //$authorizeCode = strip_tags($response->xpath('messages/message')[0]->code->asXML());
                    $authorizeCode = $response->getMessageCode();
                    //code -  E00003, I00001- successful,  I00002 - has already been cancelled
                    $initSub->status = Subscription::STATUS_CLOSED;
                    $initSub->save();
                } else {
                    $initSub->status = Subscription::STATUS_CLOSED;
                    $initSub->save();
                }
            }

            return parent::delete();
        }

        Session::flash('warning', 'Wordpress error. ' . substr($data, 0, strlen($data) - 1));
        curl_close($ch);

        return false;
    }

    function curl_exec_follow($ch, &$maxredirect = null)
    {

        // we emulate a browser here since some websites detect
        // us as a bot and don't let us do our job
        $user_agent = "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5)" .
            " Gecko/20041107 Firefox/1.0";
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);

        $mr = $maxredirect === null ? 5 : intval($maxredirect);

        if (filter_var(ini_get('open_basedir'), FILTER_VALIDATE_BOOLEAN) === false
            && filter_var(ini_get('safe_mode'), FILTER_VALIDATE_BOOLEAN) === false
        ) {

            curl_setopt($ch, CURLOPT_MAXREDIRS, $mr);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        } else {


            if ($mr > 0) {
                $original_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
                $newurl = $original_url;

                $rch = curl_copy_handle($ch);

                curl_setopt($rch, CURLOPT_HEADER, true);
                curl_setopt($rch, CURLOPT_NOBODY, true);
                curl_setopt($rch, CURLOPT_FORBID_REUSE, false);
                do {
                    curl_setopt($rch, CURLOPT_URL, $newurl);
                    $header = curl_exec($rch);
                    if (curl_errno($rch)) {
                        $code = 0;
                    } else {
                        $code = curl_getinfo($rch, CURLINFO_HTTP_CODE);
                        if ($code == 301 || $code == 302) {
                            preg_match('/Location:(.*?)\n/i', $header, $matches);
                            $newurl = trim(array_pop($matches));

                            // if no scheme is present then the new url is a
                            // relative path and thus needs some extra care
                            if (!preg_match("/^https?:/i", $newurl)) {
                                $newurl = $original_url . $newurl;
                            }
                        } else {
                            $code = 0;
                        }
                    }
                } while ($code && --$mr);

                curl_close($rch);

                if (!$mr) {
                    if ($maxredirect === null)
                        trigger_error('Too many redirects.', E_USER_WARNING);
                    else
                        $maxredirect = 0;

                    return false;
                }
                curl_setopt($ch, CURLOPT_URL, $newurl);
            }
        }
        return curl_exec($ch);
    }

    private function logger($log_type, $action, $message)
    {
        $view_log = new Logger('View Logs');
        $view_log->pushHandler(new StreamHandler(storage_path() . '/logs/custom_logs/' . $log_type . '/' . date('Y-m-d') . '.txt', Logger::INFO));
        $view_log->info('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
        $view_log->info('Action: ' . $action);
        $view_log->addInfo($message);
        $view_log->info('=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=');
    }
}
