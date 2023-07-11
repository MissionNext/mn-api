<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 07.10.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Modules\Pub\Auth\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;

class FileController  extends Controller
{
    /**
     * @param $fileName
     *
     * @return Response
     */
    public function getIndex($fileName)
    {
        $fullFileName = app_path('storage/uploads/'.$fileName);
        if (!file_exists($fullFileName)){
            return  View::make('uploads.404');
        }

        return new Response(file_get_contents($fullFileName), 200, ['Content-type' => 'application/pdf']);
    }

    public function getFile($fileName)
    {
        $fullPath = app_path('/storage/uploads/'.$fileName);
        if (!file_exists($fullPath)) {
            return  View::make('uploads.404');
        }

        return \Illuminate\Support\Facades\Response::download($fullPath);
    }
}
