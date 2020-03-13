<template>
    <div v-loading.fullscreen.lock="fullscreenLoading">
        <el-row type="flex" justify="center">
            <el-col :md="18">
                <el-row style="text-align: center">
                    <h3>Project Creative Brief</h3>
                </el-row>
                <el-row style="margin-top: 20px" type="flex" justify="center">
                    <div>
                        <quill-editor v-model="formData.creative_brief"
                                      ref="myQuillEditor"
                                      :options="editorOption">
                        </quill-editor>
                    </div>
                </el-row>
            </el-col>
        </el-row>
        <el-row style="text-align: center">
            <el-col style="margin-top: 20px">
                <el-button type="primary" @click="saveCreativeBrief(false)">Continue</el-button>
                <el-button v-if="project_type == 'website'" @click="nextStep">Skip</el-button>
                <el-button @click="cancel()" class="save-draft-btn">Save As Draft</el-button>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import {mapGetters} from 'vuex';

    export default {
        name: "ProjectCreativeBrief",
        props: ['project_id', 'revision_id', 'user_id', 'project_type'],
        data() {
            return {
                fullscreenLoading: false,
                formData: {
                    project_id: this.project_id,
                    creative_brief: ''
                },
                editorOption: {
                    // quill options
                }
            }
        },
        computed: {
            editor() {
                return this.$refs.myQuillEditor.quill
            },
            ...mapGetters([
                'ownedTeam',
                'teamMembers'
            ])
        },
        methods: {
            saveCreativeBrief(draft) {
                if (this.project_type == 'website' && this.formData.creative_brief == '') {
                    toastr['error']('Creative brief can not be empty', 'Error');
                } else {
                    this.fullscreenLoading = true;
                    axios.post('/api/projects/save-creative-brief', this.formData)
                        .then(response => {
                            this.fullscreenLoading = false;
                            if (response.data.status) {
                                if (draft) {
                                    toastr['success']('Project saved as draft', 'Success');
                                    this.$router.push({path: '/projects'});
                                } else {
                                    toastr['success'](response.data.message, 'Success');
                                    this.nextStep();
                                }
                            } else {
                                toastr['error'](response.data.errors, 'Error');
                            }
                        })
                        .catch(error => {
                            this.fullscreenLoading = false;
                            toastr['error']('Something went wrong please try again later', 'Error');
                        })
                }
            },
            nextStep() {
                if (this.ownedTeam && this.teamMembers.length) {
                    this.$router.push({
                        name: 'add_team_members',
                        params: {
                            project_id: this.project_id,
                            revision_id: this.revision_id,
                            user_id: this.user_id,
                            project_type: this.project_type,
                            creative_brief: this.formData.creative_brief
                        }
                    });
                } else {
                    this.$router.push({
                        name: 'upload_files_with_revision',
                        params: {
                            project_id: this.project_id,
                            revision_id: this.revision_id,
                            user_id: this.user_id,
                            creative_brief: this.formData.creative_brief,
                            project_type: this.project_type
                        }
                    });
                }
            },
            cancel() {
                if (this.formData.creative_brief != '') {
                    this.saveCreativeBrief(true);
                } else {
                    toastr['success']('Project saved as draft', 'Success');
                    this.$router.push({path: '/projects'});
                }
            }
        },
    }
</script>

<style>
    .ql-editing, .ql-tooltip {
        left: 2px !important;
        top: -8px !important;
    }

    @media (max-width: 550px) {
        .ql-editing, .ql-tooltip {
            width: 95% !important;
        }

        .ql-editing input {
            width: 70% !important;
        }

        .ql-editing:before, .ql-tooltip:before {
            content: none !important
        }
    }

    @media (max-width: 720px) {
        .save-draft-btn {
            margin-top: 10px;
        }
    }
</style>