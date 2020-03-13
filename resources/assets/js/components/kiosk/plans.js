Vue.component('spark-kiosk-plans', {
    data() {
        return {
            plans: {},
            deletingPlan: null,
            deletingPlanProcess: false,
            detailingPlan: null,
            loading: false,
        }
    },
    methods: {
        getPlans() {
            this.loading = true;
            var self = this;
            axios.get('/plans/list')
                .then(response => {
                    this.loading = false
                    if (response.data.status) {
                        self.plans = response.data.data;
                    } else {
                        toastr['error'](response.data.errors, 'Error');
                    }
                })
                .catch(error => {
                    this.loading = false;
                    if (error.exception) {
                        toastr['error']('Something went wrong, please try again later', 'Error');
                    } else {
                        toastr['error'](error.message, 'Error');
                    }
                });
        },
        showPlanDetails(plan) {
            this.detailingPlan = plan;
            $('#modal-plan-details').modal('show');
        },
        create() {
            Bus.$emit('createPlan');
        },
        approvePlanDeleting(plan) {
            this.deletingPlan = plan;
            $('#modal-delete-plan').modal('show');
        },
        deletePlan() {
            this.deletingPlanProcess = true;
            axios.delete('/plans/delete/' + this.deletingPlan.id)
                .then(response => {
                    this.deletingPlanProcess = false;
                    $('#modal-delete-plan').modal('hide');
                    if (response.data.status) {
                        toastr['success'](response.data.message, 'Success');
                        this.getPlans();
                    } else {
                        toastr['error'](response.data.errors, 'Error');
                    }
                })
                .catch(error => {
                    if (error.exception) {
                        toastr['error']('Something went wrong, please try again later', 'Error');
                    } else {
                        toastr['error'](error.message, 'Error');
                    }
                })
        }
    },
    created() {
        var self = this;
        this.getPlans();
        Bus.$on('planCreated', function (data) {
            toastr['success'](data.message, 'success');
            self.getPlans();
        });
    },
    filters: {
        currency(value) {
            return '$' + parseFloat(value/100).toFixed(2);
        }
    },
});
