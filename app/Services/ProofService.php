<?php

namespace App\Services;

use App\Contracts\INotificationService;
use App\Contracts\IUnreadCommentsService;
use App\Events\CommentPosted;
use App\Events\IssuePosted;
use App\Events\IssueStatusChanged;
use App\Project;
use App\Proof;
use App\Issue;
use App\Comment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Contracts\IProofService;

class ProofService implements IProofService
{
    protected $notifications;
    protected $model;
    protected $fileService;
    protected $unreadCommentsService;

    /**
     * ProofService constructor.
     * @param INotificationService $notifications
     * @param IUnreadCommentsService $unreadCommentsService
     */
    public function __construct(INotificationService $notifications, IUnreadCommentsService $unreadCommentsService)
    {
        $this->notifications = $notifications;
        $this->unreadCommentsService = $unreadCommentsService;
        $this->model = new Proof();
        $this->fileService = new FileService();
    }

    /**
     * @param $project_file
     * @return Proof|null
     */
    public function createProof($project_file)
    {
        try {
            $proof = Proof::store($project_file);
            if ($proof != null) {
                return $proof;
            }
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @param $revision_id
     * @return null
     */
    public function getByRevisionId($revision_id)
    {
        $proofs = Proof::getByRevisionId($revision_id);
        return $proofs;
    }

    /**
     * @param $version
     * @return null
     */
    public function getByRevisionVersion($version)
    {
        $proofs = Proof::getByRevisionVersion($version);
        return $proofs;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function saveProofState($data)
    {
        $proof = Proof::find($data['id']);

        if ($proof) {
            $proof->canvas_data = $data['canvas_data'];
            $proof->save();
            return $proof;
        }

    }

    /**
     * @param $proof_id
     * @param $status
     * @return mixed
     */
    public function changeProofStatus($proof_id, $status)
    {
        if ($proof_id > 0) {
            if ($status != '') {
                $proof = Proof::find($proof_id);
                $proof->status = $status;
                $proof->save();
                if ($status == 'approved') {
                    $issues = Issue::where('proof_id', $proof->id)->get();
                    if (count($issues) > 0) {
                        foreach ($issues as $key => $issue) {
                           /*  if ($issue->status != $status) { */
                                $issue->status = 'done';
                                $issue->save();
                            /* } */
                        }
                    }
                }
                
                return $proof;
            }
        }
    }

    /**
     * Change issue status
     * @param $issue_id
     * @param $status
     * @param $project_type
     * @return mixed
     */
    public function changeIssueStatus($issue_id, $status, $project_type)
    {
        if ($issue_id > 0) {
            if ($status != '') {
                $issue = Issue::find($issue_id);
                $issue->status = $status;
                $issue->save();
                $left_issues = Issue::where('proof_id', $issue->proof_id)->where('id', '!=', $issue->id)->get();

                if ($project_type == 'website') {
                    $project = $issue->proof->revision->project;
                    $users = $project->users()->where('id', '<>', Auth::user()->id)->get();

                    //Get project freelancer
                    foreach ($users as $user) {
                        //Create notification for freelancer
                        $this->notifications->create($user, [
                            'icon' => 'fa fa-pencil-square-o',
                            'body' => 'Issue is Approved',
                            'type' => 'Approved Issue',
                            'action_text' => 'View Project',
                            'action_url' => 'proofer/' . $project->id . '/' . $issue->proof->revision->id,
                            'company' => $project->company,
                            'project' => $project->name,
                            'proofer' => ''
                        ]);

                        event(new IssueStatusChanged($user, $project, $issue));
                    }
                }

                $proof = $issue->proof;

                if(count($proof->issues()->where('status', 'todo')->get()) == 0){
                    if(count($proof->issues()->where('status', 'review')->get()) == 0){
                        return $this->changeProofStatus($issue->proof_id, 'approved');
                    }
                }

                return $issue;
            }
        }
    }

    /**
     * Create new issue
     * @param $data
     * @return Issue|Issue[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function createIssue($data)
    {
        $project_type = '';
        if (array_key_exists('project_type', $data)) {
            $project_type = $data['project_type'];
            $data = array_except($data, ['project_type']);
        }

        if (array_key_exists('id', $data)) {
            $issue = Issue::with('files')->with('user')->findOrFail($data['id']);
        } else {
            $issue = new Issue();
            $issue->group = $data['group'];
            $issue->label = $data['label'];
            $issue->proof_id = $data['proof_id'];
        }
        $issue->description = $data['description'];
        $issue->status = 'todo';
        $issue->user_id = Auth::user()->id;
        if (array_key_exists('owner_type', $data)) {
            $issue->owner_type = $data['owner_type'];
        }
        $issue->save();
        $issue = Issue::with('files')->with('user')->find($issue->id);

        $project = $issue->proof->revision->project;
        $project->updated_at = Carbon::now();
        $project->save();

        $users = $project->users()->where('id', '<>', Auth::user()->id)->get();
        foreach ($users as $user) {
            //Set viewed by user to 0 for project users
            $project->users()->updateExistingPivot($user->id, ['viewed_by_user' => 0]);
        }

        if ($project_type == 'website') {
            //Set proof status to issue
            $issue->proof()->update(['status' => 'issue']);

            //Get project freelancer
            foreach ($users as $user) {
                //Set viewed by user to 0 for project users
                $project->users()->updateExistingPivot($user->id, ['viewed_by_user' => 0]);

                //Create notification for project users
                $this->notifications->create($user, [
                    'icon' => 'fa fa-pencil-square-o',
                    'body' => 'New Issue is posted',
                    'type' => 'New Issue',
                    'action_text' => 'View Project',
                    'action_url' => 'proofer/' . $project->id . '/' . $issue->proof->revision->id,
                    'company' => $project->company,
                    'project' => $project->name,
                    'proofer' => ''
                ]);

                event(new IssuePosted($user, $project, $issue));
            }
        }
        $issue['proof_status'] = $issue->proof->status;

        return $issue;
    }

    /**
     * Add new comment
     * @param $data
     * @return Comment|Comment[]|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function addComment($data)
    {
        if (array_key_exists('id', $data)) {
            if ($data['id'] > 0) {
                $comment = Comment::with('files')->with('user')->findOrFail($data['id']);
            }
        } else {
            $comment = new Comment();
        }
        $comment->issue_id = $data['issue_id'];
        $comment->description = $data['description'];
        $comment->user_id = Auth::user()->id;
        $comment->save();

        $project = Project::findOrFail($data['project_id']);
        $project->updated_at = Carbon::now();
        $project->save();

        $users = $project->users()->where('user_id', '<>', Auth::user()->id)->get();
        $data['comment_id'] = $comment->id;

        $new_comment = Comment::with('issue')->find($comment->id);

        foreach ($users as $user) {
            //Set viewed by user to 0 for project users
            $project->users()->updateExistingPivot($user->id, ['viewed_by_user' => 0]);

            //Create user notifications
            $this->notifications->create($user, [
                'icon' => 'fa-comments-o',
                'body' => Auth::user()->name . ' wrote a new comment',
                'type' => 'New Comment',
                'action_text' => 'View Comment',
                'action_url' => 'proofer/' . $project->id . '/' . $data['revision_id'] . '#' . $comment->issue->group . '-' . $comment->id,
                'company' => $project->company,
                'project' => $project->name,
                'proofer' => ''
            ]);

            //Set comment as unread for project users
            $this->unreadCommentsService->store($user, array_except($data, ['description']));

            event(new CommentPosted($user, $project, $new_comment, $data['revision_id']));
        }

        $comment = Comment::with('files')->with('user')->find($comment->id);
        return $comment;
    }

    /**
     * Delete Issue
     * @param $issue_id
     * @return mixed
     */
    public function deleteIssue($issue_id)
    {
        $fileService = new FileService();
        $issue = Issue::findOrFail($issue_id);

        if (count($issue->comments)) {
            foreach ($issue->comments as $key => $comment) {
                if (count($comment->files)) {
                    foreach ($comment->files as $file) {
                        $fileService->deleteFile($file->id, 'comment');
                    }
                }

                $comment->delete();
            }
        }

        if (count($issue->files)) {
            foreach ($issue->files as $file) {
                $fileService->deleteFile($file->id, 'issue');
            }
        }

        return $issue->delete();
    }

    /**
     * Delete Comment
     * @param $comment_id
     * @return mixed
     */
    public function deleteComment($comment_id)
    {
        $fileService = new FileService();
        $comment = Comment::findOrFail($comment_id);

        if (count($comment->files)) {
            foreach ($comment->files as $file) {
                $fileService->deleteFile($file->id, 'comment');
            }
        }

        return $comment->delete();
    }

    /**
     * Delete Proof
     * @param $proofId
     * @return mixed
     */
    public function deleteProof($proofId)
    {
        $proof = $this->model->find($proofId);
        $issues = $proof->issues;

        //Deleting proof issues comments
        foreach ($issues as $issue) {
            $issue->comments()->delete();
        }

        //Deleting proof issues
        $proof->issues()->delete();

        //Deleting proof image from server
        $fileId = $proof->projectFiles->id;
        $this->fileService->deleteFile($fileId);

        //Deleting proof image from database
        $proof->projectFiles()->delete();

        //Delete proof
        return $proof->delete();
    }
}