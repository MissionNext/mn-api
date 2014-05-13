<?php

namespace MissionNext\Controllers\Api\Affiliate;


use Illuminate\Support\Facades\DB;
use MissionNext\Api\Exceptions\AffiliateException;
use MissionNext\Api\Exceptions\ValidationException;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\Affiliate\Affiliate;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Role\Role;
use MissionNext\Models\User\User;
use MissionNext\Validators\Affiliate as AfValidator;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AffiliateController extends BaseController
{

    public function getAffiliates($affiliateId, $affiliateType)
    {
        $baseQuery = Affiliate::select("status","affiliate_approver_type", "organization_cached_profile.data as organization_profile",
            "agency_cached_profile.data as agency_profile", "affiliate_approver", "affiliate_requester" )
            ->leftJoin("organization_cached_profile", function($join){
                $join->on("organization_cached_profile.id", "=", "affiliate_approver")
                    ->orOn("organization_cached_profile.id","=","affiliate_requester");

            })
            ->leftJoin("agency_cached_profile", function($join){
                $join->on("agency_cached_profile.id", "=", "affiliate_approver")
                    ->orOn("agency_cached_profile.id","=","affiliate_requester");

            });
        if ($affiliateType === Affiliate::TYPE_ANY ){

            $query = $baseQuery
                                ->where("affiliate_requester", '=', $affiliateId)
                                ->orWhere("affiliate_approver", '=', $affiliateId);

        } else {

            $query = $baseQuery->where("affiliate_" . $affiliateType, '=', $affiliateId);
        }

        $res = $query->get()->each(function(&$el){
           $el->organization_profile = json_decode($el->organization_profile);
           $el->organization_profile->affiliate_type = $el->affiliate_approver == $el->organization_profile->id ? Affiliate::TYPE_APPROVER : Affiliate::TYPE_REQUESTER;
           $el->agency_profile = json_decode($el->agency_profile);
           $el->agency_profile->affiliate_type = $el->affiliate_approver == $el->agency_profile->id ? Affiliate::TYPE_APPROVER : Affiliate::TYPE_REQUESTER;

        });

        return new RestResponse($res);
    }

    public function getIndex($requesterId, $approverId)
    {

        return new RestResponse(Affiliate::where("affiliate_approver", '=', $approverId)
            ->where("affiliate_requester", '=', $requesterId)->first());
    }

    public function postIndex($requesterId, $approverId)
    {
        $affiliateData = $this->affiliateCheck($requesterId, $approverId);
        $affiliateData["status"] = Affiliate::STATUS_PENDING;

        return new RestResponse(Affiliate::create($affiliateData));
    }

    public function postApprove($requesterId, $approverId)
    {
       $affiliate =  Affiliate::where("affiliate_approver", '=', $approverId)
            ->where("affiliate_requester", '=', $requesterId)
            ->where("status", '<>', Affiliate::STATUS_APPROVED)
            ->firstOrFail();

          $affiliate->status = Affiliate::STATUS_APPROVED;
          $affiliate->save();

       return new RestResponse( $affiliate );
    }

    public function postCancel($requesterId, $approverId)
    {
        $affiliate =  Affiliate::where("affiliate_approver", '=', $approverId)
            ->where("affiliate_requester", '=', $requesterId)
            ->where("status", '<>', Affiliate::STATUS_CANCELLED)
            ->firstOrFail();

            $affiliate->status = Affiliate::STATUS_CANCELLED;
            $affiliate->save();

        return new RestResponse( $affiliate );
    }

    public function postPend($requesterId, $approverId)
    {
        $affiliate =  Affiliate::where("affiliate_approver", '=', $approverId)
            ->where("affiliate_requester", '=', $requesterId)
            ->where("status", '<>', Affiliate::STATUS_PENDING)
            ->firstOrFail();

        $affiliate->status = Affiliate::STATUS_PENDING;
        $affiliate->save();

        return new RestResponse( $affiliate );
    }

    private function affiliateCheck($requesterId, $approverId)
    {
        if ($requesterId == $approverId){

            throw new AffiliateException("Requester and Approver are same users", AffiliateException::ON_REQUEST);
        }
        $affiliate = Affiliate::where("affiliate_approver", '=', $requesterId)
            ->where("affiliate_requester", '=', $approverId)
            ->first();

        if ($affiliate){

            throw new AffiliateException("Affiliate requester {$affiliate->affiliate_requester} is in {$affiliate->status} status to approver {$affiliate->affiliate_approver}");
        }

        $requester = $this->getRequester($requesterId);

        $approver = $this->getApprover($approverId);

        $requesterRole = $requester->roles()->first()->role;
        $approverRole = $approver->roles()->first()->role;
        $affiliateRoles = [BaseDataModel::AGENCY, BaseDataModel::ORGANIZATION];
        if (!in_array($requesterRole, $affiliateRoles) || !in_array($approverRole, $affiliateRoles)){

            throw new AffiliateException("Affiliate users must have one of the roles ".implode(", ", $affiliateRoles), AffiliateException::ON_REQUEST);
        }

        if ($requesterRole === $approverRole){

            throw new AffiliateException("Affiliate users can't have same roles '$requesterRole , $approverRole' ", AffiliateException::ON_REQUEST);
        }

        return [
            "affiliate_approver" => $approverId,
            "affiliate_requester" => $requesterId,
            "affiliate_approver_type" => $approverRole,
        ];
    }

    /**
     * @param $requesterId
     * @return User
     */
    private function getRequester($requesterId)
    {

        return $this->userRepo()->find($requesterId);
    }
    /**
     * @param $approverId
     *
     * @return User
     */
    private function getApprover($approverId)
    {

        return $this->userRepo()->find($approverId);
    }

    /**
     * @param $approverId
     * @return Role
     */
    private function getApproverRole($approverId)
    {

        return $this->getApprover($approverId)->roles()->first();
    }
} 