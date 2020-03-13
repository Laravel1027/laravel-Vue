<template>
    <div>
        <form>
            <el-row style="text-align: center">
                <pdf-converting v-if="pdfLoader" v-on:confirm="pdfLoader = false"></pdf-converting>
                <el-row class="upload-container">
                    <el-col :xs="24" :md="{span:8, offset:8}" class="upload-area">
                        <el-upload ref="upload" drag :data="formData" :headers="headers" action="/api/files/upload" :on-preview="handlePreview" :on-remove="handleRemove"
                                   :on-error="handleError" :on-success="handleSuccess" :before-upload="handleUpload" :auto-upload="true" :file-list="fileList" name="photos"
                                   multiple list-type="picture">
                            <i class="el-icon-upload"></i>
                            <div class="el-upload__text">Drop file here or
                                <em>click to upload</em>
                            </div>
                        </el-upload>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :xs="24" :md="{span:8, offset:8}" class="buttons-area-files">
                        <!-- <el-button type="primary" @click="finishCreateProject()" :loading="saveLoading">Finish</el-button> -->
                        <el-button type="primary" @click="sendProject()" :loading="sendLoading">Finish & Send</el-button>
                        <el-button @click="cancel()" class="save-draft-btn">Save As Draft</el-button>
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :xs="24" :md="{span:8, offset:8}" class="buttons-area-files creative-brief">
                        <el-button icon="el-icon-document"
                                   class="creative-brief-button"
                                   @click="openCreativeBriefDialog">
                            View Creative Brief
                        </el-button>
                    </el-col>
                </el-row>
            </el-row>
        </form>

        <!--Creative Brief Dialog-->
        <el-dialog title="Project Creative Brief"
                   :visible.sync="showDialogCreativeBrief"
                   class="creative-brief-dialog">
            <quill-editor v-model="creativeBrief"
                          ref="myQuillEditor"
                          :options="editorOption">
            </quill-editor>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="saveCreativeBrief">Save</el-button>
                <el-button @click="showDialogCreativeBrief = false">Cancel</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
    import { validationMixin } from 'vuelidate'
    import Ls from '../../services/ls'
    import PdfConverting from './partials/PdfConverting'
    const { email, required } = require('vuelidate/lib/validators')

    export default {
        props: [
            'project_id',
            'revision_id',
            'user_id',
            'creative_brief',
            'project_type',
        ],
        components: {
            'pdf-converting': PdfConverting
        },
        data() {
            return {
                formData: {
                    file_type: '',
                    owner_type: '',
                    project_id: '',
                    user_id: ''
                },

                fileList: [],
                headers: {},
                saveLoading: false,
                sendLoading: false,
                user_type: 'freelancer',
                savedFiles: [],
                pdfLoader: false,
                editorOption: {},
                creativeBrief: '',
                showDialogCreativeBrief: false,
            }
        },
        methods: {
            handlePreview() {

            },
            handleRemove() {

            },
            handleError(error) {
                console.log(error)
            },
            handleSuccess(response) {
                var self = this;
                if (response.status == 1) {
                    if (response.data.length) {
                        response.data.forEach(function (element, index) {
                            self.savedFiles.push(element);
                            self.fileList.push({
                                name: 'Converted Image-' + (index + 1),
                                url: '/storage/' + element.path,
                            })
                        })
                    } else {
                        this.savedFiles.push(response.data);
                    }
                    toastr['success'](response.message, 'Success');
                } else {
                    this.pdfLoader = false;
                    toastr['error'](response.error, 'Error');
                }
            },
            handleUpload(file) {
                if (file.type == 'application/pdf') {
                    this.pdfLoader = true;
                }
            },
            sendProject() {
                var self = this;
                this.$confirm('Are you ready to send this project to client?', 'Warning', {
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel',
                    type: 'success',
                    title: ''
                }).then(() => {
                    if (this.savedFiles.length > 0 || this.project_type == 'website') {
                        this.sendLoading = true
                        axios.get('/api/projects/send_project/' + this.project_id + '/' + this.user_type).then(response => {
                            self.$router.push({ path: '/projects' });
                            this.sendLoading = false
                            toastr['success'](response.data.message, 'Success')
                            this.clearForm();

                        }).catch(error => {
                            this.sendLoading = false
                            console.log(error.message)
                        });

                    } else {
                        toastr['error']('You should upload some proofs before send this project to the client', 'Error');
                    }

                }).catch(() => {
                    console.log('canceled')
                });
            },
            finishCreateProject() {
                this.$router.push('/projects');
            },
            jqueryCssAdjust() {
                $('.el-upload__input').css('display', 'none');
            },
            cancel() {
                toastr['success']('Project saved as draft', 'Success');
                this.$router.push({ path: '/projects' });
            },
            openCreativeBriefDialog() {
                this.showDialogCreativeBrief = true;
            },
            saveCreativeBrief() {
                let formData = {
                    project_id: this.project_id,
                    creative_brief: this.creativeBrief
                };

                axios.post('/api/projects/save-creative-brief', formData)
                    .then(response => {
                        this.fullscreenLoading = false;
                        if (response.data.status) {
                            this.showDialogCreativeBrief = false;
                            this.$notify({
                                title: 'Success',
                                message: response.data.message,
                                type: 'success'
                            });
                        } else {
                            this.$notify({
                                title: 'Error',
                                message: response.data.errors,
                                type: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        this.$notify({
                            title: 'Error',
                            message: 'Something went wrong please try again later',
                            type: 'error'
                        });
                    })
            },
            getProjectCreativeBrief() {
                axios.get(`/api/projects/creative-brief/${this.project_id}`)
                    .then(response => {
                        if (response.data.status) {
                            this.creativeBrief = response.data.data
                        } else {
                            this.$notify({
                                title: 'Error',
                                message: 'Can not get project creative brief',
                                type: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        this.$notify({
                            title: 'Error',
                            message: 'Can not get project creative brief',
                            type: 'error'
                        });
                    })
            }
        },
        mounted() {
            const AUTH_TOKEN = Ls.get('auth.token');
            this.headers = {
                Authorization: `Bearer ${AUTH_TOKEN}`,
                Accept: 'application/json'
            }
            this.formData.file_type = 'picture';
            this.formData.owner_type = 'proof';
            this.formData.project_id = this.project_id;
            this.formData.user_id = this.user_id;
            if (this.revision_id) {
                this.formData.revision_id = this.revision_id;
            }
            this.$nextTick(function () {
                this.jqueryCssAdjust();
            });

            if (!this.creative_brief) {
                this.getProjectCreativeBrief();
            } else {
                this.creativeBrief = this.creative_brief;
            }
        }
    }
</script>

<style>
    .pdfLoader {
        width: 35%;
        height: 100%;
        position: absolute;
        top: 9px;
        left: -10px;
        z-index: 999;
    }

    .upload-container {
        max-height: calc(100vh - 170px);
        overflow-x: auto;
    }
    .upload-container .upload-area {
        margin-top: 20px;
    }
    .upload-area .el-upload,
    .upload-area .el-upload-dragger {
        width: 100%;
    }
    .buttons-area-files {
        margin-top: 20px;
    }

    @media (max-width: 367px) {
        .save-draft-btn {
            margin-top: 10px;
            margin-left: unset !important;
        }
    }

    @media (max-width: 550px) {
        .el-message-box {
            width: 90%;
        }
    }
    @media (max-width: 720px) {
        .el-dialog__wrapper .el-dialog {
            width: 90%;
        }
    }
</style>