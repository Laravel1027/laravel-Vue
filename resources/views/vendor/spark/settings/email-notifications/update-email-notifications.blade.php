<update-email-notifications :user="user" inline-template>
    <div class="panel panel-default">
        <div class="panel-heading">Email Notifications</div>

        <div class="panel-body notify-body">
            <!-- Info Message -->
            <div :class="result_info" v-if="message != ''"> @{{ message }}</div>

            <form class="form-horizontal" role="form">
                <h4 class="notify-info">Receive email notifications when:</h4>
                <!-- New Project -->
                <div class="form-group">
                    <label class="col-md-6 notify-label">New project is created</label>

                    <div class="col-md-2">
                        <label class="switch">
                            <input type="checkbox" name="new_project" v-model="form.new_project">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>

                <!-- New Issue -->
                <div class="form-group">
                    <label class="col-md-6 notify-label">New issue is posted</label>

                    <div class="col-md-2">
                        <label class="switch">
                            <input type="checkbox" name="new_comment" v-model="form.new_issue">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>

                <!-- Approved Issue -->
                <div class="form-group">
                    <label class="col-md-6 notify-label">Issue status changed</label>

                    <div class="col-md-2">
                        <label class="switch">
                            <input type="checkbox" name="new_comment" v-model="form.issue_status">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>

                <!-- New Comment -->
                <div class="form-group">
                    <label class="col-md-6 notify-label">New comment is posted</label>

                    <div class="col-md-2">
                        <label class="switch">
                            <input type="checkbox" name="new_comment" v-model="form.new_comment">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>

                <!-- New Corrections -->
                <div class="form-group">
                    <label class="col-md-6 notify-label">Corrections are submitted</label>

                    <div class="col-md-2">
                        <label class="switch">
                            <input type="checkbox" name="new_correction" v-model="form.new_correction">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>

                <!-- New Revision Round -->
                <div class="form-group">
                    <label class="col-md-6 notify-label">New revision round is uploaded</label>

                    <div class="col-md-2">
                        <label class="switch">
                            <input type="checkbox" name="new_revision" v-model="form.new_revision">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>

                <!-- Approved Project-->
                <div class="form-group">
                    <label class="col-md-6 notify-label">Project is approved</label>

                    <div class="col-md-2">
                        <label class="switch">
                            <input type="checkbox" name="approved_project" v-model="form.approved_project">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>

                <!-- Completed Project-->
                <div class="form-group">
                    <label class="col-md-6 notify-label">Project is completed</label>

                    <div class="col-md-2">
                        <label class="switch">
                            <input type="checkbox" name="completed-project" v-model="form.completed_project">
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>

                <!-- Update Button -->
                <div class="form-group">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary"
                                @click.prevent="update"
                                :disabled="form.busy">
                            Update
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</update-email-notifications>
