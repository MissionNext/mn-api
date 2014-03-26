<?php

namespace MissionNext\Api\Response;


use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Contracts\ArrayableInterface;
use StdClass;

class RestResponse extends JsonResponse
{
    /**
     * @param array $data
     *
     * @return StdClass
     */
    protected function getResponseData($data = [])
    {
        $rawData = $data instanceof ArrayableInterface ? $data->toArray() : $data;

        return
            count($data) > 1
                ? RestData::setData(['list' => $rawData])
                : RestData::setData($rawData);

    }

    /**
     * @param array $data
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|void
     */
    public function setData($data = [])
    {
        parent::setData($this->getResponseData($data));
    }

    /**
     * @param \Exception $e
     *
     * @return $this
     */
    public function setErrorData(\Exception $e)
    {
        $responseData = RestData::setData(["error" =>
            ["message" => $e->getMessage(), "code" => $e->getCode(), "type" => (new \ReflectionObject($e))->getShortName()]
        ], 0);
        parent::setData($responseData);

        return $this;
    }

} 