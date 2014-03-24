<?php


namespace MissionNext\Api\Response;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;

final class RestData
{

    public  $data,
            $status = 1;

    /** @var RestData */
    private static $instance = null;

    private static $queryLog = false;

    private function __construct()
    {

    }

    private function set($data, $status)
    {
        $this->data = $data;
        $this->status = $status;
        if (static::$queryLog){

            $this->dbLog = DB::getQueryLog();
        }
    }

    /**
     * @param $data
     * @param int $status default 1
     * @return RestData
     */
    public static function setData($data, $status = 1)
    {
        static::$instance = static::$instance ? : new self();
        static::$instance->set($data, $status);

        return static::$instance;
    }

    /**
     * @return RestData
     */
    public static function withQueryLog()
    {
        static::$queryLog = App::environment('local') ? true : false;

        return static::$instance;
    }

} 