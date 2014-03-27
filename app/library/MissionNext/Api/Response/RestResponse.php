<?php

namespace MissionNext\Api\Response;


use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Contracts\ArrayableInterface;
use MissionNext\Api\Exceptions\ResponseDataException;
use Illuminate\Database\Eloquent\Model;

class RestResponse extends JsonResponse
{
    /**
     * @param array $data
     *
     * @return RestData
     *
     * @throws \MissionNext\Api\Exceptions\ResponseDataException
     */
    protected function getResponseData($data = [])
    {
        if ($data instanceof Relation) {

            throw new ResponseDataException($data);
        }

        $rawData = $data;
        if ($data instanceof Collection) {
            $rawData = ["list" => $data->toArray()];
        } elseif ($data instanceof Model) {
            $rawData = $data->toArray();
        }

        return RestData::setData($rawData);
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