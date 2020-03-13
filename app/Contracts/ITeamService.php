<?php

namespace App\Contracts;


use Laravel\Spark\Invitation;

interface ITeamService
{
    /**
     * @param $data
     * @return mixed
     */
    public function inviteMembers($data);

    /**
     * @param $invitation
     */
    public function emailInvitation($invitation);

    /**
     * @param $team
     * @param $email
     * @param $invitedUser
     * @return mixed
     */
    public function createInvitation($team, $email, $invitedUser);

    /**
     * @param Invitation $invitation
     * @return string
     */
    public function view(Invitation $invitation);

    /**
     * @param $team
     * @param $member
     * @return mixed
     */
    public function deleteMember($team, $member);
}