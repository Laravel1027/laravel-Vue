<?php

namespace App\Services;

use App\Contracts\INotificationService;
use App\EmailNotificationSettings;
use App\Events\CustomerInvited;
use App\Events\CorrectionsSubmitted;
use App\Events\FinalFilesUploaded;
use App\Events\NewRevisionCreated;
use App\Events\ProjectApproved;
use App\Events\ProjectCreated;
use App\Project;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Contracts\IProjectService;
use App\Contracts\IRevisionService;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ApiResponse;
use App\Mail\PasswordChange;
use Laravel\Spark\Repositories\UserRepository;

/* use Laravel\Spark\Contracts\Interactions\Auth; */

class ProjectService implements IProjectService
{
    private $revisionService;

    private $userRepo;

    private $model;

    protected $notifications;

    public function __construct(IRevisionService $revisionService, UserRepository $userRepo, Project $model, INotificationService $notifications)
    {
        $this->revisionService = $revisionService;
        $this->userRepo = $userRepo;
        $this->model = $model;
        $this->notifications = $notifications;
    }

    /**
     * Get user projects
     * @param $user
     * @return array|mixed
     */
    public function getProjects($user)
    {
        $projects = [];
        $projectTeam = '';

        $userProjects = $user->projects()->orderBy('updated_at', 'desc')->get();
        // $userProjects = $user->projects()->has('revisions')->orderBy('updated_at', 'desc')->get();
        // exit($userProjects);
        // print_r($userProjects);
        $ownedTeam = $user->ownedTeams()->first();

        if ($ownedTeam) {
            $teamMembers = $ownedTeam->users()->where('id', '<>', $user->id)->pluck('id');
        } else {
            $teamMembers = [];
        }

        if (count($userProjects)) {
            foreach ($userProjects as $key => $project) {
                $lastRevision = $this->revisionService->getLastRevision($project->id);
                $issues = $this->getIssues($lastRevision);
                $userRole = $project->users()->where('user_id', $user->id)->pluck('role');
                $unreadComments = $project->unreadComments()->where('user_id', $user->id)->count();
                $viewedByUser = $project->users()->where('user_id', $user->id)->pluck('viewed_by_user');
                $firstOpen = $project->users()->where('user_id', $user->id)->pluck('first_open');
                $projectUsers = $project->users()->pluck('user_id');
                $projectTeamMembers = $project->users()->whereIn('id', $teamMembers)->get();
                $client = $project->users()->where('role', 'client')->first();
                $collabCount = $project->users()->where('role', 'collaborator')->count();
                $projectOwner = $project->users()->where('user_id', $project->created_by)->first();

                if ($projectOwner && $projectOwner->id != $user->id && $userRole[0] == 'freelancer') {
                    $projectTeam = $projectOwner->ownedTeams()->first();
                } else {
                    $projectTeam = '';
                }
                $projects[$key] = [
                    'id' => $project->id,
                    'owner' => $project->created_by,
                    'name' => $project->name,
                    'creative_brief' => $project->creative_brief,
                    'active_revision' => $lastRevision,
                    'company' => $project->company,
                    'status' => $project->status,
                    'type' => $project->type,
                    'total_issues' => $issues['totalIssues'],
                    'solved_issues' => $issues['solvedIssues'],
                    'percentage' => $issues['percentage'],
                    'role' => $userRole[0],
                    'unreadComments' => $unreadComments,
                    'viewedByUser' => $viewedByUser[0],
                    'firstOpen' => $firstOpen[0],
                    'websiteURI' => $project->website_url,
                    'users' => $projectUsers,
                    'teamMembers' => $projectTeamMembers,
                    'client' => $client,
                    'collabCount' => $collabCount,
                    'active' => ($projectOwner && isSubscribed($projectOwner)) ? true : false,
                    'team' => $projectTeam ? $projectTeam->name : ''
                ];
            }
            return $projects;
        }
        return [];
    }

