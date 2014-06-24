<?php


namespace MissionNext\Repos\Subscription;

use Illuminate\Database\Eloquent\Model;
use MissionNext\Models\Subscription\SubConfig;
use MissionNext\Repos\AbstractRepository;

class SubConfigRepository extends AbstractRepository implements SubConfigRepositoryInterface
{
    protected $modelClassName = SubConfig::class;
    /**
     * @return SubConfig
     */
    public function getModel()
    {

        return $this->model;
    }

} 