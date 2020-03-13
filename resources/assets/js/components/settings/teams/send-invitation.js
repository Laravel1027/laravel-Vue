import {mapGetters, mapActions} from 'vuex';

Vue.component('send-invitation', {
    props: ['user', 'team', 'billableType', 'invitations'],

    /**
     * The component's data.
     */
    data() {
        return {
            form: new SparkForm({
                    email: ''
                })
            };
    },


    computed: {
        /**
         * Check if there's a limit for the number of team members.
         */
        hasTeamMembersLimit() {
            if (! this.activePlan) {
                return false;
            }

            return !! this.activePlan.teams_members_count;
        },


        /**
         * Get the remaining team members in the active plan.
         */
        remainingTeamMembers() {
            return this.activePlan
                ? this.activePlan.teams_members_count - (this.$parent.team.users.length + this.invitations.length)
                : 0;
        },


        /**
         * Check if the user can invite more team members.
         */
        canInviteMoreTeamMembers() {
            return this.remainingTeamMembers > 0;
        },
        ...mapGetters([
            'activeSubscription',
            'activePlan',
            'ownedTeams',
        ]),
    },

    /**
     * The component has been created by Vue.
     */
    created() {
        this.getActiveSubscription();
    },


    methods: {
        /**
         * Send a team invitation.
         */
        send() {
            Spark.post(`/settings/${Spark.pluralTeamString}/${this.team.id}/invitations`, this.form)
                .then(() => {
                    this.form.email = '';

                    this.$parent.$emit('updateInvitations');
                });
        },

        ...mapActions([
            'getActiveSubscription'
        ])
    }
});