    /**
     * Create new project
     * @param array $data
     * @return Project|mixed|null|string
     */
    public function createProject(array $data)
    {
        $clientData['client_name'] = $data['client_name'];
        $clientData['email'] = $data['email'];
        $client = $this->registerUser($clientData);
        if ($client) {
            //Create project
            $project = $this->model->create([
                'name' => $data['name'],
                'project_hash' => Str::random(40),
                'video_url' => $data['video_url'],
                'type' => $data['type'],
                'website_url' => $data['website_url'],
                'status' => 'draft',
                'company' => $data['company'],
                'created_by' => Auth::user()->id
            ]);

            //Create project revision
            $project->revisions()->create([
                'version' => 1,
                'status_revision' => 'draft',
            ]);

            //Create project users
            $project->users()->attach([
                ['user_id' => Auth::user()->id, 'role' => 'freelancer'],
                ['user_id' => $client['user']->id, 'role' => 'client']
            ]);

            //Create project collaborators
            if (count($data['collaborators'])) {
                $this->registerCollaborators($project, $data['collaborators']);
            }

            $user = $client['user'];

            if ($client['newUser']) {

                //Send welcome email if client is newly registered
                $token = app('auth.password.broker')->createToken($user);

                event(new CustomerInvited($user, $project, $token));
            } else {
                //Send new project email if client is already registered
                $revision = $this->revisionService->getLastRevision($project->id);

                event(new ProjectCreated($user, $project, $revision));
        // exit(event(new ProjectCreated($user, $project, $revision)));
            }

            return [
                'newClient' => $client['newUser'],
                'client' => $client['user'],
                'project' => $project
            ];
        }
        return null;
    }

    /**
     * Save Creative Brief
     * @param array $data
     * @return mixed
     */
    public function saveCreativeBrief(array $data)
    {
        return $this->model->where('id', $data['project_id'])
            ->update(['creative_brief' => $data['creative_brief']]);
    }

    /**
     * Get Creative Brief
     * @param $id
     * @return mixed
     */
    public function getCreativeBrief($id)
    {
        $project = $this->model->findOrFail($id);

        return $project->creative_brief;
    }

