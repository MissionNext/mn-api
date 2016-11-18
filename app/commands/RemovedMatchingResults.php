<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class RemovedMatchingResults extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'matching:remove-ghost-results';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'This command remove all matching results for non exist job and organization.';

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
        // Delete matching results for unexisting jobs
		\MissionNext\Models\Matching\Results::where('user_type', 'job')->whereRaw('user_id NOT IN (SELECT id FROM jobs)')->delete();
        \MissionNext\Models\Matching\Results::where('for_user_type', 'job')->whereRaw('for_user_id NOT IN (SELECT id FROM jobs)')->delete();

        // Delete matching results for users
        \MissionNext\Models\Matching\Results::whereRaw('user_id NOT IN (SELECT id FROM users)')->delete();
        \MissionNext\Models\Matching\Results::whereRaw('for_user_id NOT IN (SELECT id FROM users)')->delete();
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
		);
	}

}
