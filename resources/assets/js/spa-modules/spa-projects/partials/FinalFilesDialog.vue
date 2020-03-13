<template>
    <div v-loading.fullscreen.lock="fullscreenLoading">
        <!--Final files options dialog-->
        <el-dialog title="Final Files" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false"
                   :visible.sync="showFinalFilesDialog"
                   width="80%" center top="10vh">
            <el-row type="flex" justify="center" align="middle">
                <el-col>
                    <el-row style="text-align: center">
                        <h4 v-if="currentRole == 'freelancer'">Please choose what option below best fits final files uploads.</h4>
                        <h4 v-else-if="currentRole == 'client' && project.status == 'completed'">
                            Here are your final files click the links below to access and download.
                        </h4>
                        <h4 v-else>
                            Your files will be uploaded soon and will be available in the completed section.
                        </h4>
                    </el-row>

                    <!--Options-->
                    <el-row style="margin-top: 20px" type="flex" justify="center">
                        <!--Links-->
                        <el-col :md="12" id="links">
                            <h2 v-if="currentRole == 'freelancer'">UPLOAD LINKS</h2>
                            <h2 v-else>FINAL LINKS</h2>
                            <h5 v-if="currentRole == 'freelancer'">
                                Copy and paste your downloadable link, allowing your customer to download.
                            </h5>
                            <i v-if="currentRole == 'freelancer'">EX: Dropbox, Google Drive, Box</i>
                            <div class="links-bckg">
                                <div v-if="currentRole == 'client' && !links.length">
                                    <div style="padding: 50px; display:flex; align-items:center; justify-content: center; color: #a4aaae;">
                                        <div style="text-align: center">
                                            <i class="el-icon-share" style="font-size: 80px !important; font-style: normal !important"></i>
                                            <h4>No Links</h4>
                                        </div>
                                    </div>
                                </div>
                                <div class="links">
                                    <el-tag
                                            :key="link"
                                            v-for="link in links"
                                            :disable-transitions="false"
                                            :closable="currentRole == 'freelancer' ? true : false"

                                            @close="handleLinkDelete(link)"
                                            style="margin: 5px 0; display:block">
                                        <a :href="link" target="_blank">{{link}}</a>
                                    </el-tag>
                                    <br>
                                    <div v-if="currentRole == 'freelancer'">
                                        <el-input
                                                class="input-new-tag"
                                                v-if="inputVisible"
                                                v-model="inputValue"
                                                ref="saveLinkInput"
                                                size="mini"
                                                @keyup.enter.native="handleLinkInput"
                                                @blur="handleLinkInput"
                                        >
                                        </el-input>
                                        <el-button v-else class="button-new-tag" size="small" @click="showLinkInput">+
                                            New
                                            Link
                                        </el-button>
                                    </div>
                                </div>
                                <el-button v-if="currentRole == 'freelancer'" type="primary" @click="sendFinalLinks()">Finish & Send</el-button>
                            </div>
                        </el-col>
                        <el-col :md="12" id="files">
                            <h2 v-if="currentRole == 'freelancer'">UPLOAD FILES</h2>
                            <h2 v-else>FINAL FILES</h2>
                            <h5 v-if="currentRole == 'freelancer'">Upload final files directly into your Prooflo projects.</h5>
                            <div v-if="currentRole == 'freelancer'" class="upload-bckg">
                                <div class="final-upload">
                                    <el-upload ref="upload" drag :data="imageFormData" action="/api/files/upload"
                                               :on-preview="handleFilePreview" :on-error="handleUploadError"
                                               :on-remove="deleteFile" :on-success="handleUploadSuccess"
                                               :before-upload="handleUpload" :auto-upload="true"
                                               :file-list="fileList" name="photos"
                                               list-type="picture-card" :multiple="true">
                                        <i class="el-icon-upload"></i>
                                    </el-upload>
                                </div>
                                <el-button type="primary" @click="sendFinalFiles()">Finish & Send</el-button>
                                <el-button type="primary" @click="exitingFilesDialog = true">View Uploaded Files</el-button>
                            </div>
                            <div v-else class="exiting-files">
                                <template v-if="exitingFiles.length">
                                    <ul class="el-upload-list el-upload-list--picture-card">
                                        <li v-for="(file, index) in exitingFiles" :key="file.id"
                                            class="el-upload-list__item is-success">
                                            <template v-if="file.path.substring(file.path.indexOf('.') + 1) == 'pdf'">
                                                <img src="/img/pdf-icon.png" class="el-upload-list__item-thumbnail">
                                            </template>
                                            <template v-else>
                                                <img :src="'/storage/' + file.path" class="el-upload-list__item-thumbnail">
                                            </template>
                                            <i class="el-icon-close"></i>
                                            <span class="el-upload-list__item-actions">
                                                <template v-if="file.path.substring(file.path.indexOf('.') + 1) != 'pdf'">
                                                    <span class="el-upload-list__item-preview" @click="handleFilePreview(file)">
                                                        <i class="el-icon-zoom-in"></i>
                                                    </span>
                                                </template>
                                                <span class="el-upload-list__item-preview"
                                                      @click="handleFileDownload(file.path, index)">
                                                    <i class="el-icon-download"></i>
                                                </span>
                                            </span>
                                        </li>
                                    </ul>
                                </template>
                                <template v-else>
                                    <div style="padding: 50px; display:flex; align-items:center; justify-content: center; color: #a4aaae;">
                                        <div style="text-align: center">
                                            <i class="el-icon-picture-outline" style="font-size: 80px !important"></i>
                                            <h4>No Files</h4>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </el-col>
                    </el-row>
                </el-col>
            </el-row>
            <el-row style="text-align: center; margin-top: 30px">
                <el-button @click="currentRole == 'client' ? closeFinalFilesDialog() : cancelFinalFiles()">{{currentRole == 'client' ? 'Close' : 'Cancel'}}</el-button>
            </el-row>
        </el-dialog>

        <!--Show uploaded file dialog-->
        <el-dialog :visible.sync="filePreviewDialog" top="10vh">
            <div style="padding: 20px">
                <img width="100%" :src="filePreviewUrl" alt="">
            </div>
        </el-dialog>

        <!--Show exiting files dialog-->
        <el-dialog :visible.sync="exitingFilesDialog" top="10vh">
            <div class="exiting-files">
                <template v-if="exitingFiles.length">
                    <ul class="el-upload-list el-upload-list--picture-card">
                        <li v-for="(file, index) in exitingFiles" :key="file.id" class="el-upload-list__item is-success" style="text-align: center">
                            <template v-if="file.path.substring(file.path.indexOf('.') + 1) == 'pdf'">
                                <img src="/img/pdf-icon.png" class="el-upload-list__item-thumbnail">
                            </template>
                            <template v-else>
                                <img :src="'/storage/' + file.path" class="el-upload-list__item-thumbnail">
                            </template>
                            <i class="el-icon-close"></i>
                            <span class="el-upload-list__item-actions">
                                <template v-if="file.path.substring(file.path.indexOf('.') + 1) != 'pdf'">
                                    <span class="el-upload-list__item-preview" @click="handleFilePreview(file)">
                                        <i class="el-icon-zoom-in"></i>
                                    </span>
                                </template>
                                <span v-if="currentRole == 'client'" class="el-upload-list__item-preview"
                                      @click="handleFileDownload(file.path, index)">
                                    <i class="el-icon-download"></i>
                                </span>
                                <span v-if="currentRole == 'freelancer'" class="el-upload-list__item-delete"
                                      @click="deleteFile(file)">
                                    <i class="el-icon-delete"></i>
                                </span>
                            </span>
                        </li>
                    </ul>
                </template>
                <template v-else>
                    <div style="padding: 50px; display:flex; align-items:center; justify-content: center; color: #a4aaae;">
                        <div style="text-align: center">
                            <i class="el-icon-picture-outline" style="font-size: 80px !important"></i>
                            <h4>No Files</h4>
                        </div>
                    </div>
                </template>
            </div>
        </el-dialog>
    </div>
