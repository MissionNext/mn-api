<?php
namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class AdminGroupProvider {
    /**
     * The Eloquent group model.
     *
     * @var string
     */
    protected $model = \App\Models\Admin\AdminGroupModel::class;

    /**
     * Create a new Eloquent Group provider.
     *
     * @param  string  $model
     * @return void
     */
    public function __construct($model = null)
    {
        if (isset($model))
        {
            $this->model = $model;
        }
    }

    /**
     * Find the group by ID.
     *
     * @param  int  $id
     */
    public function findById($id)
    {
        $model = $this->createModel();

        if ( ! $group = $model->newQuery()->find($id))
        {
            throw new GroupNotFoundException("A group could not be found with ID [$id].");
        }

        return $group;
    }

    /**
     * Find the group by name.
     *
     * @param  string  $name
     */
    public function findByName($name)
    {
        $model = $this->createModel();

        if ( ! $group = $model->newQuery()->where('name', '=', $name)->first())
        {
            throw new GroupNotFoundException("A group could not be found with the name [$name].");
        }

        return $group;
    }

    /**
     * Returns all groups.
     *
     * @return array  $groups
     */
    public function findAll()
    {
        $model = $this->createModel();

        return $model->newQuery()->get()->all();
    }

    /**
     * Creates a group.
     *
     * @param  array  $attributes
     */
    public function create(array $attributes)
    {
        $group = $this->createModel();
        $group->fill($attributes);
        $group->save();
        return $group;
    }

    /**
     * Create a new instance of the model.
     *
     * @return Model
     */
    public function createModel()
    {
        $class = '\\'.ltrim($this->model, '\\');

        return new $class;
    }

    /**
     * Sets a new model class name to be used at
     * runtime.
     *
     * @param  string  $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

}
