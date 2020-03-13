<?php

namespace App\Services;


use App\Contracts\IBootstrapService;
use App\User;

class BootstrapService implements IBootstrapService
{
    /**
     * @var User
     */
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Get user datas
     * @param $user
     * @return mixed
     */
    public function bootstrap($user)
    {
        $ownedTeam = $user->ownedTeams()->first();

        return [
            'user' => $user,
            'isFreelancer' => isFreelancer($user),
            'isSubscribed' => isSubscribed($user),
            'currentPlan' => isSubscribed($user) ? $user->currentPlan() : [],
            'unread_comments' => $user->unreadComments->count(),
            'ownedTeam' => $ownedTeam ? $ownedTeam : '',
            'exitingMembers' => $ownedTeam ? $ownedTeam->users()->where('id', '<>', $user->id)->get() : [],
            'teamInvitations' => $ownedTeam ? $ownedTeam->invitations : [],
        ];
    }

    /**
     * Get User by ID
     * @param $id
     * @return mixed
     */
    public function getUserById($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Change project listing type for user
     * @param $user
     * @param $type
     * @return mixed
     */
    public function changeProjectsListingType($user, $type)
    {
        $user->projects_listing_type = $type;
        return $user->save();
    }

    /**
     * Get user recent data for project create
     * @param $user
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function getRecentDatas($user)
    {
        $projects = $user->projects;
        $clientEmails = [];
        $clientNames = [];
        $projectCompanies = [];
        $tempCompanies = [];

        if (count($projects)) {
            foreach ($projects as $project) {
                if ($project->autocomplete) {
                    //Client
                    $client = $project->users()->where('role', 'client')->first();

                    if ($client) {
                        //Client last project
                        $lastProject = $client->projects()->latest()->first();

                        if ($lastProject->autocomplete) {
                            //Client email
                            $email = (object)[
                                'value' => $client['email'],
                                'clientName' => $client['name'],
                                'projectCompany' => $lastProject['company'],
                                'id' => $lastProject['id']
                            ];
                            if (!in_array($email, $clientEmails)) {
                                $clientEmails[] = $email;
                            };

                            //Client name
                            $name = (object)[
                                'value' => $client['name'],
                                'clientEmail' => $client['email'],
                                'projectCompany' => $lastProject['company'],
                                'id' => $lastProject['id'],
                                'owner' => $user->id == $lastProject->created_by
                            ];
                            if (!in_array($name, $clientNames)) {
                                $clientNames[] = $name;
                            }
                        }
                    }

                    //Project Company
                    $company = [
                        'value' => $project->company,
                        'clientEmail' => $project->users()->where('role', 'client')->first()['email'],
                        'clientName' => $project->users()->where('role', 'client')->first()['name'],
                        'owner' => $user->id == $project->created_by,
                        'id' => $project->id
                    ];

                    if (!in_array(array_except($company, 'id'), $tempCompanies)) {
                        $tempCompanies[] = array_except($company, 'id');

                        $projectCompanies[] = (object)$company;
                    }
                }
            }
        }

        return [
            'clientEmails' => $clientEmails,
            'clientNames' => $clientNames,
            'projectCompanies' => $projectCompanies,
        ];
    }

    /**
     * Disable autocomplete for project
     * @param $data
     * @return mixed
     */
    public function disableAutocompleteData($user, $data)
    {
        $project = $user->projects->where('id', $data['id'])
            ->where('created_by', $user->id)->first();
        $project->autocomplete = 0;

        return $project->save();
    }

    /**
     * Get user active subscription and plan details
     * @param $user
     * @return array
     */
    public function getActiveSubscription($user)
    {
        $activeSubscription = $user->subscriptions->first();
        $currentPlan = $user->currentPlan();

        if ($currentPlan && $user->subscribedToPlan($currentPlan->stripe_plan_id, 'default')) {
            $result = [
                'subscription' => $activeSubscription,
                'plan' => $currentPlan,
                'ownedTeams' => $user->ownedTeams
            ];
            return $result;
        }

        return [];

    }
}