<template>
    <div v-loading.fullscreen.lock="fullscreenLoading">
        <el-row>
            <el-col>
                <el-form :model="formData" status-icon :rules="rules" ref="ProjectForm">
                    <el-form-item label="Company name" prop="company">
                        <el-input type="text" v-model="formData.company"></el-input>
                    </el-form-item>
                    <el-form-item label="Project Name" prop="name">
                        <el-input v-model="formData.name"></el-input>
                    </el-form-item>
                    <el-form-item v-if="projectType == 'website'" label="Website URL" prop="website_url">
                        <el-input type="text" v-model="formData.website_url"></el-input>
                    </el-form-item>
                    <el-form-item label="Client name" prop="client_name">
                        <el-input disabled type="text" v-model="formData.client_name"></el-input>
                    </el-form-item>
                    <el-form-item label="Client Email" prop="email">
                        <el-input disabled v-model="formData.email"></el-input>
                    </el-form-item>
                    <el-form-item style="text-align: center">
                        <el-button type="primary" @click="submitForm('ProjectForm')" :loading="saveLoading">Update
                        </el-button>
                        <router-link to="/projects">
                            <el-button>Cancel</el-button>
                        </router-link>
                    </el-form-item>
                </el-form>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import {validationMixin} from 'vuelidate'
    import Ls from '../../services/ls'

    const {email, required} = require('vuelidate/lib/validators');

    export default {
        props: ['project_id'],
        mixins: [validationMixin],
        data() {
            return {
                formData: {
                    name: '',
                    company: '',
                    website_url: '',
                    client_name: '',
                    email: '',
                    type: ''
                },
                rules: {
                    name: [
                        {required: true, message: 'Project name is required', trigger: 'blur'},
                    ],
                    company: [
                        {required: true, message: 'Company name is required', trigger: 'blur'}
                    ],
                    client_name: [
                        {required: true, message: 'Client name is required', trigger: 'blur'},
                    ],
                    email: [
                        {required: true, message: 'Client email is required', trigger: 'blur'},
                        {type: 'email', required: true, message: 'Client email is not valid', trigger: 'blur'}
                    ]
                },

                fileList: [],
                headers: {},
                saveLoading: false,
                user_id: '',
                projectType: '',
                fullscreenLoading: false,
        }
        },

        methods: {
            submitForm(formName) {
                this.$refs[formName].validate((valid) => {
                    if (valid) {
                        this.saveLoading = true;

                        axios.put('/api/projects/update/' + this.project_id, this.formData).then(response => {
                            if (response.data.status == 1) {
                                console.log(response.data.data);
                                this.saveLoading = false;

                                toastr['success'](response.data.message, 'Success');
                                this.$router.push({path: '/projects'});
                            } else {
                                toastr['error'](response.data.errors[0], 'Error');
                                this.saveLoading = false
                            }

                        }).catch(error => {
                            this.saveLoading = false
                        });
                    } else {
                        toastr['error']('Error updating project. Please fix all the listed issues')
                        return false;
                    }
                });
            }
        },
        created() {
            this.fullscreenLoading = true;
            // Get project details
            axios.get('/api/projects/details/' + this.project_id)
                .then(response => {
                    if (response.data.status == 1) {
                        this.fullscreenLoading = false;

                        let project = response.data.data.project;
                        let client = response.data.data.client;

                        // Fill project details into form
                        this.projectType = project.type;
                        this.formData.name = project.name;
                        this.formData.company = project.company;
                        this.formData.website_url = project.website_url;
                        this.formData.client_name = client.name;
                        this.formData.email = client.email;
                        this.formData.type = project.type;

                        // Setting rule form website project
                        if (project.type == 'website') {
                            this.rules.website_url = [
                                {required: true, message: 'Website URL is required', trigger: 'blur'},
                                {type: 'url', required: true, message: 'Not valid URL', trigger: 'blur'}
                            ]
                        }

                    } else {
                        toastr['error'](response.data.errors[0], 'Error');
                        this.$router.push({path: '/projects'});
                    }
                })
        }
    }
</script>

<style scoped>
    .choose-project-body {
        margin-top: 35px;
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
</style>