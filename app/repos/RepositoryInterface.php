<?php

namespace MissionNext\Repos;


use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface {

    public function create(array $attributes);

    public function all($columns = array('*'));

    public function find($id, $columns = array('*'));

    public function destroy($ids);

    /**
     * @return Model
     */
    public function getModel();

}