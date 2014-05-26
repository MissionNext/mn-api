<?php
namespace MissionNext\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class AdminBaseController extends Controller {

    protected $request;

    public function __construct(Request $request) {
        $this->request = $request;
    }

} 