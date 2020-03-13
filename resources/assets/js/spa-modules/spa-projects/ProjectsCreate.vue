<template>
    <div>
        <el-row v-if="chooseProjectType">
            <el-col>
                <el-row class="choose-project-hader">
                    <el-alert
                            title=""
                            type="info"
                            center>
                        <h4>
                            CREATE NEW PROJECT BY CHOOSING PROJECT TYPE
                        </h4>
                        <h5>
                            Click on the icon below to create the type of project you want to send to client
                        </h5>
                    </el-alert>
                </el-row>
                <el-row class="choose-project-body" type="flex" align="middle" justify="center">
                    <el-col :xs="24" :sm="6" :md="8" :lg="6" :xl="4">
                        <div class="w3-hover-shadow w3-center" style="margin-bottom: 10px">
                            <div class="rollover">
                                <div class="rollover-body">
                                    <el-button @click="chooseProject('design')" class="roll-btn">CREATE DESIGN PROJECT
                                    </el-button>
                                </div>
                                <div class="text-center" style="position: absolute; bottom: 20px;">
                                    <div class="type-body">
                                        <span class="type-info">
                                            <strong>Used for print design, digital banners, large print design and more</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="w3-shadow-body">
                                <div class="img-thumb"
                                     v-bind:style="{ 'background-image': 'url(' + '/img/design-thumbnail.png' + ')' }">
                                </div>
                                <div class="text-center">
                                    <div style="margin-top: 25px">
                                        <span class="type-title">
                                            <strong>GRAPHIC DESIGN</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </el-col>

                    <el-col :xs="24" :sm="6" :md="8" :lg="6" :xl="4">
                        <div class="w3-hover-shadow w3-center" style="margin-bottom: 10px">
                            <div class="rollover">
                                <div class="rollover-body">
                                    <el-button @click="chooseProject('website')" class="roll-btn">CREATE WEBSITE PROJECT
                                    </el-button>
                                </div>
                                <div class="text-center" style="position: absolute; bottom: 20px; width: 100%">
                                    <div class="type-body">
                                        <span class="type-info">
                                            <strong>Used for live website url links</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="w3-shadow-body">
                                <div class="img-thumb"
                                     v-bind:style="{ 'background-image': 'url(' + '/img/website-thumbnail.png' + ')' }">
                                </div>

                                <div class="text-center">
                                    <div style="margin-top: 25px">
                                        <span class="type-title">
                                            <strong>WEBSITE DESIGN</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </el-col>
                    <el-col :xs="24" :sm="6" :md="8" :lg="6" :xl="4">
                        <div class="w3-hover-shadow w3-center" style="margin-bottom: 10px">
                            <div class="rollover">
                                <div class="rollover-body">
                                    <el-button @click="chooseProject('video')" class="roll-btn">CREATE VIDEO PROJECT
                                    </el-button>
                                </div>
                                <div class="text-center" style="position: absolute; bottom: 20px; width: 100%">
                                    <div class="type-body">
                                        <span class="type-info">
                                            <strong>Used for live video url links</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="w3-shadow-body">
                                <div class="img-thumb"
                                     v-bind:style="{ 'background-image': 'url(' + '/img/video-thumbnail.jpg' + ')' }">
                                </div>

                                <div class="text-center">
                                    <div style="margin-top: 25px">
                                        <span class="type-title">
                                            <strong>VIDEOGRAPHY</strong>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </el-col>
                </el-row>
            </el-col>
            <el-col style="margin-top: 30px">
                <el-row type="flex" align="middle" justify="center">
                    <router-link to="/projects">
                        <el-button>Cancel</el-button>
                    </router-link>
                </el-row>
            </el-col>
        </el-row>
        <el-row v-else>
            <el-col>
                <el-form :model="formData" status-icon :rules="rules" ref="ProjectForm">
                    <el-form-item label="Company Name" prop="company">
                        <el-autocomplete type="text" v-model="formData.company"
                                         :fetch-suggestions="querySearchCompanies"
                                         style="width: 100%" @select="handleCompanySelect">
                            <template slot-scope="{ item }">
                                <div style="position:relative;">
                                    <div class="value">{{ item.value }}</div>
                                    <span class="link">Approver: {{ item.clientName }}</span>
                                    <br>
                                    <span class="link">Email: {{ item.clientEmail }}</span>
                                    <i v-if="item.owner" class="el-icon-close disable-autocomplete-data"></i>
                                </div>
                            </template>
                        </el-autocomplete>
                    </el-form-item>
                    <el-form-item label="Project Name" prop="name">
                        <el-input v-model="formData.name"></el-input>
                    </el-form-item>
                    <el-form-item v-if="projectType == 'website'" label="Website URL" prop="website_url">
                        <el-input type="text" v-model="formData.website_url"></el-input>
                    </el-form-item>
                    <el-form-item v-if="projectType == 'video'" label="Video URL" prop="video_url">
                        <span class="video_info_link">Copy and paste a URL from youtube or dropbox. Copy the shared link from google drive. <a href="http://prooflo.com/video-projects/" target="_blank">To Learn more click here. <i class="el-icon-question"></i></a></span>
                        <el-input type="text" v-model="formData.video_url"></el-input>
                    </el-form-item>
                    <el-form-item label="Approver Name" prop="client_name">
                        <el-autocomplete type="text" v-model="formData.client_name"
                                         :fetch-suggestions="querySearchNames"
                                         style="width: 100%" @select="handleClientNameSelect">
                            <template slot-scope="{ item }">
                                <div style="position: relative;">
                                    <div class="value">{{ item.value }}</div>
                                    <span class="link">Company: {{ item.projectCompany }}</span>
                                    <br>
                                    <span class="link">Email: {{ item.clientEmail }}</span>
                                    <i v-if="item.owner" class="el-icon-close disable-autocomplete-data"></i>
                                </div>
                            </template>
                        </el-autocomplete>
                    </el-form-item>
                    <el-form-item label="Approver Email" prop="email">
                        <el-autocomplete v-model="formData.email"
                                         :fetch-suggestions="querySearchEmails"
                                         style="width: 100%" @select="handleClientEmailSelect">
                            <template slot-scope="{ item }">
                                <div class="value">{{ item.value }}</div>
                                <span class="link">Approver: {{ item.clientName }}</span>
                                <br>
                                <span class="link">Company: {{ item.projectCompany }}</span>
                            </template>
                        </el-autocomplete>
                    </el-form-item>

                    <!--Collaborators-->
                    <p style="font-size: 14px; font-weight: bold">Collaborators</p>
                    <p style="font-style: italic">Please add other collaborators here by email</p>
                    <el-tag
                            :key="collaborator"
                            v-for="collaborator in formData.collaborators"
                            :disable-transitions="false"
                            :closable="true"
                            @close="handleClose(collaborator)">
                        {{collaborator}}
                    </el-tag>
                    <el-input
                            class="input-new-tag"
                            v-if="inputVisible"
                            v-model="inputValue"
                            ref="saveTagInput"
                            size="mini"
                            @keyup.enter.native="handleInputConfirm"
                            @blur="handleInputConfirm"
                    >
                    </el-input>
                    <el-button v-else class="button-new-tag" size="small" @click="showInput">+ New Collaborator
                    </el-button>
                    <el-form-item style="text-align: center; margin-top: 20px">
                        <el-button type="primary" @click="submitForm('ProjectForm')" :loading="saveLoading">Continue
                        </el-button>
                        <el-button @click="cancel('ProjectForm')">Cancel</el-button>
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import {validationMixin} from 'vuelidate'
    import { mapGetters, mapActions } from 'vuex'
    import Ls from '../../services/ls'

    export default {
        mixins: [validationMixin],
        data() {
            return {
                formData: {
                    name: '',
                    company: '',
                    website_url: '',
                    video_url: '',
                    client_name: '',
                    email: '',
                    type: '',
                    collaborators: []
                },
                rules: {
                    name: [
                        {required: true, message: 'Project name is required', trigger: 'change'},
                    ],
                    company: [
                        {required: true, message: 'Company name is required', trigger: 'change'}
                    ],
                    client_name: [
                        {required: true, message: 'Client name is required', trigger: 'change'},
                    ],
                    email: [
                        {required: true, message: 'Client email is required', trigger: 'change'},
                        {type: 'email', message: 'Client email is not valid', trigger: 'change'}
                    ]
                },

                fileList: [],
                headers: {},
                saveLoading: false,
                project_id: '',
                revision_id: '',
                user_id: '',
                chooseProjectType: true,
                projectType: '',
                inputVisible: false,
                inputValue: '',
            }
        },

        mounted() {

        },
        methods: {
            chooseProject(type) {
                this.projectType = type;
                this.formData.type = type;
                if (type == 'website') {
                    this.rules.website_url = [
                        {required: true, message: 'Website URL is required', trigger: 'blur'},
                        {type: 'url', required: true, message: 'Not valid URL', trigger: 'blur'}
                    ]
                }
                if (type == 'video') {
                    this.rules.video_url = [
                        {required: true, message: 'Video URL is required', trigger: 'blur'},
                        {type: 'url', required: true, message: 'Not valid URL', trigger: 'blur'}
                    ]
                }
                this.chooseProjectType = false;
            },
            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.saveLoading = true
                        axios.post('/api/projects/create', this.formData).then(response => {
                            if (response.data.status == 1) {
                                if (response.data.data.newClient) {
                                    // Identify new user on Gist
                                    convertfox.identify(response.data.data.client.id, {
                                        email: response.data.data.client.email,
                                        name: response.data.data.client.name
                                    });

                                    // Trigger 'Project Invite' event
                                    convertfox.track("Project Invite", {
                                        userId: response.data.data.client.id,
                                        projectId: response.data.data.project_id
                                    });
                                }

                                // Identify logged in user on Gist
                                convertfox.identify(this.user.id, {
                                    email: this.user.email,
                                    name: this.user.name
                                });

                                // Trigger 'Project Created' event
                                convertfox.track("Project Created", {
                                    userId: response.data.data.user_id,
                                    projectId: response.data.data.project_id
                                });

                                this.saveLoading = false;
                                this.project_id = response.data.data.project_id;
                                this.revision_id = response.data.data.revision_id;
                                this.user_id = response.data.data.user_id;

                                toastr['success'](response.data.message, 'Success')
                                this.resetForm('ProjectForm');

                                if(response.data.data.project_type == 'video'){

                                    axios.get('/api/projects/send_project/' + this.project_id + '/' + this.user_type).then(response => {
                                        this.$router.push({ path: '/projects' });
                                        this.sendLoading = false
                                        /*toastr['success'](response.data.message, 'Success')
                                        this.clearForm();*/

                                    }).catch(error => {
                                        this.sendLoading = false
                                        console.log(error.message)
                                    });

                                } else {
                                    this.$router.push({
                                        name: 'project_creative_brief',
                                        params: {project_id: this.project_id, revision_id: this.revision_id, user_id: this.user_id, project_type: this.projectType}
                                    });
                                }
                            } else {
                                toastr['error'](response.data.errors[0], 'Error')
                                this.saveLoading = false
                            }

                        }).catch(error => {
                            this.saveLoading = false
                        });
                    } else {
                        toastr['error']('Error creating project. Please fix all the listed issues')
                        return false;
                    }
                });
            },
            resetForm(formName) {
                this.$refs[formName].resetFields();
            },
            cancel(formName) {
                this.resetForm(formName);
                this.chooseProjectType = true;
            },
            handleClose(key, keyPath) {
                this.formData.collaborators.splice(this.formData.collaborators.indexOf(key), 1);
            },
            handleInputConfirm() {
                let inputValue = this.inputValue;

                if (inputValue) {
                    if (inputValue != this.formData.email) {
                        if (this.formData.collaborators.indexOf(inputValue) == -1) {
                            if (this.isEmail(inputValue)) {
                                this.formData.collaborators.push(inputValue);
                            } else {
                                toastr['error']('Invalid email', 'Error');
                            }

                        } else {
                            toastr['error']('This email is already exist', 'Error');
                        }
                    } else {
                        toastr['error']('This email is used for approver', 'Error');
                    }
                }

                this.inputVisible = false;
                this.inputValue = '';
            },
            showInput() {
                this.inputVisible = true;
                this.$nextTick(_ => {
                    this.$refs.saveTagInput.$refs.input.focus();
                });
            },
            isEmail(string) {
                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(String(string).toLowerCase())
            },
            querySearchEmails(queryString, cb) {
                var emails = this.clientEmails;
                var results = queryString ? emails.filter(this.createFilter(queryString)) : emails;
                cb(results);
            },
            querySearchNames(queryString, cb) {
                var names = this.clientNames;
                var results = queryString ? names.filter(this.createFilter(queryString)) : names;
                cb(results);
            },
            querySearchCompanies(queryString, cb) {
                var companies = this.projectCompanies;
                var results = queryString ? companies.filter(this.createFilter(queryString)) : companies;
                cb(results);
            },
            createFilter(queryString) {
                return (data) => {
                    return (data.value.toLowerCase().indexOf(queryString.toLowerCase()) === 0);
                };
            },
            handleCompanySelect(item) {
                if (event.target.className == 'el-icon-close disable-autocomplete-data') {
                    this.disableData(item, 'company');
                } else {
                    this.formData.client_name = item.clientName;
                    this.formData.email = item.clientEmail;
                }
            },
            handleClientEmailSelect(item) {
                this.formData.client_name = item.clientName;
                this.formData.company = item.projectCompany;
            },
            handleClientNameSelect(item) {
                if (event.target.className == 'el-icon-close disable-autocomplete-data') {
                    this.disableData(item, 'name');
                } else {
                    this.formData.email = item.clientEmail;
                    this.formData.company = item.projectCompany;
                }
            },
            disableData(item, field) {
                let fieldName = '';
                switch (field) {
                    case 'company':
                        fieldName = 'company name';
                        break;
                    case 'name':
                        fieldName = 'approver name';
                        break;
                }

                this.$confirm(`Are you sure you want to delete this ${fieldName}?`, 'Warning', {
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel',
                    type: 'success',
                    title: ''
                }).then(() => {
                    axios.put('/api/bootstrap/disable-autocomplete-data', item)
                        .then(response => {
                            if (response.data.status) {
                                switch (field) {
                                    case 'company':
                                        this.formData.company = '';
                                        break;
                                    case 'name':
                                        this.formData.client_name = '';
                                        break;
                                }
                                this.updateRecentData({item: item, field: field});
                            } else {
                                toastr['error'](response.data.errors, 'Error')
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            toastr['error']('Internal Server Error')
                        })
                })
            },
            ...mapActions([
                'getRecentDatas',
                'updateRecentData'
            ])
        },
        computed: {
            ...mapGetters([
                'user',
                "ownedTeam",
                'clientEmails',
                'clientNames',
                'projectCompanies',
            ])
        },
        created() {
            this.getRecentDatas();
        }
    }

    const {email, required} = require('vuelidate/lib/validators')
