<?php

namespace App\Http\Controllers;

use App\Contracts\ITeamService;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Laravel\Spark\Http\Requests\Settings\Teams\RemoveTeamMemberRequest;

class TeamController extends Controller
{
    protected $teamService;

    public function __construct(ITeamService $teamService)
    {
        $this->teamService = $teamService;
    }

    /**
     * Invite team member
     * @param Request $request
     * @return array
     */
    public function inviteMembers(Request $request)
    {
        try {
            $members = $this->teamService->inviteMembers($request->all());
            return ApiResponse::success('Invitation has been sent successfully', $members);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Something went wrong, please try again later');
        }
    }

    /**
     * Delete team member
     * @param RemoveTeamMemberRequest $request
     * @param $team
     * @param $member
     * @return array
     */
    public function deleteMember(RemoveTeamMemberRequest $request, $team, $member)
    {
        try {
            $this->teamService->deleteMember($team, $member);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Something went wrong, please try again later');
        }
    }
}
