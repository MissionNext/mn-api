<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class CheckUserProfiles extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'profile:check-completed-profile';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Check user\'s completed that finished.';

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
	    $roles = [
	        'candidate',
            'organization',
            'agency'
        ];

        ini_set('memory_limit', '1024M');

        /** @var  $repoContainer \MissionNext\Repos\RepositoryContainer */
        $repoContainer = $this->getLaravel()->make(\MissionNext\Repos\RepositoryContainerInterface::class);

        /** @var  $securityContext \MissionNext\Api\Auth\SecurityContext */
        $securityContext = \MissionNext\Facade\SecurityContext::getFacadeRoot();

        $helper = $this->getHelperSet();
        /** @var  $progress */
        $progress = $helper->get('progress');
        $progress->setFormat(\Symfony\Component\Console\Helper\ProgressHelper::FORMAT_NORMAL);
        $this->output->setDecorated(true);
        $this->info("Check profiles ...");

        $apps = \MissionNext\Models\Application\Application::get();
        $forms = [];
        foreach ($apps as $app) {
            foreach ($roles as $role) {
                $securityContext->getToken()->setRoles([$role]);
                $repoContainer->setSecurityContext($securityContext);
                $forms[$app->id][$role] = $this->getProfileForm($app, $repoContainer);
            }
        }

        print("\n");
        print_r($forms);
        print("\n");

        $this->comment("Check finished.");
	}

	private function getProfileForm($app, $repoContainer) {

        /** @var  $dm AppDataModel */
        $dm = $app->DM();

        $forms = $dm->forms()->whereSymbolKey('profile')->get();
        print("\n");
        print()
        print("\n");
        /** @var  $form AppForm */
        $form = $forms->first();

        if (!$form || !$form->fields()->count()) {

            return new RestResponse(null);
        }
        /** @var  $formRepo FormRepository */
        $formRepo = $repoContainer[\MissionNext\Repos\Form\FormRepositoryInterface::KEY];


        return $formRepo->structuredGroupFields($form);
    }

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
