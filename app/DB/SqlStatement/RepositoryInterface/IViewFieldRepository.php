<?php

namespace App\DB\SqlStatement\RepositoryInterface;

use App\Models\DataModel\AppDataModel;

interface IViewFieldRepository
{
    public function deleteByDMSymbolKeys(AppDataModel $dm, $symbolKeys);
}
