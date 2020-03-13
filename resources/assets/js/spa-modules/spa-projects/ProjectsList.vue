<template>
    <div v-loading.fullscreen.lock="fullscreenLoading">
        <el-row style="margin-bottom: 10px">
            <el-alert
                    title=""
                    type="info"
                    class="header-alert"
                    center>

                <!--Freelancer bubbles-->
                <p v-if="current_rol == 'freelancer'">
                    <span v-if="showByStatus == 0">
                        <b>Project List:</b> This section is for all projects currently in your dashboard.
                    </span>

                    <span v-else-if="showByStatus == 1">
                        <b>In Progress:</b> This section is for projects you are currently working on.
                    </span>

                    <span v-else-if="showByStatus == 2">
                        <b>In Review:</b> This section is for projects being reviewed by your client.  You will be notified when the client has made corrections.
                    </span>

                    <span v-else-if="showByStatus == 3">
                        <b>Approved:</b> This section is for projects that have been approved by the client.
                    </span>

                    <span v-else-if="showByStatus == 4">
                        <b>Final Files:</b> This section contains final files for approved projects.
                    </span>

                    <span v-else-if="showByStatus == 5">
                        <b>In Draft:</b> This section is for projects that are waiting for you to be sent to your clients.
                    </span>

                    <span v-else-if="showByStatus == 6">
                        <b>On Hold:</b> This section is for projects that are not being worked on at the moment and are on hold.
                    </span>

                    <span v-else>
                        <b>{{current_member}}'s Projects:</b> This section is for projects that {{current_member}} are working on.
                    </span>
                </p>

                <!--Client bubbles-->
                <p v-if="current_rol == 'client' || current_rol == 'collaborator'">
                    <span v-if="showByStatus == 0">
                        <b>Project List:</b> This section is for all projects currently in your dashboard.
                    </span>

                    <span v-else-if="showByStatus == 1">
                        <b>In Review:</b> This section is for projects currently being worked on by your designer. {{current_rol == 'client' ? 'You will be notified when there are revisions for review.' : ''}}
                    </span>

                    <span v-else-if="showByStatus == 2">
                        <span v-if="current_rol == 'client'">
                            <b>In Progress:</b> This section is for projects awaiting your revisions. Please review and approve or send back to the designer for new revisions.
                        </span>
                        <span v-else-if="current_rol == 'collaborator'">
                            <b>In Progress:</b> This section is for projects awaiting client revisions.
                        </span>
                    </span>

                    <span v-else-if="showByStatus == 3">
                        <b>Approved:</b> This section is for projects that have been approved. {{current_rol == 'client' ? 'You will be notified when final files are available.' : ''}}
                    </span>

                    <span v-else-if="showByStatus == 4">
                        <b>Final Files:</b> This section contains final files for approved projects.
                    </span>

                    <span v-else-if="showByStatus == 6">
                        <b>On Hold:</b> This section is for projects that are not being worked on at the moment and are on hold.
                    </span>
                </p>
            </el-alert>
        </el-row>
        <el-row type="flex" align="middle" style="margin-bottom: 10px; padding: 5px" class="list-buttons">
            <el-col :md="23">
                <template v-if="projects_listing_type == 'list' && listing == 'all' && isFreelancer && isSubscribed">
                    <router-link to="/projects/create" style="text-decoration: none; height: 100%">
                        <el-button type="primary">
                            <i class="el-icon-plus"></i> New Project
                        </el-button>
                        <!--<span id="list-new-project">-->
                            <!--<i class="el-icon-plus"></i> <b>New Project</b>-->
                        <!--</span>-->
                    </router-link>
                </template>
            </el-col>
            <el-col :md="1" :offset="(projects_listing_type == 'card' || listing != 'all' || !isFreelancer) ? 23 : 0">
                <el-row type="flex" class="view-type" align="middle">
                    <i class="el-icon-menu" @click="changeProjectsListingType('card')"
                       :class="{'active-type' : projects_listing_type == 'card'}"></i>
                    <i class="el-icon-tickets" @click="changeProjectsListingType('list')"
                       :class="{'active-type' : projects_listing_type == 'list'}"></i>
                </el-row>
            </el-col>
        </el-row>
        <el-row v-if="!isSubscribed && isFreelancer" style="margin-bottom: 10px">
            <el-alert title="" type="error" center>
                <b>Your subscription as a freelancer is expired and access to your projects has been denied. Please <a href="/settings#/subscription">upgrade your subscription</a> to access your projects</b>
            </el-alert>
        </el-row>
        <el-row v-if="projects_listing_type == 'card'" :gutter="10">
            <div class="project-list">
                <el-col v-show="listing == 'all' &&  isFreelancer && isSubscribed" :xs="24" :sm="6" :md="8" :lg="6" :xl="4" class="new-project">
                    <router-link to="/projects/create" class="add-project-block">
                        <i class="el-icon-plus color-theme"></i>
                        <span>New Project</span>
                    </router-link>
                </el-col>
                <el-col :xs="24" :sm="6" :md="8" :lg="6" :xl="4" v-for="(project, index, key) in current_projects"
                        :key="key" style="padding: 0 5px;">
                    <div class="w3-hover-shadow w3-center" style="margin-bottom: 10px; min-height: 245px" :class="{'has-new-actions-card': project.unreadComments > 0 || project.viewedByUser == 0, 'unactive' : !project.active}">
                        <div style="cursor: pointer; position: relative;">
                            <div class="project-role">
                                <span v-if="project.team == ''">{{project.role | capitalize}}</span>
                                <span v-else>Team - {{project.team | capitalize}}</span>
                            </div>
                            <el-row class="rollover">
                                <div v-if="project.owner == user.id && project.active" class="delete-btn">
                                    <el-dropdown @command="handleDropdownCommand" trigger="click">
                                        <i :id="'cardIcon' + index" class="el-icon-more icon-center"
                                           style="font-size: 24px; color: #faf9f9"></i>
                                        <el-dropdown-menu slot="dropdown">
                                            <!--Rename-->
                                            <el-dropdown-item
                                                    v-if="project.status != 'completed'"
                                                    :command="{action: 'rename', project_id: project.id}">
                                                Rename Project
                                            </el-dropdown-item>

                                            <!--Delete-->
                                            <el-dropdown-item :command="{action: 'delete', project_id: project.id}">
                                                Delete
                                            </el-dropdown-item>

                                            <!--Email-->
                                            <el-dropdown-item
                                                    v-if="project.status != 'draft'"
                                                    :command="{action: 'email', project_id: project.id}">
                                                Email Client
                                            </el-dropdown-item>

                                            <!--Move to In Progress-->
                                            <el-dropdown-item
                                                    v-if="project.status == 'approved' || project.status == 'completed' || project.status == 'hold'"
                                                    :command="{action: 'moveToInProgress', project_id: project.id, project_type: project.type, status: 'progress'}">
                                                Move to In Progress
                                            </el-dropdown-item>

                                            <!--Move to On Hold-->
                                            <el-dropdown-item
                                                    v-if="project.status == 'progress' || (project.type == 'website' && project.status == 'revision')"
                                                    :command="{action: 'moveToOnHold', project_id: project.id, project_type: project.type, status: 'hold'}">
                                                Move to On Hold
                                            </el-dropdown-item>

                                            <!--Manage Team Members-->
                                            <el-dropdown-item
                                                    v-if="ownedTeam"
                                                    :command="{action: 'addTeamMember', project: project}">
                                                Add Team Member
                                            </el-dropdown-item>
                                            <!--Creative Brief-->
                                            <el-dropdown-item
                                                    :command="{action: 'creativeBrief', project: project}">
                                                View Creative Brief
                                            </el-dropdown-item>
                                        </el-dropdown-menu>
                                    </el-dropdown>
                                </div>
                                <div v-else-if="project.role == 'client'" class="delete-btn">
                                    <el-dropdown @command="handleDropdownCommand" trigger="click">
                                        <i :id="'cardIcon' + index" class="el-icon-more icon-center"
                                           style="font-size: 24px; color: #faf9f9"></i>
                                        <el-dropdown-menu slot="dropdown">
                                            <!--Creative Brief-->
                                            <el-dropdown-item
                                                    :command="{action: 'creativeBrief', project: project}">
                                                View Creative Brief
                                            </el-dropdown-item>
                                        </el-dropdown-menu>
                                    </el-dropdown>
                                </div>
                                <template v-if="project.active">
                                    <el-button v-if="project.status != 'draft'" class="roll-btn"
                                               @click="openProofer(project.id, project.active_revision.id, project)">VIEW PROJECT
                                    </el-button>
                                    <el-button v-else-if="project.status == 'draft' && current_rol == 'freelancer'" class="roll-btn"
                                               @click="addPictures(project.id, project.active_revision.id, project.creative_brief)">UPLOAD FILES
                                    </el-button>
                                </template>
                                <template v-else>
                                    <el-button class="expired">OWNER SUBSCRIPTION IS EXPIRED</el-button>
                                </template>
                                <el-col :md="24" class="project-team-members">
                                    <el-row type="flex" justify="center">
                                        <el-col v-if="project.teamMembers.length" v-for="member in project.teamMembers" :key="member.id"
                                                :xs="5" :md="5">
                                            <div style="padding: 5px">
                                                <el-tooltip :content="member.name" placement="bottom" effect="light">
                                                    <img :src="member.photo_url"
                                                         class="img-circle special-img"
                                                         style="width: 40px;height: 40px;">
                                                </el-tooltip>
                                            </div>
                                        </el-col>
                                    </el-row>
                                </el-col>
                            </el-row>

                            <div v-if="project.active_revision.proofs.length"
                                 class="img-thumb"
                                 v-bind:style="{ 'background-image': 'url(' + '/storage/' + project.active_revision.proofs[0].project_files.path + ')'}"></div>
                            <div v-else-if="current_rol == 'client' && !project.active_revision.proofs.length && project.type != 'website'"
                                 class="img-thumb">
                                <el-row type="flex" justify="center" align="middle" style="height: 100%">
                                    <el-col class="draft-project">
                                        <i class="el-icon-info"></i>
                                        <h4>Your design will be available soon.</h4>
                                    </el-col>
                                </el-row>
                            </div>
                            <div v-else class="img-thumb"
                                 v-bind:style="{ 'background-image': 'url(' + '/img/placeholder.jpg' + ')' }">
                            </div>

                            <div class="w3-container text-left">
                                <div class="project-name" style="margin-top: 10px">
									<span style="margin-bottom: 2px; font-size: 16px;">
										<strong>{{ project.name }}</strong>
									</span>
                                </div>
                                <div class="company">{{ project.company }}</div>
                            </div>
                        </div>

                        <div class="w3-container w3-center"
                             style="padding-left: 5px; padding-right: 5px">
                            <template v-if="project.active_revision.proofs.length > 0">
                                <el-row style="padding: 10px 0;">
                                    <el-col v-if="project.type == 'design'" :span="5" style="padding: 0 5px">
                                        <el-tag size="small"
                                                style="background-color: #545c64; border-radius: 30px; color: #fff"
                                                title="Current revision">
                                            <b>R - {{project.active_revision.version}}</b>
                                        </el-tag>
                                    </el-col>

                                    <el-col :span="(project.type == 'design') ? 19 : 24" style="padding: 0 5px">

                                        <div class="counters">
                                            <div v-if="getReviewsOfProject(project) > 0" class="review-counter" >{{ getReviewsOfProject(project) }}</div>
                                            <div v-if="project.unreadComments > 0" class="comment-counter" >{{project.unreadComments}}</div>
                                        </div>
                                        
                                        <el-progress :text-inside="true"
                                                     :stroke-width="25"
                                                     :percentage="project.percentage"
                                                     :color="(project.percentage == 100) ? '#80B4A0' : '#fa5555'"
                                                     >
                                        </el-progress>
                                        <div style="text-align: center">
                                            <p style="font-size: 12px; margin-bottom: 0;"><b>{{project.solved_issues}} OF {{project.total_issues}}</b> {{project.type == 'design' ? 'CORRECTIONS' : 'ISSUES'}} COMPLETED</p>
                                        </div>
                                    </el-col>

                                    <!--<el-col :md="(project.type == 'website') ? 8 : 5" style="padding: 5px; position: relative" :class="{'web-group' : project.type == 'website'}">-->
                                            <!--<div v-if="project.collabCount > 0" class="comment-counter" :class="{'design-collab-count' : project.type == 'design'}" style="right: 15px; top: -15px; background: #949292">-->
                                                <!--{{project.collabCount}}-->
                                            <!--</div>-->
                                            <!--<i class="fa fa-group icon-center" style="font-size: 23px; color: #545c64"></i>-->
                                    <!--</el-col>-->

                                    <!--<el-col v-if="project.type == 'design'" :md="5" style="padding: 5px">-->
                                        <!--<div v-if="project.unreadComments > 0" class="comment-counter" style="right: -2px; top: 6px;">-->
                                            <!--{{project.unreadComments}}-->
                                        <!--</div>-->
                                        <!--<el-button type="text"-->
                                                   <!--@click="openProofer(project.id, project.active_revision.id)"-->
                                                   <!--size="small" style="padding-top: 20px"-->
                                                   <!--title="Issues">-->
                                            <!--<el-tag size="small"-->
                                                    <!--style="background-color: #545c64; border-radius: 30px; color: #fff; position:relative; min-width: 42px"-->
                                                    <!--title="Issues">-->
                                                <!--<i class="fa fa-comment icon-center"-->
                                                   <!--style="font-size: 30px; color: #545c64; position: absolute;left: -1px;top: -3px;"></i>-->
                                            <!--</el-tag>-->

                                            <!--<div style="position: relative; right: 0; top: -20px; color: #fff">-->
                                                <!--{{ project.total_issues }}-->
                                            <!--</div>-->
                                        <!--</el-button>-->
                                    <!--</el-col>-->
                                    <!-- <el-col :md="5" style="padding: 5px">
                                        <button class="edition-btn" @click="" style="background-color: #f3f1f1" title="Project info">
                                            <i class="el-icon-info icon-center" style="font-size: 20px ;color: #797878"></i>
                                        </button>
                                    </el-col> -->
                                </el-row>
                            </template>
                            <template v-else>
                                <el-row style="padding-bottom: 15px; margin-top: 5px">
                                    <el-col :xs="24" style="padding: 5px">
                                        <button v-show="current_rol == 'freelancer'" class="edition-btn"
                                                @click="addPictures(project.id, project.active_revision.id)"
                                                style="background-color: #fc7c7c; border: 0px" title="Add proofs">
                                            <i class="el-icon-plus icon-center"
                                               style="font-size: 20px ;color: #fff; font-weight: bold"></i>
                                        </button>
                                    </el-col>
                                </el-row>
                            </template>
                            <!-- <el-button v-if="project.image" @click="openRevisions(project.id, project.name)" type="primary" size="mini" round>Open in proofer</el-button>
                                <el-button v-if="project.active_revision.proofs.length > 0" @click="openProofer(project.id, project.active_revision.id)"
                                 type="primary" size="mini">Open in proofer</el-button>
                                <el-button v-else @click="addPictures(project.id, project.active_revision.id)" type="warning" size="mini">Add proofs</el-button>
                                <el-button v-if="project.status != 'approved' && project.active_revision.proofs.length > 0 && current_rol == 'freelancer'" @click="sendProject(project.id)"
                                 icon="el-icon-message" type="warning" size="mini">Send</el-button> -->
                        </div>
                    </div>
                    <!--<div class="project-block">
                        <div v-if="current_projects.length > 0" class="image" v-bind:style="{ 'background-image': 'url(' + '/storage/' + project.active_revision.proofs[0].project_files.path + ')'}">
                        </div>
                        <div v-else class="image" v-bind:style="{ 'background-image': 'url(' + '/assets/front/img/placeholder.jpg' + ')' }">
                        </div>
                        <div class="project-details">
                            <div>
                                <span class="project-name">{{ project.name }}</span>
                                <span class="company" style="float: right">{{ project.company }}</span>
                            </div>
                            <hr>
                            <div>Active revision: {{project.active_revision.version}}</div>
                            <hr>
                            <div style="text-align: center">
                                 <el-button v-if="project.image" @click="openRevisions(project.id, project.name)" type="primary" size="mini" round>Open in proofer</el-button>
                                <el-button v-if="project.active_revision.proofs.length > 0" @click="openProofer(project.id, project.active_revision.id)" type="primary"
                                 size="mini">Open in proofer</el-button>
                                <el-button v-else @click="addPictures(project.id, project.active_revision.id)" type="warning" size="mini">Add proofs</el-button>
                                <el-button v-if="project.status != 'approved' && project.active_revision.proofs.length > 0" @click="sendProject(project.id)"
                                 icon="el-icon-message" type="warning" size="mini">Send</el-button>
                            </div>
                        </div>
                    </div> -->
                </el-col>

            </div>
        </el-row>

        <!--LIST TYPE-->
        <el-row v-else-if="projects_listing_type == 'list'" :gutter="10">
            <div class="project-list" id="list">
                <table class="table table-hover table-responsive">
                    <thead>
                        <tr style="text-align: center">
                            <th scope="col">Client</th>
                            <th scope="col">Project</th>
                            <th scope="col">Email</th>
                            <th scope="col">Thumbnail</th>
                            <th scope="col">Team</th>
                            <th scope="col">Collaborators</th>
                            <th scope="col">Revision Round</th>
                            <th scope="col">Progress</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(project, index, key) in current_projects"
                            :key="project.id"
                            :class="{'has-new-actions-list': project.unreadComments > 0 || project.viewedByUser == 0, 'unactive' : !project.active}">
                            <!--Client-->
                            <td>{{ project.client ? project.client.name : ''}}</td>
                            <!--Project Name-->
                            <td class="project-name" @click="(project.status == 'draft' && current_rol == 'freelancer')
                            ? addPictures(project.id, project.active_revision.id)
                            : (project.status != 'draft') ? openProofer(project.id, project.active_revision.id, project) : ''">
                                {{project.name}}
                            </td>
                            <!--Client Email-->
                            <td>{{project.client ? project.client.email : ''}}</td>
                            <!--Thumbnail-->
                            <td>
                                <img v-if="project.active_revision.proofs.length" :src="'/storage/' + project.active_revision.proofs[0].project_files.path" alt="">
                                <el-row v-else-if="current_rol == 'client' && !project.active_revision.proofs.length" type="flex" justify="center" align="middle" style="height: 100%">
                                    <el-col class="list-draft-project">
                                        <i class="el-icon-info"></i>
                                        <h5>Your design will be available soon.</h5>
                                    </el-col>
                                </el-row>
                                <img v-else src="/img/placeholder.jpg" alt="">
                            </td>
                            <!--Team-->
                            <td width="150px">
                                <template v-if="project.teamMembers.length">
                                    <el-row type="flex" justify="center" class="list-team-members">
                                        <el-col v-for="(member, index) in project.teamMembers" :key="member.id"
                                                :xs="6" :md="6" class="member-thumbnail" :style="'z-index' + index">
                                                <el-tooltip :content="member.name" placement="bottom" effect="light">
                                                    <img :src="member.photo_url"
                                                         class="img-circle special-img"
                                                         style="width: 100%;height: 100%;">
                                                </el-tooltip>
                                        </el-col>
                                    </el-row>
                                </template>
                                <template v-else>
                                    ---
                                </template>
                            </td>
                            <!--Collaborators-->
                            <td>
                                <div style="position: relative; padding: 10px">
                                    <div v-if="project.collabCount > 0" class="comment-counter" style="right: 27px; top: -10px; background: #949292">
                                        {{project.collabCount}}
                                    </div>
                                    <i class="fa fa-group icon-center" style="font-size: 23px; color: #545c64"></i>
                                </div>
                            </td>
                            <!--Revision Round-->
                            <td>
                                <template v-if="project.type != 'website'">
                                    <el-tag size="small"
                                            style="background-color: #545c64; border-radius: 30px; color: #fff"
                                            title="Current revision">
                                        <b>R - {{project.active_revision.version}}</b>
                                    </el-tag>
                                </template>
                                <template v-else>
                                    ---
                                </template>
                            </td>
                            <!--Progress-->
                            <td>
                                <div class="list-progress">
                                    <div style="position: relative; width: 90%">
                                        <div v-if="project.unreadComments > 0" class="comment-counter list-counter" >{{project.unreadComments}}</div>
                                        <el-progress :text-inside="true"
                                                     :stroke-width="25"
                                                     :percentage="project.percentage"
                                                     :color="(project.percentage == 100) ? '#80B4A0' : '#fa5555'"
                                                     style="margin-top: 26px">
                                        </el-progress>
                                        <div style="text-align: center">
                                            <p style="font-size: 12px;"><b>{{project.solved_issues}} OF {{project.total_issues}}</b> {{project.type == 'design' ? 'CORRECTIONS' : 'ISSUES'}} COMPLETED</p>
                                        </div>
                                    </div>
                                    <div v-if="current_rol == 'freelancer'" class="delete-btn-list">
                                        <el-dropdown @command="handleDropdownCommand" trigger="click">
                                            <i :id="'cardIcon' + index" class="el-icon-more icon-center"
                                               style="font-size: 24px; color: #949292"></i>
                                            <el-dropdown-menu slot="dropdown">
                                                <!--Rename-->
                                                <el-dropdown-item
                                                        v-if="project.status != 'completed'"
                                                        :command="{action: 'rename', project_id: project.id}">
                                                    Rename Project
                                                </el-dropdown-item>

                                                <!--Delete-->
                                                <el-dropdown-item :command="{action: 'delete', project_id: project.id}">
                                                    Delete
                                                </el-dropdown-item>

                                                <!--Email-->
                                                <el-dropdown-item
                                                        v-if="project.status != 'draft'"
                                                        :command="{action: 'email', project_id: project.id}">
                                                    Email Client
                                                </el-dropdown-item>

                                                <!--Move to In Progress-->
                                                <el-dropdown-item
                                                        v-if="project.status == 'approved' || project.status == 'completed'"
                                                        :command="{action: 'moveToInProgress', project_id: project.id, project_type: project.type}">
                                                    Move to In Progress
                                                </el-dropdown-item>
                                                <el-dropdown-item
                                                        v-if="ownedTeam"
                                                        :command="{action: 'addTeamMember', project: project}">
                                                    Add Team Member
                                                </el-dropdown-item>
                                            </el-dropdown-menu>
                                        </el-dropdown>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </el-row>

        <!--Move to In Progress Dialog-->
        <el-dialog title="Move Project to In Progress" :close-on-click-modal="false" :close-on-press-escape="false"
                   :show-close="false" :visible.sync="showInProgressDialog"
                   @open="onOpenDialogInProgress" @close="onCloseDialogInProgress" width="30%" center class="inProgressDialog">
            <el-row>
                <el-col style="text-align: center">
                    <div class="uploader" style="width: 100%; margin-top: 0px">
                        <el-upload ref="upload" drag :data="imageFormData" action="/api/files/upload"
                                   :on-preview="handlePreview" :on-error="handleError"
                                   :on-remove="deleteFile" :on-success="handleSuccess" :auto-upload="true"
                                   :file-list="fileList" name="photos"
                                   list-type="picture">
                            <i class="el-icon-upload"></i>
                            <div class="el-upload__text">Drop file here or
                                <em>click to upload</em>
                            </div>
                            <div class="el-upload__tip" slot="tip">jpg/png files with a size less than 500kb</div>
                        </el-upload>
                    </div>
                </el-col>
            </el-row>
            <el-row>
                <el-col style="margin-top: 20px; text-align: center">
                    <hr>
                    <p>Send Proofs for next revision round</p>
                </el-col>
            </el-row>
            <el-row>
                <el-col :xs="24" :md="16" :offset="8" style="margin-top: 20px">
                    <!-- <el-button type="primary" @click="finishCreateProject()" :loading="saveLoading">Finish</el-button> -->
                    <el-button type="primary" @click="moveToInProgress()">Finish & Send</el-button>
                    <el-button @click="cancelInProgress()">Cancel</el-button>
                </el-col>
            </el-row>
        </el-dialog>

        <!--Add Team Members-->
        <el-dialog :close-on-click-modal="false" :close-on-press-escape="false"
                   :show-close="true"
                   :visible.sync="showDialogAddTeamMembers"
                   @open="onOpenDialogAddTeamMembers"
                   @close="onCloseDialogAddTeamMembers"
                   width="50%" center style="padding: 20px !important"
                   class="teammember-dialog">
            <div style="padding: 25px 25px 30px">
                <el-row type="flex" justify="center">
                    <el-col>
                        <el-row style="text-align: center">
                            <h3>Add Team Member</h3>
                        </el-row>

                        <!--Team members-->
                        <el-row style="margin-top: 20px" type="flex" justify="center">
                            <div>
                                <div v-if="teamMembers.length > 0" v-for="member in teamMembers" :key="member.id"
                                     style="margin-top: 10px">
                                    <label class="notify-label">
                                        <div style="display: flex; align-items: center">
                                            <img style="float: left" :src="member.photo_url"
                                                 class="img-circle special-img team-circle">
                                            <span style="margin-left: 20px">{{member.email}}</span>
                                        </div>
                                    </label>
                                    <div style="float: right; margin-left: 20px">
                                        <label class="switch">
                                            <input type="checkbox" name="new_project"
                                                   :id="member.id"
                                                   @change="modifyMemberAccess"
                                                   :checked="current_project.users.indexOf(member.id) >= 0">
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
                        <el-button type="primary" @click="closeAddTeamMemberDialog">
                            Close
                        </el-button>
                    </el-col>
                </el-row>
            </div>
        </el-dialog>

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
                <el-button @click="showDialogCreativeBrief = false" class="cancel-btn">Cancel</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
    import {mapActions, mapGetters} from "vuex";

    export default {
        data() {
            return {
                current_projects: [],
                user_type: "freelancer",
                fullscreenLoading: false,
                current_rol: 'freelancer',
                user_id: '',
                showInProgressDialog: false,
                imageFormData: {
                    project_id: "",
                    file_type: "picture",
                    owner_type: "",
                },
                fileList: [],
                savedFiles: [],
                project_id: '',
                showDialogAddTeamMembers: false,
                showDialogCreativeBrief: false,
                currentProject: null,
                creativeBrief: '',
                editorOption: {

                },
                current_project: {
                    users: []
                }
            };
        },
        methods: {
            handleDropdownCommand(command) {
                if (command.action == 'email') {
                    this.sendProject(command.project_id)
                } else if (command.action == 'delete') {
                    this.deleteProject(command.project_id)
                } else if (command.action == 'rename') {
                    this.renameProject(command.project_id);
                } else if (command.action == 'moveToInProgress') {
                    if (command.project_type == 'design') {
                        this.openInProgressDialog(command.project_id);
                    } else if (command.project_type == 'website') {
                        this.changeProjectStatus(command.project_id, command.status);
                    }
                } else if (command.action == 'moveToOnHold') {
                    this.changeProjectStatus(command.project_id, command.status)
                } else if (command.action == 'addTeamMember') {
                    this.openAddTeamMemberDialog(command.project);
                } else if (command.action == 'creativeBrief') {
                    this.openCreativeBriefModal(command.project)
                }
            },
            addPictures(project_id, revision_id, creative_brief) {
                this.$router.push({name: 'upload_files', params: {project_id: project_id, revision_id: revision_id, user_id: this.user.id, creative_brief: creative_brief}})
            },
            openProofer(project_id, revision_id, project) {
                this.$router.push({name: 'proofer', params: {project_id: project_id, revision_id: revision_id, project: project}})
            },
            sendProject(project_id) {
                var self = this;
                this.$confirm('Are you ready to send this revision to the client?', 'Warning', {
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel',
                    type: 'success',
                    title: ''
                }).then(() => {
                    this.fullscreenLoading = true
                    axios.get('/api/projects/send_project/' + project_id + '/' + this.user_type).then(response => {
                        this.fullscreenLoading = false
                        this.loadProjects();
                        toastr['success'](response.data.message, 'Success')
                        this.fullscreenLoading = false
                    }).catch(error => {
                        this.fullscreenLoading = false
                        console.log(error.message)
                    });
                }).catch(() => {
                    console.log('canceled')
                });
            },
            showCardMenu(index) {
                $("#myDropdown" + index).toggleClass("show");
            },
            hideCardMenu(event) {
                var target = event.target;
                for (var i = 0; i < this.current_projects.length; i++) {
                    var str = "cardIcon" + i;
                    if (target.id !== str) {
                        $("#myDropdown" + i).removeClass("show");
                    }
                }
            },
            deleteProject(project_id) {
                var self = this;
                this.$confirm('Are you sure you want to delete this project?', 'Warning', {
                    confirmButtonText: 'Yes',
                    confirmButtonColor: "#ee5757",
                    cancelButtonText: 'Cancel',
                    type: 'warning',
                    title: ''
                }).then(() => {
                    this.fullscreenLoading = true
                    axios.delete('/api/projects/delete_project/' + project_id).then(response => {
                        this.fullscreenLoading = false
                        this.loadProjects();
                        toastr['success'](response.data.message, 'Success')
                        this.fullscreenLoading = false
                    }).catch(error => {
                        this.fullscreenLoading = false
                    });
                }).catch(() => {
                    // Canceled
                });
            },
            renameProject(project_id) {
                this.$router.push({
                    name: 'update_project',
                    params: {project_id: project_id}
                })
            },
            getReviewsOfProject(project) {
                var count = 0;
                project.active_revision.proofs.forEach(proof => {
                    proof.issues.forEach(issue => {
                        if (this.current_rol == 'freelancer') {
                            if (issue.status == 'todo') count ++;
                        }
                        if (this.current_rol == 'client') {
                            if (issue.status == 'review') count ++;
                        }
                    })
                })
                return count;
            },
            openInProgressDialog(project_id) {
                this.$confirm('Are you sure you want to move this project to In Progress?', 'Warning', {
                    confirmButtonText: 'Yes, move',
                    cancelButtonText: 'Cancel',
                    type: 'success',
                    title: ''
                }).then(() => {
                    this.project_id = project_id;
                    var formData = {
                        project_id: this.project_id,
                    };

                    this.fullscreenLoading = true;
                    axios
                        .post("/api/revisions/create", formData)
                        .then(response => {
                            if (response.data.status == 1) {
                                this.fullscreenLoading = false;
                                this.showInProgressDialog = true;
                                this.imageFormData.project_id = this.project_id;
                            } else {
                                this.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            this.handle_error({});
                        });
                })
            },
            openAddTeamMemberDialog(project) {
                this.project_id = project.id;
                this.current_project = project;
                this.showDialogAddTeamMembers = true;
            },
            openCreativeBriefModal(project) {
                this.creativeBrief = project.creative_brief;
                this.currentProject = project;
                this.showDialogCreativeBrief = true;
            },
            closeAddTeamMemberDialog() {
                this.project_id = '';
                this.showDialogAddTeamMembers = false
            },
            onOpenDialogAddTeamMembers() {

            },
            onCloseDialogAddTeamMembers() {

            },
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
                                this.current_project.users.push(Number(event.target.id));
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
                                console.log(this.current_project.users);
                                this.current_project.users.splice(this.current_project.users.indexOf(Number(event.target.id)), 1);
                                console.log(this.current_project.users);
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
            moveToInProgress(project_type) {
                this.fullscreenLoading = true;
                if (this.savedFiles.length > 0) {
                    axios
                        .get("/api/projects/send_project/" + this.project_id + '/' + this.current_rol)
                        .then(response => {
                            if (response.data.status == 1) {
                                this.fullscreenLoading = false;
                                this.showInProgressDialog = false;
                                this.savedFiles = [];
                                if (response.data.status == 1) {
                                    this.$alert('The revision has been sent by email successfully', '', {
                                        confirmButtonText: 'OK',
                                        callback: action => {
                                            this.loadProjects();
                                        }
                                    });
                                } else {
                                    this.fullscreenLoading = false;
                                    this.handle_error(response.data.errors);
                                }
                            } else {
                                this.fullscreenLoading = false;
                                this.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            this.handle_error({});
                        });
                    this.fullscreenLoading = false;
                } else {
                    toastr['error']('You should upload some proofs, before creating new revision round', 'Error');
                    this.fullscreenLoading = false;
                }
            },
            changeProjectStatus(project_id, status) {
                var statusText = (status == 'progress')
                    ? 'In Progress'
                    : (status == 'hold')
                        ? 'On Hold'
                        : '';
                this.$confirm('Are you sure you want to move this project to ' + statusText + '?', 'Warning', {
                    confirmButtonText: 'Yes, move',
                    cancelButtonText: 'Cancel',
                    type: 'success',
                    title: ''
                }).then(() => {
                    this.fullscreenLoading = true;
                    axios
                        .put("/api/projects/change_status/" + project_id + "/" + status)
                        .then(response => {
                            this.fullscreenLoading = false;

                            if (response.status == 200) {
                                this.$alert('Project successfully moved to ' + statusText, '', {
                                    confirmButtonText: 'OK',
                                    callback: action => {
                                        this.loadProjects();
                                    }
                                });
                            } else {
                                toastr['error'](response.error, 'Error');
                            }
                        })
                })
            },
            onOpenDialogInProgress() {
                this.imageFormData.owner_type = "proof";
            },
            onCloseDialogInProgress() {
                this.imageFormData.owner_type = "";
                this.fileList = [];
            },
            handlePreview() {
            },
            handleRemove() {
            },
            handleError(error) {
                console.log(error);
            },
            handle_error(errors) {
                this.fullscreenLoading = false;
                var text = "Connection Error!";
                if (Object.keys(errors).length > 0) {
                    text = "";
                    for (let index in errors) {
                        text += errors[index] + "\n";
                    }
                }
                swal({
                    position: "center",
                    type: "error",
                    title: "",
                    text: text,
                    showConfirmButton: true
                });
            },
            deleteFile(file, fileList) {
                var self = this;
                this.fullscreenLoading = true;
                axios
                    .delete("/api/files/delete/" + file.response.data.id)
                    .then(response => {
                        if (response.data.status == 1) {
                            self.fullscreenLoading = false;
                        } else {
                            self.handle_error(response.data.errors);
                        }
                    })
                    .catch(error => {
                        self.handle_error({});
                    });
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
                                response: {data: element},
                            })
                        })
                    } else {
                        this.savedFiles.push(response.data);
                    }
                    toastr['success'](response.message, 'Success');
                } else {
                    toastr['error'](response.error, 'Error');
                }
            },
            cancelInProgress() {
                this.showInProgressDialog = false;
                this.fullscreenLoading = true;
                axios
                    .delete("/api/revisions/delete/" + this.project_id)
                    .then(response => {
                        if (response.data.status == 1) {
                            this.fullscreenLoading = false;
                            this.showNewRevisionDialog = false;
                            this.project_id = '';
                        } else {
                            this.handle_error(response.data.errors);
                        }
                    })
                    .catch(error => {
                        this.handle_error({});
                    });
            },
            saveCreativeBrief() {
                this.fullscreenLoading = true;
                let formData = {
                    project_id: this.currentProject.id,
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
                            this.loadProjects();
                        } else {
                            this.$notify({
                                title: 'Error',
                                message: response.data.errors,
                                type: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        this.fullscreenLoading = false;
                        this.$notify({
                            title: 'Error',
                            message: 'Something went wrong please try again later',
                            type: 'error'
                        });
                    })
            },
            ...mapActions(["loadProjects", "changeProjectsListingType"])
        },
        computed: {
            showByStatus() {
                return this.$store.state.projects.show_project_by_status;
            },
            ...mapGetters([
                "user",
                'isFreelancer',
                'isSubscribed',
                "projects",
                "in_progress",
                "on_revision",
                "approved",
                'completed',
                "listing",
                "on_draft",
                "on_hold",
                'member_projects',
                'current_member',
                'ownedTeam',
                'teamMembers',
                'projects_listing_type'
            ]),
        },
        watch: {
            projects() {
                this.fullscreenLoading = false;
                this.current_projects = this.projects;
                if (this.current_projects.length > 0) {
                    for (var i in this.current_projects) {
                        if (this.current_projects[i].role == 'freelancer') {
                            this.current_rol = this.current_projects[i].role;
                        } else if (this.current_projects[i].role == 'client') {
                            this.current_rol = this.current_projects[i].role;
                        } else if (this.current_projects[i].role == 'collaborator') {
                            this.current_rol = this.current_projects[i].role;
                        }
                        return;
                    }
                }
            },
            showByStatus() {
                if (this.showByStatus == 0) {
                    this.current_projects = this.projects;
                } else if (this.showByStatus == 1) {
                    this.current_projects = this.in_progress;
                } else if (this.showByStatus == 2) {
                    this.current_projects = this.on_revision;
                } else if (this.showByStatus == 3) {
                    this.current_projects = this.approved;
                } else if (this.showByStatus == 4) {
                    this.current_projects = this.completed;
                } else if (this.showByStatus == 5) {
                    this.current_projects = this.on_draft;
                } else if (this.showByStatus == 6) {
                    this.current_projects = this.on_hold;
                } else {
                    this.current_projects = this.member_projects;
                }
            }
        },
        mounted() {
            var self = this;
            this.$nextTick(function () {
                window.onclick = function (event) {
                    self.hideCardMenu(event);
                }
            });
            this.fullscreenLoading = true;
            setTimeout(function () {
                self.user_id = self.user.id;
                self.imageFormData.user_id = self.user_id;
            }, 1000)

        },
        filters: {
            capitalize(value) {
                if (!value) return '';
                value = value.toString();
                return value.charAt(0).toUpperCase() + value.slice(1)
            }
        },
        created() {
            /* this.fullscreenLoading = true;
            axios.get("/api/auth/getCurrentRole/" + this.project_id)
                .then(response => {
                    if (response.data.status == 1) {
                        this.current_rol = response.data.data;
                        this.fullscreenLoading = false;
                    } else {
                        this.handle_error(response.data.errors);
                    }
                })
                .catch(error => {
                    self.sendLoading = false;
                }); */
        },
        updated() {
            if (this.showByStatus == 0) {
                this.current_projects = this.projects;
            } else if (this.showByStatus == 1) {
                this.current_projects = this.in_progress;
            } else if (this.showByStatus == 2) {
                this.current_projects = this.on_revision;
            } else if (this.showByStatus == 3) {
                this.current_projects = this.approved;
            } else if (this.showByStatus == 4) {
                this.current_projects = this.completed;
            } else if (this.showByStatus == 5) {
                this.current_projects = this.on_draft;
            } else if (this.showByStatus == 6) {
                this.current_projects = this.on_hold;
            } else {
                this.current_projects = this.member_projects;
            }
        },
        afterRouteUpdate: function (to, from, next) {
            next();
        }
    };
