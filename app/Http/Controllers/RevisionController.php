<?php

namespace App\Http\Controllers;

use App\Contracts\IProjectService;
use App\Http\Requests\RevisionRequest;
use Illuminate\Http\Request;
use App\Project;
use App\ProjectFile;
use App\Proof;

use App\Contracts\IRevisionService;
use App\Contracts\IProofService;
use App\Helpers\ApiResponse;
use Illuminate\Support\Facades\Redirect;

class RevisionController extends Controller
{
    private $revisionService;
    private $projectService;

    public function __construct(IRevisionService $revisionService, IProjectService $projectService)
    {
        $this->revisionService = $revisionService;
        $this->projectService = $projectService;
    }

    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'project_id' => 'required|numeric',
        ]);
        $project_id = $request->project_id;

        if ($project_id > 0) {
            $revision = $this->revisionService->create($project_id);
            if ($revision) {
                return ApiResponse::success('Revision created successfully', [$revision]);
            }
        }
        return ApiResponse::error('001', 'Error creating revision. There is an open revision in this project already.');
    }

    /**
     * Remove last created revision
     * @param $project_id
     * @return array
     */
    public function remove($project_id) {
        if ($project_id > 0) {
            $revison = $this->revisionService->remove($project_id);
            if ($revison) {
                return ApiResponse::success('Revision deleted successfully', []);
            }
        }
        return ApiResponse::error('001', 'Error deleteing revision.');
    }

    public function getRevisionsByProject($project_id)
    {
        if ($project_id > 0) {
            $revisions = $this->revisionService->getRevisionByProject($project_id);
            if ($revisions) {
                return ApiResponse::success('', [$revisions]);
            }
        }
        return ApiResponse::error('001', 'Error getting project revisions');
    }

    public function changeRevisionStatus($revision_id, $status)
    {
        if ($project_id > 0 && $version > 0 && $status != '') {
            $revision = $this->revisionService->changeRevisionStatus($revision_id, $status);
            if ($revision) {
                return ApiResponse::success('Revision status updated successfully', [$revision]);
            }
        }
        return ApiResponse::error('001', 'Error updating revision status');
    }

    public function loadRevisionById($revision_id)
    {
        $revision = $this->revisionService->getRevisionById($revision_id);
        if ($revision) {
            $issues = $this->projectService->getIssues($revision);
            $result['revision'] = $revision;
            $result['total_issues'] = $issues['totalIssues'];
            $result['solved_issues'] = $issues['solvedIssues'];
            $result['percentage'] = $issues['percentage'];
            return ApiResponse::success('Revision loaded successfully', $result);
        }
        return ApiResponse::error('001', 'Error loading the revision');
    }
}
