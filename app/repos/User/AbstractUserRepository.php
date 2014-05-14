<?php


namespace MissionNext\Repos\User;


use Illuminate\Support\Facades\DB;
use MissionNext\Api\Auth\ISecurityContextAware;
use MissionNext\Models\CacheData\UserCachedData;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\ProfileInterface;
use MissionNext\Repos\AbstractRepository;
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


    public function setSecurityContext(SecContext $securityContext)
    {
        $this->securityContext = $securityContext;

        return $this;
    }
    /**
     * @param $id
     * @param array $columns
     * @return ProfileInterface
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function find($id, $columns = array('*'))
    {
        $this->model = parent::find($id, $columns);

        if (!$this->model) {

            throw new NotFoundHttpException(class_basename($this->modelClassName)." with id $id not found");
        }

        return $this->model;
    }

    /**
     * @param Collection $fields
     * @param null $role
     *
     * @return Profile
     */
    public function profileStructure(Collection $fields, $role = null)
    {

        $profile = new Profile();
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
     * @param ProfileInterface $user
     * @return Profile
     * @throws \MissionNext\Api\Exceptions\SecurityContextException
     */
    public function profileData(ProfileInterface $user)
    {
        $this->model = $user;
        $role = $this->securityContext->role(); // or this->model->roleType
        $userName = $role === BaseDataModel::JOB ? BaseDataModel::JOB : "user";

        return $this->profileStructure($user->belongsToMany(Field::currentFieldModelName($this->securityContext), $this->securityContext->role() . '_profile', $userName.'_id', 'field_id')->withPivot('value')->get(), $role);

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
        $d = $this->profileData($user);
        /** @var  $userCachedData UserCachedData */
        $userCachedData = (new UserCachedData())->find($user->id);

        $userCachedData->setProfileData($d)
                       ->save();
    }

} 