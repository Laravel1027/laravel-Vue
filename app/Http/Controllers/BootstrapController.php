<?php

namespace App\Http\Controllers;

use App\Contracts\IBootstrapService;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class BootstrapController extends Controller
{
    /**
     * @var IBootstrapService
     */
    protected $bootstrapService;

    public function __construct(IBootstrapService $bootstrapService)
    {
        $this->bootstrapService = $bootstrapService;
    }

    /**
     * @return array
     */
    public function bootstrap()
    {
        $user = \Auth::user();
        try {
            $result = $this->bootstrapService->bootstrap($user);
            return ApiResponse::success('Success', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error connecting to server');
        }
    }

    /**
     * Change project listing type for user
     * @param Request $request
     * @return mixed
     */
    public function changeProjectsListingType(Request $request)
    {
        $user = \Auth::user();
        try {
            $result = $this->bootstrapService->changeProjectsListingType($user, $request->type);
            return ApiResponse::success('Success', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error connecting to server');
        }
    }

    /**
     * Get user recent data for project create
     * @return array
     */
    public function getRecentDatas()
    {
        $user = \Auth::user();
        try {
            $result = $this->bootstrapService->getRecentDatas($user);
            return ApiResponse::success('Success', $result);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error getting your recent data');
        }
    }

    public function disableAutocompleteData(Request $request)
    {
        $user = \Auth::user();
        try {
            $this->bootstrapService->disableAutocompleteData($user, $request->all());
            return ApiResponse::success('Success');
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error connecting to server');
        }
    }

    /**
     * Get user active subscription and plan details
     * @return array
     */
    public function getActiveSubscription()
    {
        $user = \Auth::user();

        if (isSubscribed($user)) {
            try {
                $result = $this->bootstrapService->getActiveSubscription($user);
                return ApiResponse::success('Success', $result);
            } catch (\Exception $e) {
                report($e);
                return ApiResponse::error('001', 'Error getting active plan details');
            }
        }

        return ApiResponse::success('Success', []);
    }
}
