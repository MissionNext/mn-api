<?php
use MissionNext\Facade\SecurityContext as FS;
use MissionNext\Models\DataModel\BaseDataModel;

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

      FS::getInstance()->getToken()->setRoles([$candidate]);
      $response =  $this->call('GET', $candidate.'/field');
      $responseData = $response->getData();

      $this->assertGreaterThan(2, count($responseData->data->list));
      $this->assertTrue((bool)$responseData->status);
   }

    /** @see Api\Field\Controller::getIndex */
    public function testOrganizationGetIndex()
    {
        $organization = BaseDataModel::ORGANIZATION;

        FS::getInstance()->getToken()->setRoles([$organization]);
        $response =  $this->call('GET', $organization.'/field');
        $responseData = $response->getData();

        $this->assertGreaterThan(2, count($responseData->data->list));
        $this->assertTrue((bool)$responseData->status);
    }

    /** @see Api\Field\Controller::getIndex */
    public function testAgencyGetIndex()
    {
        $agency = BaseDataModel::AGENCY;

        FS::getInstance()->getToken()->setRoles([$agency]);
        $response =  $this->call('GET', $agency.'/field');
        $responseData = $response->getData();

        $this->assertGreaterThan(2, count($responseData->data->list));
        $this->assertTrue((bool)$responseData->status);
    }

    /** @see Api\Field\Controller::postIndex */
    public function testAgencyPostIndex()
    {
        $agency = BaseDataModel::AGENCY;

        FS::getInstance()->getToken()->setRoles([$agency]);
        $paramams = [];

        $paramams["fields"][] = [
                            "symbol_key" => "new_date",
                            "name" => "New date",
                            "type" => \MissionNext\Models\Field\FieldType::DATE,
                            "default_value" => '',
                            "choices"=> '',

                           ];

        $response =  $this->call('POST', $agency.'/field', $paramams);
        $responseData = $response->getData();

        $this->assertEquals(count($paramams["fields"]), count($responseData->data->list));
        $this->assertEquals(count($paramams["fields"]), count($responseData->data->list));
        $this->assertEquals("new_date", $responseData->data->list[0]->symbol_key);
        $this->assertTrue((bool)$responseData->status);
    }
} 