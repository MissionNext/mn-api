<?php
namespace MissionNext\Provider;


use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use MissionNext\Api\Auth\SecurityContext;
use MissionNext\Events;
use MissionNext\Models\Form\FormGroup;
use MissionNext\Repos\Matching\ConfigRepositoryInterface;

class EventProvider extends ServiceProvider
{
    public function register()
    {
        Event::listen(Events::FILTER_ROLE, function(SecurityContext $securityContext){
//           App::make(ConfigRepositoryInterface::class)->setModel(new FormGroup());
        });
    }
} 