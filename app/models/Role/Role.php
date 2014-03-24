<?php
namespace MissionNext\Models\Role;

use Illuminate\Database\Eloquent\Model as Eloquent;
use MissionNext\Models\ModelInterface;

class Role extends Eloquent implements ModelInterface
{

    const ROLE_CANDIDATE = 1;
    const ROLE_ORGANIZATION = 2;
    const ROLE_AGENCY = 3;

    public $timestamps = false;

    protected $table = 'roles';

    protected $fillable = array('name','role');

    /**
     * Get users with a certain role
     */
    public function users()
    {
        return $this->belongsToMany(static::prefix_ns.'\User\User', 'user_roles');
    }
} 