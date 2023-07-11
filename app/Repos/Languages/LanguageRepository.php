<?php
/**
 * Created by PhpStorm.
 * User: xeon
 * Date: 04.06.14
 * Time: 13:13
 */

namespace App\Repos\Languages;

use App\Repos\AbstractRepository;
use App\Models\Language\LanguageModel;

class LanguageRepository extends AbstractRepository implements LanguageRepositoryInterface {

    protected $modelClassName = LanguageModel::class;

    /**
     * @return LanguageModel
     */
    public function getModel()
    {

        return $this->model;
    }

}
