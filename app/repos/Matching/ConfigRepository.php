<?php

namespace MissionNext\Repos\Matching;

use MissionNext\Models\Application\Application as AppModel;
use MissionNext\Models\Field\Candidate as CandidateFieldModel;

/**
 * Class ConfigRepository
 * @package MissionNext\Repos\Matching
 */
class ConfigRepository extends AbstractConfigRepository
{
    /**
     * @param array $configs
     *
     * @return \MissionNext\Models\Matching\Config
     */
    public function insert(array $configs)
    {
        if (count($configs)) {
            $typeRole = $this->sec_context->role();
            $insert = array_map(function ($config) use ($typeRole) {

                return
                    array(
                        "matching_type" => $config["matching_type"],
                        "weight" => $config["weight"],
                        $typeRole . "_field_id" => $config["matching_field_id"],
                        "main_field_id" => $config["main_field_id"],
                        "app_id" => $this->sec_context->getApp()->id,
                    );

            }, $configs);
            $this->getModel()->insert($insert);
        }

        return $this->getModel();
    }

} 