</script>

<style scoped>
    .el-tag + .el-tag {
        margin-left: 5px;
    }
    .choose-project-body {
        margin-top: 35px;
    }
    .value {
        text-overflow: ellipsis;
        overflow: hidden;
    }
    .link {
        font-size: 13px;
        color: #b4b4b4;
        font-style: italic;
    }
    .disable-autocomplete-data {
        position: absolute;
        top: 8px;
        right: 0;
        font-size: 15px;
    }
    @media (max-width: 500px) {
        .choose-project-body {
            display: block;
        }
        .choose-project-body .w3-hover-shadow.w3-center {
            margin-left: 0;
            margin-right: 0;
        }
    }

    .w3-hover-shadow {
        border: 1px solid #c5c3c3 !important;
        border-radius: 5px;
        position: relative;
        height: 250px;
        margin: 0 20px;
    }

    .w3-shadow-body {
        cursor: pointer;
        position: relative;
        height: 100%;
        width: 100%;
        padding: 50px;
    }

    .el-alert--info {
        text-align: center;
        background: #5fbfff;
        color: #fff;
        border-radius: 5px;
    }

    .rollover {
        width: 100%;
        height: 100%;
        z-index: 999;
        position: absolute;
        background: rgba(0, 0, 0, .6);
        opacity: 0;
        /*transition: 0.1s*/
    }

    .rollover-body {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .w3-hover-shadow:hover .rollover {
        opacity: 1;
        /*transition: 0.1s*/
    }

    .roll-btn {
        background-color: rgba(0, 0, 0, .7);
        color: #fff;
        font-weight: bold;
    }

    .img-thumb {
        width: auto;
        height: 135px;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        border-radius: 5px;
    }

    .type-title {
        margin-bottom: 5px;
        font-size: 13px;
        color: #8f9091;
    }

    .type-info {
        margin-bottom: 5px;
        font-size: 12px;
        color: #fff;
    }

    .type-body {
        padding: 0 10px;
        line-height: 1;
    }
    .video_info_link{position: absolute;top: 0;left: 87px;font-style: italic;}
    .video_info_link a{font-weight: bold;}

    @media (max-width: 550px) {
        .choose-project-hader .el-alert h4 {
            font-size: 16px;
        }
    }
</style>
