<?php


namespace MissionNext\Repos\User;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use MissionNext\Api\Auth\ISecurityContextAware;
use MissionNext\Models\Application\Application;
use MissionNext\Models\CacheData\UserCachedData;
use MissionNext\Models\CacheData\UserCachedDataTrans;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Language\LanguageModel;
use MissionNext\Models\ProfileInterface;
use MissionNext\Repos\AbstractRepository;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Repos\CachedData\UserCachedRepositoryInterface;
use MissionNext\Repos\Field\Field;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Collection;
use MissionNext\Models\Profile;
use MissionNext\Models\Field\FieldType;
use MissionNext\Api\Auth\SecurityContext as SecContext;

abstract class AbstractUserRepository extends AbstractRepository implements ISecurityContextAware
{

    /** @var \MissionNext\Api\Auth\SecurityContext */
    protected $securityContext;

    protected $userCacheTable = 'user_cached_profile';

    /** @var  LanguageModel */
    protected $languageModel;


    public function setSecurityContext(SecContext $securityContext)
    {
        $this->securityContext = $securityContext;

        return $this;
    }

    /**
     * @param BelongsToMany $query
     * @param null $role
     *
     * @return Profile
     */
    public function profileStructure(BelongsToMany $query, $role = null)
    {

        $profile = new Profile();
        $profile->setModel($query->getParent());
        $fields = $query->get();
       //  dd(get_class($this->getModel()), $this->getModel()->name);
        foreach($this->getModel()->toArray() as $prop=>$val){
            $profile->$prop = $val;
        }
        $profile->role = $role;
        $profile->profileData = new \stdClass();

        $fields->each(function ($field) use ($profile) {
            $key = $field->symbol_key;
            if (isset($profile->profileData->$key)) {
                $profile->profileData->$key = array_merge($profile->profileData->$key, [$field->pivot->value]);
            } else {
                $profile->profileData->$key = FieldType::isMultiple($field->type) ? [$field->pivot->value] : $field->pivot->value;
            }
        });

        return $profile;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return Profile
     */
    public function profileStructureTrans(\Illuminate\Database\Eloquent\Builder $query)
    {
        $profile = new Profile();
        $profile->setModel($this->getModel());
        //dd('sdf',$profile->toArray());
        $fields = $query->get();

        $this->setUsersBaseData($profile, $this->getModel());
        $profile->profileData = new \stdClass();

        $fields->each(function ($field) use ($profile) {
            $strategy = $this->profileDataStructureStrategy($this->languageModel);
            $strategy($profile, $field);
        });

        return $profile;
    }

    /**
     * @param LanguageModel $languageModel
     *
     * @return callable
     */
    protected function profileDataStructureStrategy(LanguageModel $languageModel)
    {

        return !$languageModel->id
            ?
              function(Profile $profile, $field){
                  $key = $field->symbol_key;
                  if (isset($profile->profileData->$key)) {
                      $profile->profileData->$key = array_merge($profile->profileData->$key, [$field->trans_value]);
                  } else {
                      $profile->profileData->$key = $field->value;
                      if (FieldType::isMultiple($field->type)){
                          $profile->profileData->$key = [$field->trans_value];
                      } elseif (FieldType::hasDictionary($field->type)) {
                          $profile->profileData->$key = $field->trans_value;
                      }
                  }
              }
            :
            function(Profile $profile, $field){
                $key = $field->symbol_key;
                if (isset($profile->profileData->$key)) {
                    $profile->profileData->$key = array_merge($profile->profileData->$key, [$field->value => $field->trans_value]);
                } else {
                    $profile->profileData->$key = $field->value;
                    if (FieldType::hasDictionary($field->type)) {
                        $profile->profileData->$key = [$field->value => $field->trans_value];
                    }
                }
            };
    }


    /**
     * @param ProfileInterface $user
     * @return Profile
     * @throws \MissionNext\Api\Exceptions\SecurityContextException
     */
    public function profileData(ProfileInterface $user)
    {
        $this->model = $user;
        $role = $this->securityContext->role(); // or this->model->roleType
        $userName = $role === BaseDataModel::JOB ? BaseDataModel::JOB : "user";

        return $this->profileStructure($user->belongsToMany(Field::currentFieldModelName($this->securityContext), $this->securityContext->role() . '_profile', $userName.'_id', 'field_id')->withPivot('value'), $role);

    }

    public function insertUserCachedData(ProfileInterface $user)
    {
        $d = $this->profileData($user);

        $userCachedData = new UserCachedData();
        $userCachedData->setUser($user)
                       ->setProfileData($d);

        $userCachedData->save();
    }

    public function updateUserCachedData(ProfileInterface $user)
    {
        $d = $this->profileDataTrans($user, new LanguageModel());

        /** @var  $userCachedData UserCachedData */
        $userCachedData = (new UserCachedData())->find($user->id) ? : new UserCachedData();

        $userCachedData->setProfileData($d)
                       ->setUser($user)
                       ->save();

        foreach(LanguageModel::all() as $languageModel){
            $dt = $this->profileDataTrans($user, $languageModel);
            $userCachedDataTrans = (new UserCachedDataTrans())
                                   ->whereLangId($languageModel->id)
                                   ->whereId($user->id)
                                   ->get()->first()
                                   ? : new UserCachedDataTrans();

            $userCachedDataTrans
                ->setProfileData($dt)
                ->setUser($user)
                ->setLang($languageModel)
                ->save();
        }

    }

    public function addUserCachedData(ProfileInterface $user)
    {

        $d = $this->profileDataTrans($user, new LanguageModel());
        /** @var  $userCachedData UserCachedData */
        $userCachedData = (new UserCachedData())->find($user->id) ? : new UserCachedData();

        $userCachedData->setProfileData($d)
            ->setUser($user)
            ->save();

        foreach(LanguageModel::all() as $i=>$languageModel){
            $dt = $this->profileDataTrans($user, $languageModel);

            $userCachedDataTrans = (new UserCachedDataTrans())
                ->whereLangId($languageModel->id)
                ->whereId($user->id)
                ->get()->first()
                ? : new UserCachedDataTrans();

            $userCachedDataTrans
                ->setProfileData($dt)
                ->setLang($languageModel);

            if (!$userCachedDataTrans->id) { //insert
                $userCachedDataTrans
                    ->setUser($user)
                    ->save();
            } else {
                $userCachedDataTrans
                    ->where("lang_id", "=", $languageModel->id)
                    ->where("id", "=", $user->id)
                    ->update([
                        'data' => $userCachedDataTrans->data,
                        'id' => $user->id,
                        'lang_id' => $userCachedDataTrans->lang_id
                    ]);
            }
        }

    }

    abstract  public function addApp(Application $app);

    /**
     * @param ProfileInterface $user
     * @param LanguageModel $languageModel
     *
     * @return Profile
     */
    abstract  public function profileDataTrans(ProfileInterface $user, LanguageModel $languageModel);

    abstract  public function setUsersBaseData(Profile $profile, ProfileInterface $data);




} 