<spark-pending-invitations inline-template>
    <div>
        <div class="panel panel-default" v-if="invitations.length > 0">
            <div class="panel-heading">Pending Invitations</div>

            <div class="panel-body">
                <table class="table table-borderless m-b-none">
                    <thead>
                        <th>{{ ucfirst(Spark::teamString()) }}</th>
                        <th></th>
                        <th></th>
                    </thead>

                    <tbody>
                        <tr v-for="invitation in invitations">
                            <!-- Team Name -->
                            <td>
                                <div class="btn-table-align">
                                    @{{ invitation.team.name }}
                                </div>
                            </td>

                            <td style="text-align: center">
                                <!-- Accept Button -->
                                <button class="btn btn-success accept-invitation" style="width: 170px" @click="accept(invitation)">
                                    <i class="fa fa-check"></i> ACCEPT INVITATION
                                </button>

                                <!-- Reject Button -->
                                <button class="btn btn-danger-outline decline-invitation" style="width: 170px" @click="reject(invitation)">
                                    <i class="fa fa-times"></i> DECLINE INVITATION
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</spark-pending-invitations>