</script>
<style>
    .el-badge__content {
        width: 28px;
    }
    .el-alert__closebtn {
        color: unset !important;
    }
    .unactive {
        opacity: .5 !important;
    }
    .expired {
        background: #F2A731 !important;
        color: #fff;
        font-weight: bold;
    }
    .expired:hover {
        color: #fff;
    }
    #list img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
    }
    th, td {
        text-align: center;
        vertical-align: middle !important;
    }
    th {
        font-weight: bold !important;
    }

    tbody tr:hover {
        background: rgba(175, 175, 175, 0.5) !important;
    }
    .view-type i {
        font-size: 25px;
    }

    .view-type i:hover {
        cursor: pointer;
        color: #3890b5;
    }

    #list-new-project {
        border: 2px solid #ccecfc;
        font-size: 14px;
        padding: 5px;
        border-radius: 10px;
    }

    #list-new-project:hover {
        cursor: pointer;
        color: #3890b5;
        background: #ccecfc;
    }
    .active-type {
        color: #3890b5;
    }
    .collab-count .el-badge__content {
        background-color: rgb(250, 85, 85) !important;
        color: #fff !important;
        border-radius: 10px;
        display: inline-block;
        font-size: 12px;
        height: 20px;
        line-height: 18px;
        padding: 0 6px;
        text-align: center;
        white-space: nowrap;
        border: 1px solid #fff;
    }
    tr td:first-child {
        border-top-left-radius: 5px;
        border-bottom-left-radius: 5px;
    }
    tr td:last-child {
        border-top-right-radius: 5px;
        border-bottom-right-radius: 5px;
    }
    .has-new-actions-card {
        border: 3px solid rgb(250, 115, 115) !important;
    }
    .has-new-actions-list {
        box-shadow: inset 0 0 0 1px rgb(250, 115, 115);
        border-radius: 5px;
    }
    tr, td {
        border: none !important;
    }

    .counters {
        position: absolute;
        top: -8px;
        right: 0px;
        z-index: 999;
        display: flex;
    }

    .comment-counter {
        background-color: rgb(250, 115, 115);
        width: 23px;
        height: 23px;
        border-radius: 30px;
        text-align: center;
        color: white;
        font-weight: 600;
        margin-left: 5px;
    }

    .review-counter {
        background-color: #e6a23c;
        width: 23px;
        height: 23px;
        border-radius: 30px;
        text-align: center;
        z-index: 98;
        color: white;
        font-weight: 600;
    }

    .list-counter {
        top: 5px !important;
        right: -5px !important;
    }

    .design-collab-count {
        right: -2px !important;
    }
    .web-margin {
        margin-top: 22px;
    }
    .project-list .el-progress-bar__outer {
        background-color: #545c64 !important;
    }
    .project-list .add-project-block {
        text-decoration: none;
        border: 2px dashed #bdbbbb;
        border-radius: 10px;
        height: 100%;
    }

    .add-project-block:hover {
        border: 2px dashed #949292;
        background-color: rgb(245, 243, 243);
    }

    hr {
        border: 1px dashed rgb(236, 234, 234);
    }

    .el-menu {
        border-right: 0px !important;
    }

    .img-thumb {
        width: auto;
        height: 120px;
        background-repeat: no-repeat;
        background-position: center center;
        background-size: cover;
        border-radius: 5px;
    }

    .list-team-members {
        flex-wrap: wrap;
        text-align: center
    }

    .list-team-members img {
        border-radius: 50% !important;
    }

    .member-thumbnail {
        margin-right: -15px
    }

    .project-name:hover {
        cursor: pointer;
    }
    .project-name:hover p {
        color: #3890b5;
    }

    .w3-hover-shadow {
        border: 1px solid #c5c3c3;
        border-radius: 5px;
        position: relative;
    }

    .delete-btn {
        position: absolute;
        top: 5px;
        right: 10px;
    }

    .delete-btn-list i {
        margin-top: -3px;
        transform: rotate(90deg);
    }

    .delete-btn-list i:hover {
        color: #3890b5 !important;
    }

    .list-progress {
        display: flex;
        justify-content: space-around;
        align-items: center;
    }

    .project-team-members {
        position: absolute;
        bottom: 0;
    }

    i.el-icon-more {
        color: white;
        font-size: 20px;
        outline: none;
    }

    .edition-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        text-align: center;
        padding-left: 6px;
        padding-bottom: 10px;
        font-size: 22px;
        border: 1px solid #cacbcc;
        color: #626364;
        outline: none;
        background-color: #fff;
    }

    .dropbtn {
        color: white;
        cursor: pointer;
    }

    .dropbtn:hover,
    .dropbtn:focus {
        background-color: #2980B9;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f1f1f1;
        min-width: 160px;
        overflow: auto;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown a:hover {
        background-color: #ddd
    }

    .show {
        display: block;
    }

    .inner-badge > .el-badge__content {
        background-color: rgb(252, 124, 124);
        border: 0px;
        color: #fff;
    }

    .navbar {
        background-color: #545c64;
    }

    .rollover {
        width: 100%;
        height: 100%;
        z-index: 999;
        position: absolute;
        display: flex;
        justify-content: center;
        align-items: center;
        background: rgba(0, 0, 0, .5);
        opacity: 0;
        /*transition: 0.1s*/
    }

    .w3-hover-shadow:hover .rollover {
        opacity: 1;
        /*transition: 0.1s*/
    }

    .roll-btn {
        background-color: rgba(0, 0, 0, .7);
        color: #fff;
    }

    .draft-project i {
        margin-top: 20px;
        font-size: 45px;
        color: #80B4A0;
    }

    .list-draft-project i {
        font-size: 30px;
        color: #80B4A0;
    }

    .el-alert--info {
        text-align: center;
        background: #ccecfc;
        color: #3890b5;
        border-radius: 10px;
    }

    .el-alert--info p {
        margin: 0px !important;
    }
    
    .inProgressDialog .el-upload__input {
        display: none !important;
    }

    /* The switch - the box around the slider */
    .switch {
        position: relative;
        display: inline-block;
        width: 49px;
        height: 23px;
    }

    /* Hide default HTML checkbox */
    .switch input {display:none;}

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

    .notify-label{
        text-align: right;
        font-weight: normal;
    }

    .project-role {
        padding: 0 5px;
        background: #ccecfc;
        color: #3890b5;
        position: absolute;
        border-radius: 5px;
        font-weight: bold;
        top: -5px;
        left: -5px;
        z-index: 1000;
        box-shadow: 0 4px 10px 0 rgba(0,0,0,0.2), 0 4px 20px 0 rgba(0,0,0,0.19)
    }
    .creative-brief-dialog .el-dialog__body{
        text-align: center;
    }
    /* Mobile View */
    @media (max-width: 750px) {
        /* Dashboard menu */
        .el-menu-item .labels,
        .el-menu-item .projects-counts {
            display: none;
        }
        /* Header alert */
        .header-alert {
            display: none;
        }
        /* List buttons */
        .list-buttons {
            display: none;
        }
        .project-list > div:first-child {
            margin-bottom: 10px;
        }
        /* Header navbar */
        .navbar .navbar-header .hamburger {
            display: none;
        }
        .navbar .navbar-collapse {
            position: absolute;
            top: 0;
            right: 0;
            display: flex;
            border-top: 0;
        }
        .navbar .navbar-collapse .navbar-right {
            display: flex;
            margin: 0;
        }
        .navbar .navbar-collapse .navbar-right .dropdown-menu {
            background-color: #fff;
            position: absolute;
            right: 0;
            left: unset;
            border: 1px solid #ccc;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
        }
    }
    .project-list .new-project {
        min-height: 245px;
        margin-bottom: 10px;
    }
    @media (max-width: 550px) {
        .project-list .new-project {
            position: relative;
            height: 245px;
            width: 100%;
            /*padding-top: 100%;*/
            margin-left: 5px;
            margin-right: 5px;
        }
        .project-list .new-project .add-project-block {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 245px;
        }
        .teammember-dialog .el-dialog {
            width: 100% !important;
        }
        .teammember-dialog .el-dialog .notify-label div span {
            margin-left: 5px !important;
        }
        .cancel-btn {
            margin-top: 10px;
        }
        .el-dialog__footer {
            text-align: center !important;
        }
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
    /* Dialog Media */
    @media (max-width: 1200px) {
        /* Dialog */
        .el-dialog__wrapper .el-dialog {
            width: 90%;
        }
        .el-dialog__body {
            text-align: center;
        }
    }
    .ql-editing, .ql-tooltip {
        left: 2px !important;
        top: -8px !important;
    }
</style>