</template>

<script>
    export default {
        name: "FinalFilesDialog",
        props: [
            'showFinalFilesDialog',
            'currentRole',
            'project',
            'revisionId'
        ],
        data() {
            return {
                fullscreenLoading: false,
                links: [],
                savedFiles: [],
                savedFilesIds: [],
                exitingFiles: [],
                linksFormData: {
                    project_id: this.project.id,
                    revision_id: this.revisionId,
                    links: null,
                },
                imageFormData: {
                    project_id: this.project.id,
                    file_type: "picture",
                    owner_type: "final",
                },
                fileList: [],
                inputVisible: false,
                inputValue: '',
                filePreviewUrl: '',
                filePreviewDialog: false,
                exitingFilesDialog: false,
            }
        },
        methods: {
            getFinalFiles() {
                var self = this;
                this.fullscreenLoading = true;
                axios.get(`/api/projects/getFinalFiles/${this.project.id}`)
                    .then(response => {
                        this.fullscreenLoading = false;
                        if (response.data.status) {
                            self.links = response.data.data.links;
                            self.exitingFiles = response.data.data.files;
                        } else {
                            toastr['error'](response.data.errors, 'Error');
                        }
                    })
                    .catch(error => {
                        this.fullscreenLoading = false;
                        if (error.exception) {
                            toastr['error']('Something went wrong, please try again later', 'Error');
                        } else {
                            toastr['error'](error.message, 'Error');
                        }
                    })
            },
            showLinkInput() {
                this.inputVisible = true;
                this.$nextTick(_ => {
                    this.$refs.saveLinkInput.$refs.input.focus();
                });
            },
            sendFinalLinks() {
                var self = this;
                this.fullscreenLoading = true;
                if (this.links.length > 0) {
                    this.linksFormData.links = this.links;
                    axios
                        .post("/api/projects/sendFinalFiles", this.linksFormData)
                        .then(response => {
                            self.fullscreenLoading = false;
                            if (response.data.status) {
                                // Identify project owner on Gist
                                convertfox.identify(response.data.data.owner.id, {
                                    email: response.data.data.owner.email,
                                    name: response.data.data.owner.name
                                });

                                // Trigger 'Congratulations Email' event
                                convertfox.track("Congratulations Email", {
                                    userId: response.data.data.owner.id,
                                    projectId: this.project_id
                                });
                                self.$emit('filesSended', response.data.message);
                            } else {
                                toastr['error'](response.data.errors, 'Error');
                            }
                        })
                        .catch(error => {
                            this.fullscreenLoading = false;
                            if (error.exception) {
                                toastr['error']('Something went wrong, please try again later', 'Error');
                            } else {
                                toastr['error'](error.message, 'Error');
                            }
                        });
                } else {
                    this.fullscreenLoading = false;
                    toastr['error']('You should write some links, before sending', 'Error');
                }
            },
            sendFinalFiles() {
                var self = this;
                this.fullscreenLoading = true;
                if (this.savedFiles.length > 0) {
                    this.imageFormData.files = this.savedFiles;
                    axios
                        .post("/api/projects/sendFinalFiles", this.imageFormData)
                        .then(response => {
                            self.fullscreenLoading = false;
                            if (response.data.status) {
                                // Identify project owner on Gist
                                convertfox.identify(response.data.data.owner.id, {
                                    email: response.data.data.owner.email,
                                    name: response.data.data.owner.name
                                });

                                // Trigger 'Congratulations Email' event
                                convertfox.track("Congratulations Email", {
                                    userId: response.data.data.owner.id,
                                    projectId: this.project_id
                                });

                                self.$emit('filesSended', response.data.message);
                            } else {
                                toastr['error'](response.data.errors, 'Error');
                            }
                        })
                        .catch(error => {
                            this.fullscreenLoading = false;
                            if (error.exception) {
                                toastr['error']('Something went wrong, please try again later', 'Error');
                            } else {
                                toastr['error'](error.message, 'Error');
                            }
                        });
                } else {
                    this.fullscreenLoading = false;
                    toastr['error']('You should upload some files, before sending', 'Error');
                }
            },
            isURL(link) {
                let regexp = /^(?:(?:https?|ftp):\/\/)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/\S*)?$/;

                if (regexp.test(link)) {
                    return true
                } else {
                    return false
                }
            },
            closeFinalFilesDialog() {
                this.$emit('closeFinalFilesDialog')
            },
            cancelFinalFiles() {
                var self = this;
                if (this.savedFilesIds.length) {
                    this.fullscreenLoading = true;
                    axios.post('/api/files/deleteFinalFiles', {projectId: this.project.id, ids: this.savedFilesIds})
                        .then(response => {
                            if (response.data.status) {
                                self.fullscreenLoading = false;
                                this.$emit('closeFinalFilesDialog')
                            } else {
                                self.fullscreenLoading = false;
                                toastr['error'](response.data.errors, 'Error');
                            }
                        })
                        .catch(error => {
                            this.fullscreenLoading = false;
                            if (error.exception) {
                                toastr['error']('Something went wrong, please try again later', 'Error');
                            } else {
                                toastr['error'](error.message, 'Error');
                            }
                        })
                } else {
                    this.$emit('closeFinalFilesDialog')
                }
            },
            deleteFile(file) {
                var self = this;
                this.$confirm('Are you sure you want to delete this file?', 'Warning', {
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    type: 'success',
                    title: ''
                }).then(() => {
                    var id = file.response ? file.response.data.id : file.id;
                    this.fullscreenLoading = true;
                    axios.delete(`/api/files/deleteFinalFile/${this.project.id}/${id}`)
                        .then(response => {
                            if (response.data.status) {
                                self.fullscreenLoading = false;
                                toastr['success'](response.data.message, 'Success');
                                self.getFinalFiles();
                            } else {
                                self.fullscreenLoading = false;
                                toastr['error'](response.data.errors, 'Error');
                            }
                        })
                        .catch(error => {
                            this.fullscreenLoading = false;
                            if (error.exception) {
                                toastr['error']('Something went wrong, please try again later', 'Error');
                            } else {
                                toastr['error'](error.message, 'Error');
                            }
                        });
                })
            },
            handleUpload(file) {},
            handleFilePreview(file) {
                this.filePreviewUrl = file.url ? file.url : `/storage/${file.path}`;
                this.filePreviewDialog = true;
            },
            handleFileDownload(file, index) {
                let link = document.createElement('a');
                link.href = `/storage/${file}`;
                link.download = `${this.project.company}_${this.project.name}_RR${this.project.versions[0].value}_${index + 1}`;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            },
            handleUploadSuccess(response) {
                var self = this;
                if (response.status) {
                    self.savedFilesIds.push(response.data.id);
                    self.savedFiles.push(response.data);

                    if (response.data.type && response.data.type == 'pdf') {
                        self.fileList.push({
                            url: '/img/pdf-icon.png',
                            response: {data: response.data},
                        })
                    }
                    toastr['success'](response.message, 'Success');
                } else {
                    toastr['error'](response.error, 'Error');
                }
            },
            handleUploadError() {},
            handleLinkDelete(key) {
                this.links.splice(this.links.indexOf(key), 1);
            },
            handleLinkInput() {
                if (this.inputValue) {
                    if (this.links.indexOf(this.inputValue) == -1) {
                        if (this.isURL(this.inputValue)) {
                            this.links.push(this.inputValue);
                        } else {
                            toastr['error']('Invalid URL', 'Error');
                        }
                    } else {
                        toastr['error']('This link is already exist', 'Error');
                    }
                }
                this.inputVisible = false;
                this.inputValue = '';
            },
        },
        created() {
            this.getFinalFiles();
        },
        mounted() {}
    }
