<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use MissionNext\Repos\RepositoryContainerInterface;
use Symfony\Component\Console\Helper\ProgressHelper;

class ProfileUpdateCache extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'profile:update-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        /** @var  $repoContainer \MissionNext\Repos\RepositoryContainer */
        $repoContainer = $this->getLaravel()->make(RepositoryContainerInterface::class);

        /** @var  $profileRepo  \MissionNext\Repos\User\ProfileRepositoryFactory */
        $profileRepo = $repoContainer[\MissionNext\Repos\User\ProfileRepositoryFactory::KEY];

        /** @var  $securityContext \MissionNext\Api\Auth\SecurityContext */
        $securityContext = \MissionNext\Facade\SecurityContext::getFacadeRoot();

        $helper = $this->getHelperSet();
        /** @var  $progress */
        $progress = $helper->get('progress');
        $progress->setFormat(ProgressHelper::FORMAT_NORMAL);
        $this->output->setDecorated(true);
        $this->info("Profile update ...");
        $users = \MissionNext\Models\User\User::all();
        $jobs = \MissionNext\Models\Job\Job::all();
        $progCount = $users->count() + $jobs->count();

        $progress->start($this->output, $progCount);
        //$progress->setRedrawFrequency(100);

        foreach ($users as $user) {
            $securityContext->getToken()->setRoles([$user->role()]);
            $repoContainer->setSecurityContext($securityContext);
            $profileRepo->setRepoContainer($repoContainer);
            $profileRepo->profileRepository()->addUserCachedData($user);

            $progress->advance();
        }


        foreach ($jobs as $job) {
            $securityContext->getToken()->setRoles([$job->role()]);
            $repoContainer->setSecurityContext($securityContext);
            $profileRepo->setRepoContainer($repoContainer);
            $profileRepo->profileRepository()->addUserCachedData($job);

            $progress->advance();
        }

        $progress->finish();

        $this->comment("Update Successful");

    }

//	/**
//	 * Get the console command arguments.
//	 *
//	 * @return array
//	 */
//	protected function getArguments()
//	{
//		return array(
//			array('example', InputArgument::REQUIRED, 'An example argument.'),
//		);
//	}

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return array(
            array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
        );
    }

}
