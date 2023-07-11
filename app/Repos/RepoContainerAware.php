<?php


namespace App\Repos;


interface RepoContainerAware
{
    public  function  setRepoContainer(RepositoryContainer $container);
}
