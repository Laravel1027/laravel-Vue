import {mapActions, mapGetters} from 'vuex';

Vue.component('create-team', {
    props: ['user'],
    data() {
        return {
            loading: true,
            form: new SparkForm({
                name: '',
                slug: ''
            })
        };
    },

    computed: {
        hasTeamLimit() {
            if (!this.activePlan) {
                return;
            }
            return this.activePlan.teams_count;
        },

        remainingTeams() {
            return this.activePlan
                ? this.activePlan.teams_count - this.ownedTeams.length
                : 0;
        },

        canCreateMoreTeams() {
            return this.remainingTeams > 0;
        },
        ...mapGetters([
            'activeSubscription',
            'activePlan',
            'ownedTeams',
        ])
    },

    created() {
    },


    events: {
        activatedTab(tab) {
            if (tab === Spark.pluralTeamString) {
                Vue.nextTick(() => {
                    $('#create-team-name').focus();
                });
            }

            return true;
        }
    },


    watch: {
        'form.name': function (val, oldVal) {
            if (this.form.slug == '' ||
                this.form.slug == oldVal.toLowerCase().replace(/[\s\W-]+/g, '-')
            ) {
                this.form.slug = val.toLowerCase().replace(/[\s\W-]+/g, '-');
            }
        },
        activeSubscription() {
            this.loading = false;
        }
    },


    methods: {
        create() {
            Spark.post('/settings/'+Spark.pluralTeamString, this.form)
                .then(() => {
                    this.form.name = '';
                    this.form.slug = '';

                    Bus.$emit('updateUser');
                    Bus.$emit('updateTeams');
                    this.loading = true;
                    this.getActiveSubscription();
                });
        },
        ...mapActions([
            'getActiveSubscription'
        ])
    }
})