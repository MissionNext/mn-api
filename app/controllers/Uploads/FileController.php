<?php

namespace MissionNext\Controllers\Uploads;


use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\View;
use MissionNext\Routing\UploadsRouting;
/**
 * Class FileController
 * @package MissionNext\Controllers\Uploads
 * @see UploadsRouting
 */
class FileController extends Controller
{
    /**
     * @param $fileName
     *
     * @return Response
     */
    public function getIndex($fileName)
    {
        $fullFileName = public_path('uploads/'.$fileName);
        if (!file_exists($fullFileName)){

           return  View::make('uploads.404');
        }

        return new Response(file_get_contents($fullFileName), 200, ['Content-type' => 'application/pdf']);
    }
} 