</script>

<style scoped>
    #links, #files {
        text-align: center;
    }

    #links i, #files i {
        font-size: 14px;
        font-style: italic;
    }

    #links {
        padding-right: 25px;
        border-right: 1px solid rgba(0, 0, 0, 0.25);
    }

    #files {
        padding-left: 25px;
        border-left: 1px solid rgba(0, 0, 0, 0.25);
    }

    .upload-bckg {
        height: 340px;
        width: 90%;
        margin: auto;
        margin-top: 31px;
        background: #e7f7ff;
        padding: 20px 30px;
    }

    .links-bckg {
        height: 340px;
        padding: 20px 30px;
    }

    .final-upload {
        height: 240px;
        overflow-y: auto;
        background: #fff;
        padding: 10px;
        border: 1px dashed #80b4a0 !important;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .links {
        height: 240px;
        overflow-y: auto;
        padding: 10px;
        margin-bottom: 20px;
    }

    .final-upload i {
        font-size: 80px !important;
        font-style: normal !important;
    }

    .exiting-files {
        padding: 10px;
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }

    .exiting-files img {
        width: 100px;
        height: 100px;
        object-fit: cover;
    }

    .exiting-files i {
        font-size: 20px !important;
        font-style: normal !important;
    }

    .el-upload-list__item {
        height: 100px !important;
        width: 100px !important;
    }

    .el-dialog__header {
        padding-top: 40px !important;
    }

</style>