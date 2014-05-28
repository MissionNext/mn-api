<?php

namespace MissionNext\Repos;


use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface {

    public function create(array $attributes);

    public function all($columns = array('*'));

    public function find($id, $columns = array('*'));

    public function with($string);

    public function destroy($ids);

    public  function  setRepoContainer(RepositoryContainer $container);

    /**
     * @return Model
     */
    public function getModel();

}