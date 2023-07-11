<?php

namespace App\Repos\Affiliate;

use App\Models\Affiliate\Affiliate;
use App\Repos\AbstractRepository;

class AffiliateRepository   extends AbstractRepository implements  AffiliateRepositoryInterface
{
    protected $modelClassName = Affiliate::class;

    /**
     * @return Affiliate
     */
    public function getModel()
    {

        return $this->model;
    }
}
