<template>
    <div v-loading.fullscreen.lock="fullscreenLoading">
        <el-row type="flex" justify="center">
            <el-col>
                <el-row style="text-align: center">
                    <h3>Add Team Member</h3>
                </el-row>
                <el-row style="margin-top: 20px" type="flex" justify="center">
                    <div>
                        <div v-if="teamMembers.length > 0" v-for="member in teamMembers" :key="member.id"
                             style="margin-top: 10px">
                            <label class="notify-label">
                                <div style="display: flex; align-items: center">
                                    <img style="float: left" :src="member.photo_url"
                                         class="img-circle special-img team-circle user-avatar">
                                    <span class="member-emailaddress" style="margin-left: 20px">{{member.email}}</span>
                                </div>
                            </label>
                            <div style="float: right; margin-left: 20px">
                                <label class="switch">
                                    <input type="checkbox" name="new_project"
                                           :id="member.id"
                                           @change="modifyMemberAccess">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </el-row>
            </el-col>
        </el-row>
        <el-row style="text-align: center">
            <el-col style="margin-top: 20px">
                <el-button type="primary" @click="nextStep">Continue</el-button>
                <el-button v-if="project_type == 'website'" type="primary" @click="nextStep">Skip</el-button>
                <el-button @click="cancel()">Save As Draft</el-button>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex'

    export default {
        props: [
            'project_id',
            'revision_id',
            'user_id',
            'project_type',
            'creative_brief',
        ],
        data() {
            return {
                fullscreenLoading: false,
            }
        },
        methods: {
            modifyMemberAccess(event) {
                let formData = {
                    member_id: event.target.id,
                    project_id: this.project_id,
                };
                this.fullscreenLoading = true;

                if (event.target.checked) {
                    axios
                        .post("/api/projects/add-team-member", formData)
                        .then(response => {
                            if (response.data.status == 1) {
                                this.fullscreenLoading = false;
                                toastr['success'](response.data.message, 'Success');
                            } else {
                                this.fullscreenLoading = false;
                                event.target.checked = false;
                                toastr['error'](response.data.errors, 'Error');
                            }
                        })
                        .catch(error => {
                            this.fullscreenLoading = false;
                            event.target.checked = false;
                            toastr['error']('Something went wrong, please try again later', 'Error');
                            this.handle_error({});
                        });
                } else {
                    axios
                        .delete("/api/projects/delete-team-member/" + this.project_id + '/' + event.target.id)
                        .then(response => {
                            if (response.data.status == 1) {
                                this.fullscreenLoading = false;
                                toastr['success'](response.data.message, 'Success');
                            } else {
                                this.fullscreenLoading = false;
                                event.target.checked = true;
                                toastr['error'](response.data.errors, 'Error');
                            }
                        })
                        .catch(error => {
                            this.fullscreenLoading = false;
                            event.target.checked = true;
                            toastr['error']('Something went wrong, please try again later', 'Error');
                        });
                }
            },
            nextStep() {
                this.$router.push({
                    name: 'upload_files_with_revision',
                    params: {
                        project_id: this.project_id,
                        revision_id: this.revision_id,
                        user_id: this.user_id,
                        creative_brief: this.creative_brief,
                        project_type: this.project_type,
                    }
                });
            },
            cancel() {
                toastr['success']('Project saved as draft', 'Success');
                this.$router.push({ path: '/projects' });
            }

        },
        computed: {
            ...mapGetters(['teamMembers'])
        }
    }
</script>

<style scoped>

    /* The switch - the box around the slider */
    .switch {
        position: relative;
        display: inline-block;
        width: 49px;
        height: 23px;
    }

    /* Hide default HTML checkbox */
    .switch input {
        display: none;
    }

    /* The slider */
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 15px;
        width: 15px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #2196F3;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #2196F3;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .notify-body {
        text-align: center;
    }

    .notify-info {
        margin-bottom: 20px;
    }

    .notify-label {
        text-align: right;
        font-weight: normal;
    }

    @media (max-width: 550px) {
        .notify-label .member-emailaddress {
            margin-left: 10px !important;
        }
        .notify-label .user-avatar {
            margin: 0 !important;
        }
    }
</style>