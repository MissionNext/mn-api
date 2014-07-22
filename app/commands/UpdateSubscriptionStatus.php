<?php
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use MissionNext\Repos\RepositoryContainerInterface;
use Symfony\Component\Console\Helper\ProgressHelper;
use MissionNext\Models\Configs\GlobalConfig;
use MissionNext\Models\Subscription\Subscription;
use MissionNext\Models\User\User;
use MissionNext\Models\DataModel\BaseDataModel;
use Carbon\Carbon;

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
                                     ->get();

        $subscriptions->each(function($subscription) use ($gracePeriod){

           if  ( ( $subscription->status === Subscription::STATUS_EXPIRED || $subscription->status === Subscription::STATUS_GRACE  )
               && $subscription->price == 0
               )  {

                   $subscription->end_date = $subscription->is_recurrent ? Carbon::now()->addMonth()
                                                                         : Carbon::now()->addYear();

                   $subscription->status = Subscription::STATUS_ACTIVE;
                   $subscription->save();
           }else {

               $absDaysLeft = abs($subscription->days_left);
               /** @var $subscription Subscription */
               if ($subscription->days_left < 0) {
                   if ($absDaysLeft < $gracePeriod) {
                       $subscription->status = Subscription::STATUS_GRACE;
                   } elseif ($absDaysLeft > $gracePeriod) {
                       $subscription->status = Subscription::STATUS_EXPIRED;

                   }
                   $subscription->save();
               }
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