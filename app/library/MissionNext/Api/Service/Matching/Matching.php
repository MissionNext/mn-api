<?php

namespace MissionNext\Api\Service\Matching;


use MissionNext\Api\Auth\SecurityContext;
use MissionNext\Api\Auth\Token;
use MissionNext\Api\Exceptions\SecurityContextException;
use MissionNext\Api\Service\Matching\Type\EqualMatching;
use MissionNext\Api\Service\Matching\Type\GreaterMatching;
use MissionNext\Api\Service\Matching\Type\GreaterOrEqualMatching;
use MissionNext\Api\Service\Matching\Type\LessMatching;
use MissionNext\Api\Service\Matching\Type\LessOrEqualMatching;
use MissionNext\Api\Service\Matching\Type\LikeMatching;
use MissionNext\Filter\RouteSecurityFilter;
use MissionNext\Models\Field\FieldType;
use MissionNext\Models\Matching\Config;
use MissionNext\Repos\Field\Field;
use MissionNext\Api\Service\Matching\Data\Matching as MatchingData;

abstract class Matching
{

    const NO_PREFERENCE_SYMBOL = "(!)";
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

    private $selectFieldTypes = [FieldType::SELECT, FieldType::SELECT_MULTIPLE, FieldType::CHECKBOX, FieldType::RADIO];

    protected $matchData, $matchAgainstData, $matchConfig;

    /**
     * @param $type
     *
     * @return array
     *
     * @throws \MissionNext\Api\Exceptions\SecurityContextException
     */
    public  function selectFieldsOfType($type)
    {
        if (!RouteSecurityFilter::isAllowedRole($type)) {

            throw new SecurityContextException("'$type' role doesn't exists", SecurityContextException::ON_SET_ROLE);
        }

        $fieldModelName = Field::currentFieldModelName( (new SecurityContext())->setToken((new Token())->setRoles([$type]) ) );

        return
            array_fetch((new $fieldModelName)->whereIn("type",
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
                                            ->setMatchingValues($matchingValues);

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
        $maxMatching = 0;
        $this->matchConfig->each(function ($c) use (&$maxMatching) {
            $maxMatching += $c->weight;
        });

        foreach ($data as &$job) {
            $job['matching_percentage'] = 0;
            foreach ($job['profileData'] as $key=>&$prof) {
                //  var_dump($prof);
                if (isset($prof['matches']) && $prof['matches']) {
                    $job['matching_percentage'] += $prof['weight'];
                } elseif (!isset($prof['matches'])) {
                    //dd($key);
                    //@TODO job field not in matching config
                    $prof = ["job_value" => $prof, "candidate_value" => null];
                }
            }
            $job['matching_percentage'] = round(($job['matching_percentage'] / $maxMatching) * 100);
        }

        return array_values($data);
    }





    abstract public function matchResults();

} 