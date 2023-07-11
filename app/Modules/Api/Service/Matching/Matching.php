<?php

namespace App\Modules\Api\Service\Matching;


use Illuminate\Support\Collection;
use App\Modules\Api\Auth\SecurityContext;
use App\Modules\Api\Auth\Token;
use App\Modules\Api\Exceptions\SecurityContextException;
use App\Modules\Api\Service\Matching\Type\EqualMatching;
use App\Modules\Api\Service\Matching\Type\GreaterMatching;
use App\Modules\Api\Service\Matching\Type\GreaterOrEqualMatching;
use App\Modules\Api\Service\Matching\Type\LessMatching;
use App\Modules\Api\Service\Matching\Type\LessOrEqualMatching;
use App\Modules\Api\Service\Matching\Type\LikeMatching;
use App\Modules\Api\Filter\RouteSecurityFilter;
use App\Models\Field\FieldType;
use App\Models\Matching\Config;
use App\Repos\Field\Field;
use App\Modules\Api\Service\Matching\Data\Matching as MatchingData;

abstract class Matching
{

    const NO_PREFERENCE_SYMBOL = "(!)";

    protected $matchingModel;

    protected $mainMatchingModel;

    protected $reverseMatching = false;

    /**
     * @param $matchData
     * @param $matchAgainstData
     * @param $matchConfig
     */
    public function __construct($matchData, $matchAgainstData, $matchConfig)
    {
        $this->matchData = $matchData;
        $this->matchAgainstData = $matchAgainstData;
        $this->matchConfig = $matchConfig;
    }

    private $selectFieldTypes = [FieldType::SELECT, FieldType::SELECT_MULTIPLE, FieldType::CHECKBOX, FieldType::RADIO, FieldType::RADIO_YES_NO, FieldType::MARITAL_STATUS];

    protected $matchData, $matchAgainstData, $matchConfig;

    /**
     * @param $type
     *
     * @return array
     *
     * @throws \App\Api\Exceptions\SecurityContextException
     */
    public  function selectFieldsOfType($type)
    {
        if (!RouteSecurityFilter::isAllowedRole($type)) {

            throw new SecurityContextException("'$type' role doesn't exists", SecurityContextException::ON_SET_ROLE);
        }

        $fieldModelName = Field::currentFieldModelName( (new SecurityContext())->setToken((new Token())->setRoles([$type]) ) );

        return
            array_pluck((new $fieldModelName)->whereIn("type",
                $this->selectFieldTypes)->get()->toArray(), 'symbol_key');
    }

    /**
     * @param $values
     *
     * @return bool
     */
    protected  function isNoPreference($values)
    {
        foreach ($values as $value) {
            if (starts_with($value, static::NO_PREFERENCE_SYMBOL)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $mainValues
     * @param $matchingValues
     * @param $matchingType
     * @return bool
     */
    protected function isMatches($mainValues, $matchingValues, $matchingType)
    {
        $matchingData = (new MatchingData())->setMainValues($mainValues)
            ->setMatchingValues($matchingValues)
            ->setReverseMatching($this->reverseMatching);

        switch($matchingType){
            case Config::MATCHING_EQUAL:
                $matchingData->setMatchingType(new EqualMatching());
                break;
            case Config::MATCHING_LESS:
                $matchingData->setMatchingType(new LessMatching());
                break;
            case Config::MATCHING_LESS_OR_EQUAL:
                $matchingData->setMatchingType(new LessOrEqualMatching());
                break;
            case Config::MATCHING_GREATER:
                $matchingData->setMatchingType(new GreaterMatching());
                break;
            case Config::MATCHING_GREATER_OR_EQUAL:
                $matchingData->setMatchingType(new GreaterOrEqualMatching());
                break;
            case Config::MATCHING_LIKE:
                $matchingData->setMatchingType(new LikeMatching());
                break;
            default:
                $matchingData->setMatchingType(new EqualMatching());

        }

        return $matchingData->isMatches();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected  function calculateMatchingPercentage(array $data)
    {

        foreach ($data as &$profileData) {

            $profileData['matching_percentage'] = 0;
            $maxMatching = 0;
            $mustMatchMultiplier = $profileData['multiplier'];

            if ($mustMatchMultiplier != 0) {
                if (isset($profileData['results'])) {

                    array_map   (function($c) use (&$maxMatching){

                        if ($c['weight'] < 5)
                            $maxMatching += $c['weight'];

                    }, $profileData['results']);

                    foreach ($profileData['results'] as $key=>&$prof) {
                        if ($prof['weight'] < 5) {
                            if (isset($prof['matches']) && $prof['matches'])
                                $profileData['matching_percentage'] += $prof['weight'];
                            elseif (!isset($prof['matches']) || !$prof['matches'])
                                $prof = [$this->matchingModel."_value" => $prof, $this->mainMatchingModel."_value" => null];
                        }
                    }
                }

                if ($maxMatching > 0)
                    $profileData['matching_percentage'] = round(($profileData['matching_percentage'] / $maxMatching) * 100);
                else
                    $profileData['matching_percentage'] = 0;
            } else {
                $profileData['matching_percentage'] = 0;
            }
        }

        $result = array_filter(array_values($data), function($d){
            return  $d['matching_percentage'] != 0;
        });

        return $result;
    }


    abstract public function matchResults();


    public function getIntersection($mainArray, $matchingArray)
    {
        $mainIntersectArray = $matchingIntersectArray = [];

        foreach ($mainArray as $item) {
            if (in_array($item, $matchingArray) || starts_with($item, static::NO_PREFERENCE_SYMBOL)) {
                $mainIntersectArray[] = $item;
                $matchingIntersectArray[] = $item;
            }
        }

        foreach ($matchingArray as $item){
            if (starts_with($item, static::NO_PREFERENCE_SYMBOL) && !in_array($item, $mainIntersectArray)){
                $matchingIntersectArray[] = $item;
                $mainIntersectArray[] = $item;
            }
        }

        return [
            $mainIntersectArray, $matchingIntersectArray
        ];
    }
}
