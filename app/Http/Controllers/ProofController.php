<?php

namespace App\Http\Controllers;

use App\Contracts\IUnreadCommentsService;
use Illuminate\Http\Request;
use App\Project;
use App\ProjectFile;
use App\Proof;
use App\Helpers\ApiResponse;

use App\Contracts\IRevisionService;
use App\Contracts\IProofService;
use App\Http\Requests\ProofRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProofController extends Controller
{
    private $proofService;
    private $unreadCommentsService;

    public function __construct(IProofService $proofService, IUnreadCommentsService $unreadCommentsService)
    {
        $this->proofService = $proofService;
        $this->unreadCommentsService = $unreadCommentsService;
    }

    public function saveProofState(ProofRequest $request)
    {
        $data = $request->all();
        $proof = $this->proofService->saveProofState($data);
        if ($proof) {
            return ApiResponse::success('Resource saved successfully', [$proof]);
        } else {
            return ApiResponse::error('001', 'Proof not found');
        }
    }

    /**
     * Create new issue
     * @param Request $request
     * @return array
     */
    public function createIssue(Request $request)
    {
        $validatedData = $request->validate([
            'proof_id' => 'sometimes',
            'description' => 'required',
            'group' => 'sometimes',
            'label' => 'sometimes',
            'id' => 'sometimes',
        ]);

        $data = $request->all();

        try {
            $issue = $this->proofService->createIssue($data);
            if ($issue) {
                return ApiResponse::success('Issue created successfully', [$issue]);
            } else {
                return ApiResponse::error('001', 'Error saving the issue');
            }
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error saving the issue');
        }
    }
    public function deleteIssue($issue_id)
    {
        if ($issue_id > 0) {
            $issue = $this->proofService->deleteIssue($issue_id);
            if ($issue) {
                return ApiResponse::success('Resource deleted successfully', $issue);
            }
            return ApiResponse::error('001', 'Error deleting the issue');
        }
    }

    public function deleteComment($comment_id)
    {
        if ($comment_id > 0) {
            try {
                $comment = $this->proofService->deleteComment($comment_id);
                return ApiResponse::success('Comment deleted successfully', [$comment]);
            } catch (\Exception $e) {
                report($e);
                return ApiResponse::error('001', 'Error deleting the comment, please try again later');
            }
        }
    }

    /**
     * Add new comment
     * @param Request $request
     * @return array
     */
    public function addComment(Request $request)
    {
        $validatedData = $request->validate([
            'id' => 'sometimes:integer',
            'issue_id' => 'required|integer',
            'description' => 'required',
        ]);
        try {
            $data = $request->all();
            $comment = $this->proofService->addComment($data);
            if ($comment) {
                return ApiResponse::success('Comment created successfully', [$comment]);
            } else {
                return ApiResponse::error('001', 'Error saving the comment');
            }
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error saving the comment');
        }
    }

    public function changeProofStatus($proof_id, $status)
    {
        if ($proof_id > 0 && $status != '') {
            $proof = $this->proofService->changeProofStatus($proof_id, $status);
            if ($proof) {
                return ApiResponse::success('Proof status updated successfully', [$proof]);
            }
        }
        return ApiResponse::error('001', 'Error updating proof status');
    }

    /**
     * Change issue status
     * @param $issue_id
     * @param $status
     * @param $project_type
     * @return array
     */
    public function changeIssueStatus($issue_id, $status, $project_type)
    {
        if ($issue_id > 0 && $status != '') {
            $issue = $this->proofService->changeIssueStatus($issue_id, $status, $project_type);
            if ($issue) {
                return ApiResponse::success('Issue status updated successfully', [$issue]);
            }
        }
        return ApiResponse::error('001', 'Error updating issue status');
    }

    /**
     * Delete Proof
     * @param $proofId
     * @return array
     */
    public function deleteProof($proofId) {
        try {
            DB::beginTransaction();
                $result = $this->proofService->deleteProof($proofId);
            DB::commit();

            return ApiResponse::success('Proof deleted successfully', []);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return ApiResponse::error('001', 'Error deleting proof');
        }
    }

    public function deleteIssueUnreadComments($issueId) {
        $condition = [
            'user_id' => Auth::user()->id,
            'issue_id' => $issueId
        ];
        try {
            $this->unreadCommentsService->delete($condition);
            return ApiResponse::success('Comment marked as read', []);
        } catch (\Exception $e) {
            report($e);
            return ApiResponse::error('001', 'Error mark comments as read');
        }
    }
}