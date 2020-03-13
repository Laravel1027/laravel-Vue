Vue.component('kiosk-users', {
    data() {
        return {
            loader: false,
            users: {},
            detailingUser: null,
            activePlan: null,
            deletingUser: null,
            deletingUserProcess: false,
            searchString: '',
            cancelSource: null,
        }
    },
    methods: {
        getUsers() {
            this.loader = true;

            axios.get('/api/admin/users/all')
                .then(response => {
                    this.loader= false;

                    if (response.data.status) {
                        this.users = response.data.data;
                    } else {
                        toastr['error'](response.data.errors, 'Error');
                    }
                })
                .catch(error => {
                    this.loader = false;
                    toastr['error']('Internal server error, try again later', 'Error');
                })
        },

        showUserDetails(user) {
            this.detailingUser = user;
            $('#modal-user-details').modal('show');
        },

        impersonate(user) {
            window.location = '/spark/kiosk/users/impersonate/' + user.id;
        },

        activeSubscription(userId) {
            axios.get(`/api/admin/users/active-subscription/${userId}`)
                .then(response => {
                    if (response.data.status) {
                        this.activePlan = response.data.data.plan;
                        return true;
                    } else {
                        toastr['error'](response.data.errors, 'Error');
                    }
                })
                .catch(error => {
                    toastr['error']('Internal server error, try again later', 'Error');
                })
        },

        approveUserDeleting(user) {
            this.deletingUser = user;
            $('#modal-delete-user').modal('show');
        },

        deleteUser() {
            this.deletingUserProcess = true;

            axios.delete(`/api/admin/users/delete/${this.deletingUser.id}`)
                .then(response => {
                    this.deletingUserProcess = false;
                    if (response.data.status) {
                        toastr['success'](response.data.message, 'Success');
                        this.getUsers();
                        $('#modal-delete-user').modal('hide');
                    } else {
                        toastr['error'](response.data.errors, 'Error');
                    }
                })
                .catch(error => {
                    this.deletingUserProcess = false;
                    toastr['error']('Internal server error, try again later', 'Error');
                })
        },
    },
    created() {
    },
    mounted() {
        this.getUsers();
    },
    watch: {
        searchString (value) {
            if (!value) {
                this.loader = true;
                this.getUsers()
            } else {
                //Cancel prev request
                if (this.cancelSource) {
                    this.cancelSource.cancel('Operation canceled');
                }

                this.users = [];
                this.loader = true;
                this.cancelSource = axios.CancelToken.source();

                axios.post('/api/admin/users/search', {'data': this.searchString}, {cancelToken: this.cancelSource.token})
                    .then(response => {
                        this.loader = false;

                        if (response && response.data.status) {
                            this.users = response.data.data;
                        }
                    })
                    .catch(error => {
                        this.loader = false;

                        if (axios.isCancel(message)) {
                            console.log('Request canceled');
                        } else {
                            toastr['error']('Internal server error, try again later', 'Error');
                        }
                    })
            }
        }
    }
});