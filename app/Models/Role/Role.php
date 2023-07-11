<?php
namespace App\Models\Role;

use Illuminate\Database\Eloquent\Model as Eloquent;
use App\Models\DataModel\BaseDataModel;
use App\Models\ModelInterface;
use App\Models\User\User as UserModel;

class Role extends Eloquent implements ModelInterface
{

    const ROLE_CANDIDATE = 1;
    const ROLE_ORGANIZATION = 2;
    const ROLE_AGENCY = 3;

    public $timestamps = false;

    protected $table = 'roles';

    protected $fillable = ['name','role'];

    protected $appends = ['label'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {

        return $this->belongsToMany(UserModel::class, 'user_roles');
    }

    /**
     * @return string
     */
    public function getLabelAttribute()
    {

        return $this->attributes['label'] = BaseDataModel::label($this->role);
    }


}
