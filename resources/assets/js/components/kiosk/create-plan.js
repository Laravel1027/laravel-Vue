Vue.component('create-plan', {
    props: [],
    data() {
        return {
            form: new SparkForm({
                name: '',
                description: '',
                price: '',
                teams_count: 0,
                teams_members_count: 0,
            }),
        }
    },
    methods: {
        create() {
            Spark.post('/plans/store', this.form)
                .then(response => {
                    if (response.status) {
                        Bus.$emit('planCreated', response);
                        $('#modal-create-plan').modal('hide');
                    } else {
                        toastr['error'](response.errors, 'Error');
                    }
                })
                .catch(err => {
                    if (err.exception) {
                        toastr['error']('Something went wrong, please try again later', 'Error');
                    } else {
                        toastr['error'](err.message, 'Error');
                    }
                })
        },
    },
    created() {
        Bus.$on('createPlan', function () {
            $('#modal-create-plan').modal('show');
        });
    }
});