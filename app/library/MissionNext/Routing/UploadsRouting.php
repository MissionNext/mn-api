<?php


namespace MissionNext\Routing;

use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use MissionNext\Controllers\Uploads\FileController;

class UploadsRouting {

    public function __construct(Application $App)
    {
       /** @var  $router Router */
       $router =  $App->make('router');

       $router->controller('/uploads/{fileName}', FileController::class) ;
    }
} 