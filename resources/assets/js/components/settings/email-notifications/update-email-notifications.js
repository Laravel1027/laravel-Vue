Vue.component('update-email-notifications', {
    props: ['user'],

    data() {
        return {
            form: new SparkForm({
                new_project: this.user.email_notifications ? this.user.email_notifications.new_project : 0,
                new_issue: this.user.email_notifications ? this.user.email_notifications.new_issue : 0,
                issue_status: this.user.email_notifications ? this.user.email_notifications.issue_status : 0,
                new_comment: this.user.email_notifications ? this.user.email_notifications.new_comment: 0,
                new_correction: this.user.email_notifications ? this.user.email_notifications.new_correction : 0,
                new_revision: this.user.email_notifications ? this.user.email_notifications.new_revision : 0,
                approved_project: this.user.email_notifications ? this.user.email_notifications.approved_project : 0,
                completed_project: this.user.email_notifications ? this.user.email_notifications.completed_project : 0
            }),
            result_info: '',
            message: ''
        };
    },

    methods: {
        update() {
            Spark.put('/settings/email-notifications', this.form)
                .then(response => {
                    this.message = response.info;
                    if (response.status) {
                        this.result_info = 'alert alert-success';
                    } else {
                        this.result_info = 'alert alert-danger';
                    }
                });
        }
    },
});