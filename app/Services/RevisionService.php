<?php

namespace App\Services;

use App\Project;
use App\ProjectFile;
use App\Proof;
use App\Revision;
use App\Contracts\IProofService;;
use Illuminate\Support\Facades\Auth;

use App\Contracts\IRevisionService;

class RevisionService implements IRevisionService
{
    private $proofService;

    public function __construct(IProofService $proofService)
    {
        $this->proofService = $proofService;
    }

    public function getRevisionById($revision_id)
    {
        if ($revision_id > 0) {
            $revision = Revision::getRevisionById($revision_id);
            return $revision;
        }
        return null;
    }
    public function getLastRevision($project_id)
    {
        $revision = Revision::getLastRevision($project_id);
        return $revision;
    }
    public function create($project_id)
    {
        if ($project_id > 0) {
            $project = Project::findOrFail($project_id);
            if ($project) {
                $data['project'] = $project;
                $revision = Revision::store($data);
                if ($revision != null) {
                    return $revision;
                }
            }
            return null;
        }
    }

    /**
     * Remove last created revision
     * @param $project_id
     * @return bool|null
     * @throws \Exception
     */
    public function remove($project_id) {
        if ($project_id > 0) {
            $project = Project::findOrFail($project_id);
            if ($project) {
                $revision = Revision::remove($project_id);
                return $revision;
            }
        }
        return null;
    }
    public function getRevisionByProject($project_id)
    {
        if ($project_id > 0) {
            $project = Project::where('id', $project_id)->where('user_id', \Auth::user()->id)->firstOrFail();
            if ($project != null) {
                $revisions = Revision::where('project_id', $project_id)->with('proofs')->get();
                return $revisions;
            }
        }
        return null;
    }

    public function changeRevisionStatus($revision_id, $status)
    {
        if ($revision_id > 0) {
            $revision = Revision::find($revision_id);
            if ($revision != null) {
                $revision->status_revision = $status;
                if ($status == 'approved') {
                    if (count($revision->proofs) > 0) {
                        foreach ($revision->proofs as $key => $proof) {
                            $this->proofService->changeProofStatus($proof->id, $status);
                        }
                    }

                }
                $project = $revision->project;
                if ($project) {
                    $project->status = $status;
                    $project->save();
                }

                $revision->save();
                return $revision;
            }
        }
        return null;
    }
}