    /**
     * Get project active revision
     * @param $project_id
     * @return mixed|void
     */
    public function getActiveRevision($project_id)
    {
        $activeRevision = Project::getActiveRevision($project_id);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function createSendProject(array $data)
    {
//        $project = Project::store($data);
//        $message = Mail::to($request->email)->send(new ProjectSent($project));
//        return $message;
    }

    /**
     * @param $project_id
     * @return mixed|string
     */
    public function getRevisionVersions($project_id)
    {
        $revisions = Project::getRevisionVersions($project_id);
        return $revisions;
    }

    /**
     * Send new revision
     * @param $project_id
     * @param $user_type
     * @return mixed|null
     */
    public function sendProject($project_id, $user_type)
    {
        $project = Project::findOrFail($project_id);
        if ($project) {
            $revision = $this->revisionService->getLastRevision($project->id);

            if ($revision) {
                $this->revisionService->changeRevisionStatus($revision->id, 'revision');
            }

            $users = $project->users()->where('id', '<>', Auth::user()->id)->get();
            foreach ($users as $user) {
                //Set viewed by user to 0 for project users
                $project->users()->updateExistingPivot($user->id, ['viewed_by_user' => 0]);

                $firstOpen = $project->users()->where('user_id', $user->id)->pluck('first_open')[0];

                //Create site notifications for project users
                $this->notifications->create($user, [
                    'icon' => 'fa fa-pencil-square-o',
                    'body' => Auth::user()->name . ' moved project in Progress',
                    'type' => 'Project In Progress',
                    'action_text' => 'View Project',
                    'action_url' => 'proofer/' . $project->id . '/' . $revision->id,
                    'company' => $project->company,
                    'project' => $project->name,
                    'proofer' => ''
                ]);

                if (!$firstOpen) {
                    event(new NewRevisionCreated($user, $project, $revision));
                }
            }
            return $revision;
        }
        return null;
    }

    /**
     * Registering new user for project
     * @param array $data
     * @return User|mixed|null
     */
    public function registerUser(array $data)
    {
        // print_r("123");exit;
        if ($this->getUserByEmail($data['email'])) {
            if (\Auth::user()->email == $data['email']) {
                return null;
            } else {
                $user = $this->getUserByEmail($data['email']);
                $newUser = false;
            }

        } else {
            $user = new User();
            $user->name = $data['client_name'];
            $user->password = bcrypt(Str::random(7));
            $user->email = $data['email'];
            $user->verified = 1;
            $user->save();

            $emailNotifications = new EmailNotificationSettings();
            $user->emailNotifications()->save($emailNotifications);
            $newUser = true;
        }
        return [
            'user' => $user,
            'newUser' => $newUser
        ];
    }

    public function getUserByEmail($email)
    {
        if ($email != '') {
            $user = User::where('email', $email)->first();
        // print_r($user);exit;
            return $user;
        }
        return null;
    }

    /**
     * Approve project
     * @param array $data
     * @return array|mixed
     */
    public function approveProject(array $data)
    {
        $project = $this->model->where('id', $data['project_id'])->first();

        if ($project) {
            //Set viewed by user to 0 for project users
            $projectUsers = $project->users()->where('user_id', '<>', Auth::user()->id)->get();
            foreach ($projectUsers as $user) {
                $project->users()->updateExistingPivot($user->id, ['viewed_by_user' => 0]);
            }
            //Change project status
            $project->status = 'approved';
            $project->save();

            //Change last revision status
            $revision = $this->revisionService->getLastRevision($project->id);
            $version = $this->revisionService->changeRevisionStatus($revision->id, 'approved');

            //Get project freelancers
            $freelancers = $project->users()->where('role', 'freelancer')->get();

            if (count($version->proofs) == 0) {
                $version->delete();
                $version->save();
            }
            if ($version) {
                foreach ($freelancers as $freelancer) {

                    //Create site notification for freelancers
                    $this->notifications->create($freelancer, [
                        'icon' => 'fa-check',
                        'body' => Auth::user()->name . ' approved project',
                        'type' => 'Approved',
                        'action_text' => 'View Project',
                        'action_url' => 'proofer/' . $project->id . '/' . $revision->id,
                        'company' => $project->company,
                        'project' => $project->name,
                        'proofer' => ''
                    ]);

                    event(new ProjectApproved($freelancer, $project, $revision));
                }
                return ApiResponse::success('Project approved successfully', ['client' => Auth::user()]);
            } else {
                return ApiResponse::error('001', 'This revision has already been approved. You can safely close your browser window and wait for future revisions');
            }
        }
        return ApiResponse::error('001', 'There has been an error sending the email');
    }

    /**
     * Submit Corrections
     * @param $project_id
     * @return mixed|null
     */
    public function submitCorrections($project_id)
    {
        $project = Project::findOrFail($project_id);
        if ($project) {
            //Set viewed by user to 0 for project users
            $projectUsers = $project->users()->where('user_id', '<>', Auth::user()->id)->get();
            foreach ($projectUsers as $user) {
                $project->users()->updateExistingPivot($user->id, ['viewed_by_user' => 0]);
            }

            //Change project status
            $project->status = 'progress';
            $project->save();

            //Change last revision status
            $revision = $this->revisionService->getLastRevision($project->id);
            $revision = $this->revisionService->changeRevisionStatus($revision->id, 'progress');

            $users = $project->users()->where('role', 'freelancer')->get();
            foreach ($users as $user) {
                //Create site notifications for project users
                $this->notifications->create($user, [
                    'icon' => 'fa-clock-o',
                    'body' => Auth::user()->name . ' added new corrections',
                    'type' => 'New Corrections',
                    'action_text' => 'View Project',
                    'action_url' => 'proofer/' . $project->id . '/' . $revision->id,
                    'company' => $project->company,
                    'project' => $project->name,
                    'proofer' => ''
                ]);

                event(new CorrectionsSubmitted($user, $project, $revision));
            }

            // Get project owner
            $owner = $project->users()->where('id', $project->created_by)->first();

            return [
                'revision' => $revision,
                'owner' => $owner
            ];
        }
        return null;
    }

    /**
     * Saving and sending final files
     * @param $data
     * @return array|null
     */
    public function sendfinalFiles($data)
    {
        $project = $this->model->where('id', $data['project_id'])->first();

        if (array_key_exists('owner_type', $data)) {
            $links = [];
        } else {
            $links = $data['links'];

            //Store project final links
            $project->finalLinks()->delete();
            foreach ($links as $link) {
                $project->finalLinks()->create(['url' => $link]);
            }
        }

        //Change project status;
        $project->status = 'completed';
        $project->save();

        //Change last revision status
        $revision = $this->revisionService->getLastRevision($project->id);
        $revision = $this->revisionService->changeRevisionStatus($revision->id, 'completed');

        //Get project client
        $client = $project->users()->where('role', 'client')->first();

        //Get project owner
        $owner = $project->users()->where('id', $project->created_by)->first();

        //Set viewed by user to 0 for project client
        $project->users()->updateExistingPivot($client->id, ['viewed_by_user' => 0]);

        //Create site notification for client
        $this->notifications->create($client, [
            'icon' => 'fa-upload',
            'body' => Auth::user()->name . ' uploaded files',
            'type' => 'Completed',
            'action_text' => 'View Project',
            'action_url' => 'proofer/' . $project->id . '/' . $revision->id,
            'company' => $project->company,
            'project' => $project->name,
            'proofer' => ''
        ]);

        event(new FinalFilesUploaded($client, $project, $links, $revision));

        return [
            'client' => $client,
            'owner' => $owner,
        ];
    }

    /**
     * Delete project
     * @param $project_id
     * @return mixed
     */
    public function deleteProject($project_id)
    {
        $project = $this->model->findOrFail($project_id);
//        $project->finalFiles()->delete();
        return $project->delete();
    }

    /**
     * Get project details
     * @param $projectId
     * @return mixed
     */
    public function getDetails($projectId)
    {
        $project = $this->model->where('id', $projectId)->first();

        if ($project) {
            $owner = $project->users()->where('user_id', $project->created_by)->first();
            $client = $project->users()->where('role', 'client')->first();

            return [
                'project' => $project,
                'client' => $client,
                'active' => isSubscribed($owner) ? true : false
            ];
        }
        return null;
    }

    /**
     * Update project data
     * @param $projectId
     * @param $data
     * @return mixed
     */
    public function updateProject($projectId, $data)
    {
        return $project = $this->model->where('id', $projectId)->update($data);
    }

    /**
     * Change project status
     * @param $projectId
     * @param $status
     * @return mixed
     */
    public function changeStatus($projectId, $status)
    {
        $project = $this->model->where('id', $projectId)->first();

        if ($project) {
            $project->finalFiles()->delete();
            if ($status == 'progress') {
                //Set viewed by user to 0 for project users
                $users = $project->users()->where('user_id', '<>', Auth::user()->id)->get();
                foreach ($users as $user) {
                    $project->users()->updateExistingPivot($user->id, ['viewed_by_user' => 0]);
                }
            }

            if ($project->type == 'design') {
                //Change last revision status
                $lastRevision = $this->revisionService->getLastRevision($project->id);
                $lastRevision->status_revision = $status;
                $lastRevision->save();

                //Change project status
                return $project->update(['status' => $status]);
            } elseif ($project->type == 'website') {
                //Change last revision status
                $lastRevision = $this->revisionService->getLastRevision($project->id);
                $lastRevision->status_revision = ($status == 'hold') ? 'hold' : 'revision';
                $lastRevision->save();

                //Change project status
                return $project->update(['status' => ($status == 'hold') ? 'hold' : 'revision']);
            }
        }

        return null;
    }

    /**
     * Get project issues
     * @param $lastRevision
     * @return array|mixed
     */
    public function getIssues($lastRevision)
    {
        $proofs = $lastRevision->proofs;
        $totalIssues = 0;
        $solvedIssues = 0;
        $percentage = 0;

        foreach ($proofs as $proof) {
            $totalIssues += $proof->issues->count();
            $solvedIssues += $proof->issues()->where('status', 'done')->count();
        }

        if ($solvedIssues > 0) {
            $percentage = round(($solvedIssues / $totalIssues) * 100);
        }

        return [
            'totalIssues' => $totalIssues,
            'solvedIssues' => $solvedIssues,
            'percentage' => $percentage
        ];
    }

    /**
     * Create Collaborators for project
     * @param $project
     * @param $collaborators
     * @return mixed
     */
    public function registerCollaborators($project, $collaborators)
    {
        $collabs = [];

        foreach ($collaborators as $key => $collaborator) {
            if ($this->getUserByEmail($collaborator)) {
                $user = $this->getUserByEmail($collaborator);

                //Send new project email if collaborator is already registered
                $revision = $this->revisionService->getLastRevision($project->id);

                event(new ProjectCreated($user, $project, $revision));
            } else {
                $user = $this->registerUser([
                    'client_name' => 'Collaborator',
                    'email' => $collaborator
                ]);

                $user = $user['user'];

                //Send welcome email if collaborator is newly registered
                $token = app('auth.password.broker')->createToken($user);

                event(new CustomerInvited($user, $project, $token));
            }

            $collabs[$key]['user_id'] = $user->id;
            $collabs[$key]['role'] = 'collaborator';

        }

        return $project->users()->attach($collabs);
    }

    /**
     * Load last revision
     * @param $project_id
     * @param $revision_id
     * @return array|mixed|null
     */
    public function loadInitialRevision($project_id, $revision_id)
    {
        $result = [];

        $user = Auth::user();
        $project = $this->model->where('id', $project_id)->first();
        $ownedTeam = $user->ownedTeams()->first();
        if ($ownedTeam) {
            $teamMembers = $ownedTeam->users()->where('id', '<>', $user->id)->pluck('id');
        } else {
            $teamMembers = [];
        }
        if ($project) {
            $project->users()->updateExistingPivot(Auth::user()->id, ['viewed_by_user' => 1]);
            $owner = $project->users()->where('user_id', $project->created_by)->first();
            $result['project_status'] = $project->status;
            $result['project_type'] = $project->type;
            $result['websiteURI'] = $project->website_url;
            $result['collaborators'] = $project->users()->where('role', 'collaborator')->get();
            $result['teamMembers'] = $project->users()->whereIn('id', $teamMembers)->get();
            $result['exitingUsers'] = $project->users()->pluck('email');
            $result['firstOpen'] = $project->users()->where('user_id', Auth::user()->id)->first()->pivot->first_open;
            $result['active'] = isSubscribed($owner) ? true : false;
            $result['creative_brief'] = $project->creative_brief;
            $result['id'] = $project->id;
            $result['name'] = $project->name;
            $result['company'] = $project->company;
            $revisionVersions = $this->getRevisionVersions($project_id);
            if ($revisionVersions) {
                foreach ($revisionVersions as $key => $version) {
                    $result['versions'][$key]['id'] = $version->id;
                    $result['versions'][$key]['status'] = $version->status_revision;
                    $result['versions'][$key]['value'] = $version->version;
                    $result['versions'][$key]['label'] = 'Revision Round ' . $version->version;
                }
            }
            if ($revision_id > 0) {
                $revision = $this->revisionService->getRevisionById($revision_id);
                if ($revision) {
                    $issues = $this->getIssues($revision);
                    $result['last_revision'] = $revision;
                    $result['total_issues'] = $issues['totalIssues'];
                    $result['solved_issues'] = $issues['solvedIssues'];
                    $result['percentage'] = $issues['percentage'];
                }
            } else {
                $revision = $this->revisionService->getLastRevision($project_id);
                if ($revision) {
                    $issues = $this->getIssues($revision);
                    $result['last_revision'] = $revision;
                    $result['total_issues'] = $issues['totalIssues'];
                    $result['solved_issues'] = $issues['solvedIssues'];
                    $result['percentage'] = $issues['percentage'];
                }
            }
            //Update first open field
            $project->users()->updateExistingPivot(Auth::user()->id, ['first_open' => 0]);

            return $result;
        }
        return null;
    }

    /**
     * Add team member to project
     * @param $data
     * @return mixed|null
     */
    public function addTeamMember($data)
    {
        $project = $this->model->where('id', $data['project_id'])->first();

        if ($project) {
            return $project->users()->attach([
                [
                    'role' => 'freelancer',
                    'user_id' => $data['member_id']
                ]
            ]);
        }
        return null;
    }

    /**
     * Remove team member from project
     * @param $projectId
     * @param $memberId
     * @return mixed|null
     */
    public function deleteTeamMember($projectId, $memberId)
    {
        $project = $this->model->where('id', $projectId)->first();

        if ($project) {
            $user = $project->users()->wherePivot('role', 'client')->where('id', $memberId)->get();
            if (count($user)) {
                return 'You can not remove client from project';
            }
            return $project->users()->detach($memberId);
        }
        return null;
    }

    /**
     * Get project final files
     * @param $projectId
     * @return array
     */
    public function getFinalFiles($projectId)
    {
        $project = $this->model->where('id', $projectId)->first();
        return [
            'links' => $project->finalLinks()->pluck('url'),
            'files' => $project->finalFiles,
        ];
    }
}
