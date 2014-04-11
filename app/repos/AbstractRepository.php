<?php
namespace MissionNext\Repos;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractRepository implements RepositoryInterface
{
    protected $modelClassName;
    /**
     * @var Model
     */
    protected $model;


    public function __construct()
    {
        $this->model = new $this->modelClassName;


    }

    public function create(array $attributes)
    {

        return call_user_func_array("{$this->modelClassName}::create", array($attributes));
    }

    /**
     * @param array $columns
     *
     * @return Collection
     */
    public function all($columns = array('*'))//@TODO Boot current field model
    {
        return call_user_func_array("{$this->modelClassName}::all", array($columns));
    }

    public function find($id, $columns = array('*'))
    {
        return call_user_func_array("{$this->modelClassName}::find", array($id, $columns));
    }

    public function destroy($ids)
    {
        return call_user_func_array("{$this->modelClassName}::destroy", array($ids));
    }

} 