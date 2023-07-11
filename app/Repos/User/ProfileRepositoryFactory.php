<?php
namespace App\Repos\User;

use App\Models\DataModel\BaseDataModel;
use App\Repos\RepoContainerAware;
use App\Repos\RepositoryContainer;

class ProfileRepositoryFactory implements RepoContainerAware
{
    const KEY = 'profile_repo';

    private  $repoContainer;

    /**
     * @param RepositoryContainer $container
     *
     * @return $this
     */
    public function setRepoContainer(RepositoryContainer $container)
    {
        $this->repoContainer = $container;

        return $this;
    }

    /**
     * @return RepositoryContainer
     */
    public function repoContainer()
    {

        return $this->repoContainer;
    }

    /**
     * @return UserRepository|JobRepository
     */
    public function profileRepository()
    {

        return $this->repoContainer()->securityContext()->role() === BaseDataModel::JOB
                ? $this->repoContainer()[JobRepositoryInterface::KEY]
                : $this->repoContainer()[UserRepositoryInterface::KEY];
    }

}
