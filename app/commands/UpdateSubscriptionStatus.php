<?php
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use MissionNext\Repos\RepositoryContainerInterface;
use Symfony\Component\Console\Helper\ProgressHelper;
use MissionNext\Models\Configs\GlobalConfig;
use MissionNext\Models\Subscription\Subscription;

class UpdateSubscriptionStatus extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'subscription:update-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update subscriptions statuses if in grace or expired period ';

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
        $gracePeriod = (new GlobalConfig())->gracePeriod();

        $subscriptions = Subscription::where('status', '<>', Subscription::STATUS_CLOSED)
                                     ->where('status', '<>', Subscription::STATUS_EXPIRED)
                                     ->get();
        $subscriptions->each(function($subscription) use ($gracePeriod){
            /** @var $subscription Subscription */
            if ($subscription->days_left > $gracePeriod){
                $subscription->status = Subscription::STATUS_EXPIRED;
                $subscription->save();
            }

            if ($subscription->days_left > 0 && $subscription->days_left < $gracePeriod){
                $subscription->status = Subscription::STATUS_GRACE;
                $subscription->save();
            }

        });

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