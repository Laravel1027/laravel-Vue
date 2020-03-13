<?php

namespace App\Services\Admin;


use App\Contracts\Admin\IProjectService;
use App\Jobs\DeleteCommentFiles;
use App\Jobs\DeleteIssueFiles;
use App\Jobs\DeleteProjectFinalFiles;
use App\Jobs\DeleteProofFiles;
use App\Project;

class ProjectService implements IProjectService
{
    /**
     * @var Project
     */
    protected $model;

    /**
     * ProjectService constructor.
     * @param Project $model
     */
    public function __construct(Project $model)
    {
        $this->model = $model;
    }

    /**
     * @param $user
     * @return mixed
     */
    public function deleteUserProjects($user)
    {
        $finalFiles = [];
        $proofFiles = [];
        $issueFiles = [];
        $commentFiles = [];
        $projects = $user->projects()->with('revisions.proofs.issues.comments')->get();

        foreach ($projects as $project) {
            $role = $project->users()->where('id', $user->id)->pluck('role')[0];
            $finalFiles = array_merge($finalFiles, $project->finalFiles()->pluck('id')->toArray());

            if ($role == 'client' || $project->created_by == $user->id) {
                // Delete project data if user is Client on project or project is created by user
                if ($project->revisions) {
                    foreach ($project->revisions as $revision) {

                        if (count($revision->proofs)) {
                            foreach ($revision->proofs as $proof) {
                                $proofFiles[] = $proof->projectFiles->id;

                                if ($proof->issues) {
                                    foreach ($proof->issues as $issue) {
                                        if (count($issue->files)) {
                                            $issueFiles = array_merge($issueFiles, $issue->files()->pluck('id')->toArray());
                                        }

                                        if ($issue->comments) {
                                            foreach ($issue->comments as $comment) {
                                                if (count($comment->files)) {
                                                    $commentFiles = array_merge($commentFiles, $comment->files()->pluck('id')->toArray());
                                                }

                                                // Delete comment
                                                $comment->delete();
                                            }

                                        }

                                        // Delete issue
                                        $issue->delete();
                                    }
                                }

                                // Delete revision proofs
                                $proof->delete();
                            }

                        }

                        // Delete revision
                        $revision->delete();
                    }
                }

                //Remove project users from project
                foreach ($project->users as $user) {
                    $project->users()->detach($user->id);
                }

                //Delete Project final links
                if (count($project->finalLinks)) {
                    $project->finalLinks()->delete();
                }

                // Delete project
                $project->delete();

                // JOBS for deleting files from server
                if (count($finalFiles)) {
                    DeleteProjectFinalFiles::dispatch($finalFiles);
                }
                if (count($proofFiles)) {
                    DeleteProofFiles::dispatch($proofFiles);
                }

                if (count($issueFiles)) {
                    DeleteIssueFiles::dispatch($issueFiles);
                }

                if (count($commentFiles)) {
                    DeleteCommentFiles::dispatch($commentFiles);
                }
            } else {
                // Remove user from project
                $project->users()->detach($user->id);
            }

        }

        return true;
    }
}