<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Admin\IUserService;
use App\Helpers\ApiResponse;
use App\Services\Admin\ProjectService;
use App\Services\StripeService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * @var IUserService
     */
    protected $userService;

    /**
     * UserController constructor.
     * @param IUserService $userService
     */
    public function __construct(IUserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Get users list
     * @return array
     */
    public function list()
    {
        try {
            $users = $this->userService->list();

            return ApiResponse::success('Success', $users);
        } catch (\Exception $e) {
            report($e);

            return ApiResponse::error('001', 'Error getting users, try again later');
        }
    }

    /**
     * Get user by Id
     * @param $id
     * @return array
     */
    public function getById($id)
    {
        try {
            $user = $this->userService->getById($id);
            return ApiResponse::success('Success', $user);
        } catch (\Exception $e) {
            report($e);

            return ApiResponse::error('001', 'Error getting user, try again later');
        }
    }

    public function delete($id, ProjectService $projectService, StripeService $stripeService)
    {
        try {
            $user = $this->userService->getById($id);

            DB::beginTransaction();
                // Delete user projects/remove user from projects
                if (count($user->projects)) {
                    $projectService->deleteUserProjects($user);
                }

                // Delete user billing data from Stripe
                if ($user->stripe_id) {
                    $stripeService->deleteCustomer($user);
                }

                // Delete user from database
                $this->userService->deleteUser($id);
            DB::commit();

            return ApiResponse::success('User deleted successfully', []);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);

            return ApiResponse::error('001', 'Error deleting user, try again later');
        }
    }

    public function search(Request $request)
    {
        try {
            $users = $this->userService->search($request->data);
            return ApiResponse::success('Success', $users);
        } catch(\Exception $e) {
            report($e);

            return ApiResponse::error('001', 'User search failed, try again later');
        }
    }
}
