<?php
use MissionNext\Facade\SecurityContext as FS;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Field\FieldType;

/**
 * Class FieldControllerTest
 * @see Api\Field\Controller;
 */
class FieldControllerTest extends TestCase
{

    /** @see Api\Field\Controller::getIndex */
   public function testCandidateGetIndex()
   {
      $candidate = BaseDataModel::CANDIDATE;

      $this->setRole($candidate);
      $response =  $this->call('GET', $candidate.'/field');
      $responseData = $response->getData();

      $this->assertGreaterThan(2, count($responseData->data->list));
      $this->assertTrue((bool)$responseData->status);
   }

    /** @see Api\Field\Controller::getIndex */
    public function testOrganizationGetIndex()
    {
        $organization = BaseDataModel::ORGANIZATION;

        $this->setRole($organization);
        $response =  $this->call('GET', $organization.'/field');
        $responseData = $response->getData();

        $this->assertGreaterThan(2, count($responseData->data->list));
        $this->assertTrue((bool)$responseData->status);
    }

    /** @see Api\Field\Controller::getIndex */
    public function testAgencyGetIndex()
    {
        $agency = BaseDataModel::AGENCY;

        $this->setRole($agency);
        $response =  $this->call('GET', $agency.'/field');
        $responseData = $response->getData();

        $this->assertGreaterThan(2, count($responseData->data->list));
        $this->assertTrue((bool)$responseData->status);
    }

    /** @see Api\Field\Controller::postIndex */
    public function testAgencyPostIndex()
    {
        $agency = BaseDataModel::AGENCY;
        $this->setRole($agency);
        $paramams = [];

        $paramams["fields"][] = [
                            "symbol_key" => "new_date",
                            "name" => "New date",
                            "type" => FieldType::DATE,
                            "default_value" => '',
                            "choices"=> '',
                           ];

        $paramams["fields"][] = [
                             "symbol_key" => "my_movies",
                             "name" => "My Movies",
                             "type" => FieldType::CHECKBOX,
                             "default_value" => "terminator,bamby",
                             "choices" => "terminator,lolo,bamby",
                           ];

        $response =  $this->call('POST', $agency.'/field', $paramams);

        $responseData = $response->getData();

        $this->assertEquals(count($paramams["fields"]), count($responseData->data->list));
        $this->assertEquals("new_date", $responseData->data->list[0]->symbol_key);
        $this->assertEquals("my_movies", $responseData->data->list[1]->symbol_key);
        $this->assertTrue((bool)$responseData->status);
    }

    /** @see Api\Field\Controller::postIndex */
    public function testOrganizationPostIndex()
    {
        $organization = BaseDataModel::ORGANIZATION;
        $this->setRole($organization);
        $paramams = [];

        $paramams["fields"][] = [
            "symbol_key" => "state",
            "name" => "State",
            "type" => FieldType::CHECKBOX,
            "default_value" => 'in_progress',
            "choices"=> 'in_progress,ready',
        ];

        $response =  $this->call('POST', $organization.'/field', $paramams);

        $responseData = $response->getData();

        $this->assertEquals(count($paramams["fields"]), count($responseData->data->list));
        $this->assertEquals("state", $responseData->data->list[0]->symbol_key);
        $this->assertTrue((bool)$responseData->status);
    }

    /** @see Api\Field\Controller::postIndex */
    public function testCandidatePostIndex()
    {
        $candidate = BaseDataModel::CANDIDATE;
        $this->setRole($candidate);
        $paramams = [];

        $paramams["fields"][] = [
            "symbol_key" => "is_married",
            "name" => "Is married",
            "type" => FieldType::BOOLEAN,
            "default_value" => true,
            "choices"=> '',
        ];

        $response =  $this->call('POST', $candidate.'/field', $paramams);

        $responseData = $response->getData();

        $this->assertEquals(count($paramams["fields"]), count($responseData->data->list));
        $this->assertEquals("is_married", $responseData->data->list[0]->symbol_key);
        $this->assertTrue((bool)$responseData->status);
    }
} 