<template>
    <el-container class="page-proof" v-loading.fullscreen.lock="fullscreenLoading">
        <!-- Proof Header -->
        <el-header v-if="!isMobile" class="proof-header">
            <el-container>

                <div class="el-aside header-left">

                    <!-- Nav Dashboard -->
                    <div class="nav-dashboard" @click="goToDashboard()">
                        <i class="menu-icon el-icon-menu"></i>
                        <span class="menu-text">
                            <i class="el-icon-arrow-left"></i>
                            DASHBOARD
                        </span>
                    </div>
                </div>

                <el-main class="header-main">

                    <!-- Select Revision -->
                    <el-select
                        v-if="project.project_type != 'website'"
                        v-model="current_revision.id"
                        @change="goToRevision()"
                        class="select-revision">
                        <el-option
                            v-for="version in project.versions"
                            :key="version.value"
                            :label="version.label"
                            :value="version.id">
                        </el-option>
                    </el-select>

                    <template v-if="project.versions">
                        <!-- Freelancer buttons -->
                        <template v-if="current_role == 'freelancer'">
                            <el-button v-if="
                                project.project_status == 'progress'
                                "
                                type="primary"
                                icon="el-icon-upload"
                                @click="openNewRevisionDialog">
                                Upload new proof
                            </el-button>
                            <el-button v-if="
                                project.project_status == 'approved' ||
                                project.project_status == 'completed'
                                "
                                type="primary"
                                icon="el-icon-upload"
                                @click="showFinalFilesDialog = true">
                                Upload Final Files
                            </el-button>
                        </template>

                        <!-- Client buttons -->
                        <template v-if="current_role == 'client'">
                            <el-button v-if="
                                project.project_type != 'website' &&
                                project.project_status == 'revision'
                                "
                                icon="el-icon-edit"
                                @click="showSendCorrectionsDialog = true">
                                Send Corrections
                            </el-button>
                            <el-button v-if="
                                project.project_status == 'progress'
                                "
                                type="primary"
                                icon="el-icon-success">
                                Corrections Sent
                            </el-button>
                            <el-button v-if="
                                project.project_status == 'revision'
                                "
                                type="primary"
                                icon="el-icon-circle-check"
                                @click="approveProject()">
                                Approve
                            </el-button>
                            <el-button v-if="
                                project.project_status == 'approved' ||
                                project.project_status == 'completed'
                                "
                                type="primary"
                                icon="el-icon-download"
                                @click="showFinalFilesDialog = true">
                                Download Final Files
                            </el-button>
                        </template>
                    </template>

                </el-main>

                <el-aside class="header-right">

                    <div class="collaborators">

                        <!-- Collaborator List -->
                        <el-tooltip
                            v-for="collaborator in project.collaborators" :key="collaborator.id"
                            :content="collaborator.name">
                            <img class="collaborator user-avatar" :src="collaborator.photo_url">
                        </el-tooltip>

                        <!-- Add new collaborator -->
                        <el-tooltip
                            content="Add New Collaborator">
                            <el-button class="user-avatar add-button" @click="showNewCollaboratorDialog = true">
                                <i class="el-icon-plus"></i>
                            </el-button>
                        </el-tooltip>

                    </div>

                </el-aside>

            </el-container>
        </el-header>
        <!-- Proof Header End -->

        <!-- Mobile Header -->
        <el-header v-else class="mobile-header">

            <!-- Revision Header -->
            <el-container v-if="mobileStatus == 'revision'">
                <el-aside width="70px">
                    <div class="nav-dashboard" @click="goToDashboard()">
                        <i class="menu-icon el-icon-menu"></i>
                        <span class="menu-text">
                            <i class="el-icon-arrow-left"></i>
                        </span>
                    </div>
                </el-aside>
                <el-main>
                    <div class="revision-buttons">
                        <el-select
                            v-if="project.project_type != 'website'"
                            v-model="current_revision.id"
                            @change="goToRevision()"
                            class="select-revision"
                            size="small">
                            <el-option
                                v-for="version in project.versions"
                                :key="version.value"
                                :label=" 'RR ' + version.value"
                                :value="version.id">
                            </el-option>
                        </el-select>
                        <template v-if="
                            project.versions
                            ">
                            <!-- Freelancer buttons -->
                            <template v-if="current_role == 'freelancer'">
                                <el-button v-if="
                                    project.project_status == 'progress'
                                    "
                                    icon="el-icon-upload"
                                    @click="openNewRevisionDialog"
                                    size="small">
                                </el-button>
                                <el-button v-if="
                                    project.project_status == 'approved' ||
                                    project.project_status == 'completed'
                                    "
                                icon="el-icon-upload"
                                @click="showFinalFilesDialog = true">
                            </el-button>
                            </template>

                            <!-- Client buttons -->
                            <template v-if="current_role == 'client'">
                                <el-button v-if="
                                    project.project_type != 'website' &&
                                    current_revision.status_revision == 'revision'
                                    "
                                    @click="showSendCorrectionsDialog = true"
                                    type="danger"
                                    icon="el-icon-edit"
                                    class="submit-button"
                                    size="small">
                                </el-button>
                                <el-button v-if="
                                    current_revision.status_revision == 'revision'
                                    "
                                    type="success" 
                                    icon="el-icon-check"
                                    class="approve-button"
                                    @click="approveProject()"
                                    size="small">
                                </el-button>
                                <el-button v-if="
                                    current_revision.status_revision == 'approved'
                                    "
                                    type="primary"
                                    icon="el-icon-check"
                                    size="small">
                                    Approved
                                </el-button>

                                <el-button v-if="
                                    current_revision.status_revision == 'completed'"
                                    type="primary"
                                    icon="el-icon-download"
                                    @click="showFinalFilesDialog = true"
                                    size="small">
                                </el-button>
                            </template>
                        </template>
                        <template v-if="
                            project.project_status == 'revision'
                            &&
                            ( current_role == 'freelancer' ||
                            current_role == 'client' && project.project_type == 'website' ) 
                            ">
                            <el-button
                                type="danger"
                                icon="el-icon-upload" class="upload-button"
                                @click="openUploadDialog"
                                size="small">
                            </el-button>
                        </template>
                    </div>
                </el-main>
                <el-aside width="60px" class="text-center">
                    <div class="user">
                        <el-button class="user-avatar add-button" @click="showNewCollaboratorDialog = true">
                            <i class="el-icon-plus"></i>
                        </el-button>
                    </div>
                </el-aside>
            </el-container>

            <!-- Canvas Header -->
            <el-container v-if="mobileStatus == 'canvas'">
                <el-aside width="70px">
                    <div class="nav-dashboard" @click="mobileStatus = 'revision'">
                        <i class="menu-icon fa fa-square"></i>
                        <span class="menu-text">
                            <i class="el-icon-arrow-left"></i>
                        </span>
                    </div>
                </el-aside>
                <el-main>
                    <div class="canvas-tools">
                        <el-button
                            icon="el-icon-edit"
                            :type="pen_active ? 'primary' : ''"
                            @click="togglePen()"
                            class="canvas-tool"
                            circle>
                        </el-button>

                        <el-slider
                            v-model="zoom"
                            :min="3"
                            :max="400"
                            :change="setZoom()"
                            class="zoom-slider">
                        </el-slider>
                    </div>
                </el-main>
            </el-container>

            <!-- Comments Header -->
            <el-container v-if="mobileStatus == 'comments'">
                <el-aside width="70px">
                    <div class="nav-dashboard" @click="mobileStatus = 'revision'">
                        <i class="menu-icon fa fa-square"></i>
                        <span class="menu-text">
                            <i class="el-icon-arrow-left"></i>
                        </span>
                    </div>
                </el-aside>
                <el-main>
                    <div class="comments-header">
                        <i class="fa fa-comment"></i>
                        <span>Comments</span>
                    </div>
                </el-main>
                <el-aside width="70px">
                    <div class="nav-dashboard" @click="mobileStatus = 'canvas'">
                        <i class="menu-icon el-icon-edit"></i>
                        <span class="menu-text">
                            <i class="el-icon-arrow-right"></i>
                        </span>
                    </div>
                </el-aside>
            </el-container>
        </el-header>
        <!-- Mobile Header End -->

        <el-container class="proof-container">

            <!-- Left Sidebar -->
            <div v-if="!isMobile" class="el-aside sidebar-left">

                <div class="el-header">
                    <div class="progress-area">
                        <!-- Progress bar -->
                        <el-progress 
                            :percentage="project.percentage"
                            :text-inside="true"
                            :stroke-width="17"
                            class="project-progress">
                        </el-progress>
                        <div class="progress-info">
                            <span class="text-success">{{ project.solved_issues }}</span>
                            OF
                            <span class="text-danger">{{ project.total_issues }}</span>
                            ISSUES COMPLETED
                        </div>
                        <!-- Team members -->
                        <div class="team-members">
                            <el-tooltip
                                v-for="member in project.teamMembers" :key="member.id"
                                :content="member.name">
                                <img class="user-avatar" :src="member.photo_url">
                            </el-tooltip>
                        </div>
                    </div>
                    <template v-if="
                        project.project_status == 'revision'
                        &&
                        ( current_role == 'freelancer' ||
                          current_role == 'client' && project.project_type == 'website' ) 
                        ">
                        <div v-if="project.project_type == 'website'" class="buttons-area">
                            <el-button-group class="button-group">
                                <el-tooltip :content="pluginInstalled ? 'Capture image with plugin' : 'Install plugin'">
                                    <el-button class="upload-button" @click="pluginInstalled ? captureScreen() : installProofloPlugin()">
                                        <img src="/img/p.png" alt="p">
                                    </el-button>
                                </el-tooltip>
                                <el-tooltip content="Upload image from computer">
                                    <el-button 
                                        icon="el-icon-upload" class="upload-button"
                                        @click="openUploadDialog">
                                    </el-button>
                                </el-tooltip>
                            </el-button-group>
                        </div>
                        <div v-else class="buttons-area">
                            <el-button
                                icon="el-icon-upload" class="upload-button"
                                @click="openUploadDialog">
                                Upload Image
                            </el-button>
                        </div>
                    </template>
                    <div class="buttons-area creative-brief">
                        <el-button icon="el-icon-document"
                                   class="creative-brief-button"
                                   @click="openCreativeBriefDialog">
                            Creative Brief
                        </el-button>
                    </div>
                </div>

                <el-main>
                    <!-- Proof thumb list -->
                    <el-card 
                        v-for="(proof, index) in current_revision.proofs" :key="proof.id"
                        @click.native="goToProof(proof)"
                        class="proof-thumb"
                        :class="{ 'active' : current_proof.id == proof.id }">
                        <span class="proof-number">{{ index + 1 }}</span>
                        <img class="thumb-image" :src="'/storage/' + proof.project_files.thumb_path">
                        <div class="buttons-area">
                            <el-button
                                v-if="
                                    current_role == 'freelancer' ||
                                    current_role == 'client' && proof.project_files.user_id == current_user.id
                                    "
                                type="danger" icon="el-icon-delete"
                                class="delete-button"
                                @click="deleteProof(proof)"
                                size="mini" plain>
                            </el-button>
                            <el-button
                                type="danger" icon="el-icon-download"
                                class="download-button"
                                @click="downloadProof(proof)"
                                size="mini" plain>
                            </el-button>
                        </div>
                        <div class="review-comment">
                            <el-button-group>
                                <el-button
                                    v-if="getReviewCount(proof)"
                                    type="warning" icon="el-icon-warning">
                                    {{ getReviewCount(proof) }}
                                </el-button>
                                <el-button
                                    v-if="getunreadComments(proof)"
                                    type="danger" icon="fa fa-commenting">
                                    {{ getunreadComments(proof) }}
                                </el-button>
                            </el-button-group>
                        </div>
                        <div v-if="proof.status == 'approved'" class="proof-check">
                            <i class="el-icon-check"></i>
                        </div>
                    </el-card>
                </el-main>

            </div>
            <!-- Left Sidebar End -->

            <!-- Proof Main -->
            <el-main class="proof-main">

                <!-- Canvas Stage -->
                <div id="stage"></div>

                <div v-if="!isMobile" class="canvas-tools">

                    <div class="pen-tool">
                        <!-- Draw Tool -->
                        <el-button
                            icon="el-icon-edit"
                            :type="pen_active ? 'primary' : ''"
                            @click="togglePen()"
                            class="canvas-tool"
                            circle>
                        </el-button>
                        <!-- Tool Info -->
                        <div v-if="showTooltip" class="tool-tip">
                            SELECT TO ADD CORECTION(S)
                        </div>
                    </div>

                    <!-- Zoom Slider -->
                    <el-slider
                        v-model="zoom"
                        :min="5"
                        :max="400"
                        :change="setZoom()"
                        show-input
                        class="zoom-slider">
                    </el-slider>

                    <!-- FitWidth Tool -->
                    <el-button
                        icon="fa fa-arrows-h"
                        @click="fitImageSize('fullwidth')"
                        class="canvas-tool"
                        circle>
                    </el-button>

                    <!-- FitScreen Tool -->
                    <el-button
                        icon="fa fa-arrows-v"
                        @click="fitImageSize('fitscreen')"
                        class="canvas-tool"
                        circle>
                    </el-button>

                    <!-- Rotate Tool -->
                    <el-button
                        v-if="project.project_type != 'website'"
                        icon="el-icon-refresh"
                        @click="rotateImage()"
                        class="canvas-tool"
                        circle>
                    </el-button>

                    <!-- Toggle Buttons -->
                    <el-button 
                        icon="el-icon-picture"
                        class="toggle-button left"
                        @click="toggleSidebar('left')">
                    </el-button>
                    <el-button
                        icon="fa fa-bars"
                        class="toggle-button right"
                        @click="toggleSidebar('right')">
                    </el-button>
                </div>

                <!-- Pagination -->
                <div v-if="!isMobile" class="proof-pagination">
                    <el-pagination
                        v-if="current_revision.proofs"
                        background
                        layout="prev, pager, next"
                        :page-size="1"
                        :total="current_revision.proofs.length"
                        :current-page="current_revision.proofs.indexOf(current_proof) + 1"
                        @current-change="handlePageChange"
                        >
                    </el-pagination>
                </div>

                <!-- Mobile Cover -->
                <div v-if="isMobile && mobileStatus == 'revision'"
                    @click="enableStageEditable()"
                    class="canvas-overwrap">
                    <h5 class="tip-edit">Tap Image To Edit</h5>
                </div>

            </el-main>
            <!-- Proof Main End -->

            <!-- Right Sidebar -->
            <div
                v-show="!isMobile || mobileStatus == 'comments'"
                class="el-aside sidebar-right"
                :class="{ 'mobile-sidebar': isMobile }">

                <div class="issue-list">
                    <!-- New Issue Form -->
                    <el-card v-show="newIssueForm.active" class="new-issue" shadow="never">
                        <el-form :model="newIssueForm" ref="newIssueForm" @submit.native.prevent="addNewIssue()">
                            <el-form-item
                                prop="description" :rules="[
                                    { required: true, message: 'Description is required' }
                                ]">
                                <el-input
                                    type="textarea" 
                                    autosize
                                    ref="newIssueForm_description"
                                    v-model="newIssueForm.description"
                                    @keydown.native.enter.prevent="addNewIssue()"
                                    @keydown.native.esc.prevent="cancelNewIssue()"
                                    placeholder="Type comment here">
                                </el-input>
                            </el-form-item>
                            <el-form-item class="form-buttons">
                                <el-upload
                                    class="upload-dialog"
                                    name="photos"
                                    action="/api/files/upload"
                                    ref="upload_new_issue"
                                    :data="uploadFormData"
                                    :file-list="uploadFileList"
                                    :on-success="onIssueImageUploaded"
                                    :auto-upload="false"
                                    multiple>
                                    <el-button slot="trigger" size="mini" icon="fa fa-paperclip"></el-button>
                                    <el-button size="mini" @click="cancelNewIssue()" class="cancel-button">Cancel</el-button>
                                    <el-button type="primary" size="mini" @click="addNewIssue()">OK</el-button>
                                </el-upload>
                            </el-form-item>
                        </el-form>
                    </el-card>

                    <!-- Issue List -->
                    <el-card
                        v-for="(issue, index) in reverse(current_proof.issues)" :key="issue.id"
                        class="issue-box"
                        :class="issue.status"
                        shadow="never">
                        <el-row class="box-header">
                            <el-col :span="4">
                                <el-button class="nav-button"
                                    icon="el-icon-arrow-left"
                                    @click="showIssueDetails(issue)" circle>
                                </el-button>
                            </el-col>
                            <el-col :span="16" class="text-center">
                                <template v-if="
                                    project.project_type == 'website' && current_role == 'freelancer' && issue.status == 'done'">
                                    <el-button class="check-button">
                                        Approved
                                    </el-button>
                                </template>
                                <template v-else-if="
                                    project.project_type == 'website' && current_role == 'client' && issue.status == 'todo'">
                                    <el-button class="check-button">
                                        In Review
                                    </el-button>
                                </template>
                                <template v-else>
                                    <el-checkbox
                                        v-model="issue.isCheck"
                                        :label="
                                            (project.project_type == 'website' && issue.status != 'todo')
                                            ? issue.status == 'review'
                                                ? current_role == 'client' ? 'Approve' :'In Review'
                                                : 'Approved'
                                            : issue.status
                                            "
                                        @change="toggleIssueStatus(issue)"
                                        class="check-button"
                                        border>
                                    </el-checkbox>
                                </template>
                            </el-col>
                            <el-col :span="4" class="text-right">
                                <el-button
                                    :type="issue.owner_type == 'freelancer' ? 'info' : 'danger'"
                                    @click="showIssueGroup(issue.group)"
                                    class="issue-label"
                                    circle>
                                    {{ issue.label }}
                                </el-button>
                            </el-col>
                        </el-row>
                        <el-row>
                            <el-col :span="4">
                                <el-tooltip :content="issue.user.name">
                                    <template v-if="issue.unread_comments && issue.unread_comments.length > 0">
                                        <el-badge :value="issue.unread_comments.length" class="item">
                                            <img class="user-avatar" :src="issue.user.photo_url">
                                        </el-badge>
                                    </template>
                                    <template v-else>
                                        <img class="user-avatar" :src="issue.user.photo_url">
                                    </template>
                                </el-tooltip>
                            </el-col>
                            <el-col :span="19">
                                <div class="datetime">
                                    {{ issue.created_at | utc_to_local }}
                                </div>
                                <div class="description" trigger="click" @click="editIssue(issue, index)">
                                    <span v-html="urlify(issue.description)"></span>
                                </div>
                                <div class="edit-issue">
                                    <el-input
                                        type="textarea"
                                        :autosize="{ minRows: Math.round(issue.description.length / 20) }"
                                        v-model="issue.description"
                                        @keydown.native.enter.prevent="updateIssue(issue, index)"
                                        class="edit-description"
                                        placeholder="Type comment here"
                                        autofocus>
                                    </el-input>
                                    <div class="text-right">
                                        <el-upload
                                            class="upload-dialog"
                                            name="photos"
                                            action="/api/files/upload"
                                            :ref="'upload_issue_' + issue.id"
                                            :data="uploadFormData"
                                            :file-list="uploadFileList"
                                            :on-success="onIssueImageUploaded"
                                            :auto-upload="false"
                                            multiple>
                                            <el-button
                                                size="mini" type="danger"
                                                class="update-button"
                                                icon="el-icon-delete"
                                                @click="deleteIssue(issue)"
                                                plain>
                                            </el-button>
                                            <el-button 
                                                slot="trigger" size="mini" type="info"
                                                icon="fa fa-paperclip"
                                                plain>
                                            </el-button>
                                            <el-button 
                                                type="primary" size="mini"
                                                class="update-button"
                                                @click="updateIssue(issue, index)">
                                                OK
                                            </el-button>
                                        </el-upload>
                                    </div>
                                </div>
                                <template v-if="issue.project_files_id">
                                    <div v-for="file in current_issue.files" class="attach-image" :key="file.id">
                                        <img :src="'/storage/' + file.thumb_path">
                                        <div class="attach-overlay">
                                            <i class="el-icon-zoom-in attach-button"
                                               @click="openImagePreview('/storage/' + file.thumb_path)"></i>
                                            <a class="attach-button"
                                               :href="'/storage/' + file.thumb_path" download>
                                                <i class="el-icon-download"></i>
                                            </a>
                                            <i class="el-icon-delete attach-button"
                                               @click="deleteImage(current_issue, file, 'issue')">
                                            </i>
                                        </div>
                                    </div>
                                </template>
                            </el-col>
                            <el-col :span="1">
                                <el-dropdown v-if="issue.user_id == current_user.id" trigger="click" class="more-button">
                                    <span class="el-dropdown-link">
                                        <i class="el-icon-more"></i>
                                    </span>
                                    <el-dropdown-menu slot="dropdown">
                                        <el-dropdown-item v-if="current_role == 'client' && issue.status != 'done'" @click.native="toggleIssueStatus(issue, 'done')">Approve</el-dropdown-item>
                                        <el-dropdown-item @click.native="editIssue(issue, index)">Edit</el-dropdown-item>
                                        <el-dropdown-item @click.native="deleteIssue(issue)">Delete</el-dropdown-item>
                                    </el-dropdown-menu>
                                </el-dropdown>
                            </el-col>
                        </el-row>
                    </el-card>
                </div>

                <!-- Issue Details -->
                <transition name="comments-slide">
                    <div v-if="isIssueDetails" class="issue-details">

                        <!-- Issue Box -->
                        <el-card class="issue-box current-issue" :class="current_issue.status" shadow="never">
                            <el-row class="box-header">
                                <el-col :span="4">
                                    <el-button class="nav-button"
                                        icon="el-icon-arrow-right"
                                        @click="hideIssueDetails()" circle>
                                    </el-button>
                                </el-col>
                                <el-col :span="16" class="text-center">
                                    <template v-if="
                                        project.project_type == 'website' && current_role == 'freelancer' && current_issue.status == 'done'">
                                        <el-button class="check-button">
                                            Approved
                                        </el-button>
                                    </template>
                                    <template v-else-if="
                                        project.project_type == 'website' && current_role == 'client' && current_issue.status == 'todo'">
                                        <el-button class="check-button">
                                            In Review
                                        </el-button>
                                    </template>
                                    <template v-else>
                                        <el-checkbox
                                            v-model="current_issue.isCheck"
                                            :label="
                                                (project.project_type == 'website' && current_issue.status != 'todo')
                                                ? current_issue.status == 'review'
                                                    ? current_role == 'client' ? 'Approve' :'In Review'
                                                    : 'Approved'
                                                : current_issue.status
                                                "
                                            @change="toggleIssueStatus(current_issue)"
                                            class="check-button"
                                            border>
                                        </el-checkbox>
                                    </template>
                                </el-col>
                                <el-col :span="4" class="text-right">
                                    <el-button
                                        :type="current_issue.owner_type == 'freelancer' ? 'info' : 'danger'"
                                        @click="showIssueGroup(current_issue.group)"
                                        class="issue-label"
                                        circle>
                                        {{ current_issue.label }}
                                    </el-button>
                                </el-col>
                            </el-row>
                            <el-row>
                                <el-col :span="4">
                                    <el-tooltip :content="current_issue.user.name">
                                        <img class="user-avatar" :src="current_issue.user.photo_url">
                                    </el-tooltip>
                                </el-col>
                                <el-col :span="19">
                                    <div class="datetime">
                                        {{ current_issue.created_at | utc_to_local }}
                                    </div>
                                    <div class="description" @click="editIssue(current_issue, -1)">
                                        <span v-html="urlify(current_issue.description)"></span>
                                    </div>
                                    <div class="edit-issue">
                                        <el-input
                                            type="textarea" 
                                            :autosize="{ minRows: Math.round(current_issue.description.length / 20) }"
                                            v-model="current_issue.description"
                                            @keydown.native.enter.prevent="updateIssue(current_issue, -1)"
                                            class="edit-description"
                                            placeholder="Type comment here"
                                            autofocus>
                                        </el-input>
                                        <div class="text-right">
                                            <el-upload
                                                class="upload-dialog"
                                                name="photos"
                                                action="/api/files/upload"
                                                :ref="'upload_current_issue_' + current_issue.id"
                                                :data="uploadFormData"
                                                :file-list="uploadFileList"
                                                :on-success="onIssueImageUploaded"
                                                :auto-upload="false"
                                                multiple>
                                                <el-button
                                                    slot="trigger"
                                                    type="info" size="mini"
                                                    icon="fa fa-paperclip"
                                                    plain>
                                                </el-button>
                                                <el-button
                                                    size="mini" type="danger"
                                                    class="update-button"
                                                    icon="el-icon-delete"
                                                    @click="deleteIssue(current_issue)"
                                                    plain>
                                                </el-button>
                                                <el-button 
                                                    type="primary" size="mini"
                                                    class="update-button"
                                                    @click="updateIssue(current_issue, -1)">
                                                    OK
                                                </el-button>
                                            </el-upload>
                                        </div>
                                    </div>
                                    <template v-if="current_issue.files">
                                        <div v-for="file in current_issue.files" class="attach-image" :key="file.id">
                                            <img :src="'/storage/' + file.thumb_path">
                                            <div class="attach-overlay">
                                                <i class="el-icon-zoom-in attach-button"
                                                   @click="openImagePreview('/storage/' + file.thumb_path)"></i>
                                                <a class="attach-button"
                                                   :href="'/storage/' + file.thumb_path" download>
                                                    <i class="el-icon-download"></i>
                                                </a>
                                                <i class="el-icon-delete attach-button"
                                                   @click="deleteImage(current_issue, file, type = 'issue')">
                                                </i>
                                            </div>
                                        </div>
                                    </template>
                                </el-col>
                                <el-col :span="1">
                                    <el-dropdown v-if="current_issue.user_id == current_user.id" trigger="click" class="more-button">
                                        <span class="el-dropdown-link">
                                            <i class="el-icon-more"></i>
                                        </span>
                                        <el-dropdown-menu slot="dropdown">
                                            <el-dropdown-item v-if="current_role == 'client' && current_issue.status != 'done'" @click.native="toggleIssueStatus(current_issue, 'done')">Approve</el-dropdown-item>
                                            <el-dropdown-item @click.native="editIssue(current_issue, -1)">Edit</el-dropdown-item>
                                            <el-dropdown-item @click.native="deleteIssue(current_issue)">Delete</el-dropdown-item>
                                        </el-dropdown-menu>
                                    </el-dropdown>
                                </el-col>
                            </el-row>
                        </el-card>

                        <!-- New Comment Form -->
                        <el-card class="new-issue" shadow="never">
                            <el-form :model="newCommentForm" ref="newCommentForm" @submit.native.prevent="addNewComment()">
                                <el-form-item prop="description">
                                    <el-input
                                        type="textarea"
                                        @keydown.native.enter.prevent="addNewComment()"
                                        v-model="newCommentForm.description"
                                        placeholder="Type comment here"
                                        autosize>
                                    </el-input>
                                </el-form-item>
                                <el-form-item class="form-buttons">
                                    <el-upload
                                        class="upload-dialog"
                                        name="photos"
                                        action="/api/files/upload"
                                        ref="upload_new_comment"
                                        :data="uploadFormData"
                                        :file-list="uploadFileList"
                                        :on-success="onCommentImageUploaded"
                                        :auto-upload="false"
                                        multiple>
                                        <el-button slot="trigger" size="mini" icon="fa fa-paperclip"></el-button>
                                        <el-button
                                            type="primary" size="mini" 
                                            @click="addNewComment()"
                                            class="add-button">
                                            OK
                                        </el-button>
                                    </el-upload>
                                </el-form-item>
                            </el-form>
                        </el-card>

                        <!-- Comment List -->
                        <el-card
                            v-for="(comment, index) in reverse(current_issue.comments)" :key="comment.id"
                            class="issue-box comment-box"
                            shadow="never">
                            <el-row>
                                <el-col :span="4">
                                    <el-tooltip :content="comment.user.name">
                                        <img class="user-avatar" :src="comment.user.photo_url">
                                    </el-tooltip>
                                </el-col>
                                <el-col :span="19">
                                    <div class="author-name">
                                        {{ comment.user.name }}
                                    </div>
                                    <div class="datetime">
                                        {{ comment.created_at | utc_to_local }}
                                    </div>
                                    <div class="description" @click="editComment(comment, index)">
                                        <span v-html="urlify(comment.description)"></span>
                                    </div>
                                    <div class="edit-issue">
                                        <el-input
                                            type="textarea" 
                                            :autosize="{ minRows: Math.round(comment.description.length / 20) }"
                                            v-model="comment.description"
                                            @keydown.native.enter.prevent="updateComment(issue, index)"
                                            class="edit-description"
                                            placeholder="Type comment here"
                                            autofocus>
                                        </el-input>
                                        <div class="text-right">
                                            <el-upload
                                                class="upload-dialog"
                                                name="photos"
                                                action="/api/files/upload"
                                                :ref="'upload_comment_' + comment.id"
                                                :data="uploadFormData"
                                                :file-list="uploadFileList"
                                                :on-success="onCommentImageUploaded"
                                                :auto-upload="false"
                                                multiple>
                                                <el-button
                                                    slot="trigger"
                                                    type="info" size="mini"
                                                    icon="fa fa-paperclip"
                                                    plain>
                                                </el-button>
                                                <el-button
                                                    size="mini" type="danger"
                                                    class="update-button"
                                                    icon="el-icon-delete"
                                                    @click="deleteComment(comment)"
                                                    plain>
                                                </el-button>
                                                <el-button 
                                                    type="primary" size="mini"
                                                    class="update-button"
                                                    @click="updateComment(comment, index)">
                                                    OK
                                                </el-button>
                                            </el-upload>
                                        </div>
                                    </div>
                                    <template v-if="comment.files">
                                        <div v-for="file in comment.files" class="attach-image" :key="file.id">
                                            <img :src="'/storage/' + file.thumb_path">
                                            <div class="attach-overlay">
                                                <i class="el-icon-zoom-in attach-button"
                                                   @click="openImagePreview('/storage/' + file.thumb_path)"></i>
                                                <a class="attach-button"
                                                   :href="'/storage/' + file.thumb_path" download>
                                                    <i class="el-icon-download"></i>
                                                </a>
                                                <i class="el-icon-delete attach-button"
                                                   @click="deleteImage(comment, file, type = 'comment')">
                                                </i>
                                            </div>
                                        </div>
                                    </template>
                                </el-col>
                                <el-col :span="1">
                                    <el-dropdown v-if="comment.user_id == current_user.id" trigger="click" class="more-button">
                                        <span class="el-dropdown-link">
                                            <i class="el-icon-more"></i>
                                        </span>
                                        <el-dropdown-menu slot="dropdown">
                                            <el-dropdown-item @click.native="editComment(comment, index)">Edit</el-dropdown-item>
                                            <el-dropdown-item @click.native="deleteComment(comment)">Delete</el-dropdown-item>
                                        </el-dropdown-menu>
                                    </el-dropdown>
                                </el-col>
                            </el-row>
                        </el-card>

                    </div>
                </transition>

                <div class="getting-started">
                    <el-button class="link-button">
                        <a href="https://prooflo.com/video-tutorials/" target="_blank">Getting Started | Video Tutorials</a>
                    </el-button>
                </div>

            </div>
            <!-- Right Sidebar End -->

        </el-container>

        <el-footer v-if="isMobile" class="mobile-footer" height="100px">
            <el-row type="flex">
                <el-col class="footer-main">
                    <el-card 
                        v-for="(proof, index) in current_revision.proofs" :key="proof.id"
                        @click.native="loadProof(proof)"
                        class="proof-thumb"
                        :class="{ 'active' : current_proof.id == proof.id }">
                        <!-- <img class="thumb-image" :src="'/storage/' + proof.project_files.thumb_path"> -->
                        <span class="proof-number">{{ index + 1 }}</span>
                        <img class="thumb-image" :src="'/storage/' + proof.project_files.thumb_path">
                        <el-button
                            v-if="
                                current_role == 'freelancer' ||
                                current_role == 'client' && proof.project_files.user_id == current_user.id
                                "
                            type="danger" icon="el-icon-delete"
                            class="delete-button"
                            @click="deleteProof(proof)"
                            size="mini" plain>
                        </el-button> 
                        <div class="review-comment">
                            <el-button-group>
                                <el-button
                                    v-if="getReviewCount(proof)"
                                    type="warning" icon="el-icon-warning">
                                    {{ getReviewCount(proof) }}
                                </el-button>
                                <el-button
                                    v-if="getunreadComments(proof)"
                                    type="danger" icon="fa fa-commenting">
                                    {{ getunreadComments(proof) }}
                                </el-button>
                            </el-button-group>
                        </div>
                        <div v-if="proof.status == 'approved'" class="proof-check">
                            <i class="el-icon-check"></i>
                        </div>
                    </el-card>
                </el-col>
                <el-col class="footer-right">
                    <el-button
                        icon="fa fa-commenting"
                        class="toggle-comments"
                        @click="toggleComments()">
                        COMMENTS
                    </el-button>
                </el-col>
            </el-row>
        </el-footer>

        <!-- Upload Dialog -->
        <el-dialog
            title="Upload New Image"
            :visible.sync="showUploadDialog"
            @close="onCloseUploadDialog"
            class="upload-dialog">
            <pdf-converting v-if="pdfLoader" v-on:confirm="pdfLoader = false"></pdf-converting>
            <el-upload
                name='photos'
                list-type="picture"
                action='/api/files/upload'
                :data="uploadFormData"
                :file-list="uploadFileList"
                :before-upload="handleUpload"
                :on-success="handleSuccess"
                drag multiple>
                <i class="el-icon-upload"></i>
                <div class="el-upload__text">Drop file here or <em>click to upload</em></div>
            </el-upload>
            <span slot="footer" class="dialog-footer">
                <el-button @click="showUploadDialog = false">Cancel</el-button>
                <el-button type="primary" @click="sendUploadFiles()">Send</el-button>
            </span>
        </el-dialog>

        <!--Creative Brief Dialog-->
        <el-dialog title="Project Creative Brief"
                   :visible.sync="showCreativeBriefDialog"
                   class="creative-brief-dialog">
            <quill-editor v-model="creative_brief"
                          ref="myQuillEditor"
                          :options="editor_option">
            </quill-editor>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="saveCreativeBrief">Save</el-button>
                <el-button @click="showCreativeBriefDialog = false">Cancel</el-button>
            </span>
        </el-dialog>

        <!-- New Revision Dialog -->
        <el-dialog
            title="Upload New Proofs"
            :visible.sync="showNewRevisionDialog"
            :close-on-click-modal="false"
            :close-on-press-escape="false"
            :show-close="false"
            @close="onCloseUploadDialog"
            class="upload-dialog">
            <h4 class="text-success">Send proofs for next Revision Round</h4>
            <el-upload
                name='photos'
                list-type="picture"
                action='/api/files/upload'
                :data="uploadFormData"
                :file-list="uploadFileList"
                drag multiple>
                <i class="el-icon-upload"></i>
                <div class="el-upload__text">Drop file here or <em>click to upload</em></div>
            </el-upload>
            <span slot="footer" class="dialog-footer">
                <el-button @click="cancelNewRevision()">Cancel</el-button>
                <el-button type="primary" @click="sendNewRivision()">Send</el-button>
            </span>
        </el-dialog>

        <!-- Send Corrections Dialog -->
        <el-dialog
            title="Send Corrections"
            :visible.sync="showSendCorrectionsDialog"
            class="send-corrections">
            <h4>
                Do you want to send the corrections on 
                <span class="text-success">
                    Rivision Round {{ current_revision.version }}
                </span>
                ?
            </h4>
            <span slot="footer" class="dialog-footer">
                <el-button @click="showSendCorrectionsDialog = false">No</el-button>
                <el-button type="primary" @click="sendCorrections">Yes</el-button>
            </span>
        </el-dialog>

        <!-- New Collaborator Dialog -->
        <el-dialog
            title="New Collaborators"
            :visible.sync="showNewCollaboratorDialog"
            @close="onCloseNewCollaboratorDialog"
            class="collaborator-dialog">
            <div v-if="isMobile" class="collaborators">
                <img
                    v-for="collaborator in project.collaborators" :key="collaborator.id"
                    class="collaborator user-avatar" :src="collaborator.photo_url">
            </div>
            <span class="text-center">
                <h5>Please add other collaborators here by email</h5>
                <div>
                    <el-tag
                        :key="collaborator"
                        v-for="collaborator in newCollaborators"
                        closable
                        :disable-transitions="false"
                        @close="handleClose(collaborator)">
                        {{ collaborator }}
                    </el-tag>
                    <el-input
                        class="input-new-tag"
                        v-if="collaboratorInputVisible"
                        v-model="collaboratorInputValue"
                        ref="saveTagInput"
                        size="mini"
                        @keyup.enter.native="handleInputConfirm"
                        @blur="handleInputConfirm"
                        >
                    </el-input>
                    <el-button v-else class="button-new-tag" size="small" @click="showInput">+ New Collaborator</el-button>
                </div>
            </span>
            <span slot="footer" class="dialog-footer">
                <el-button @click="showNewCollaboratorDialog = false">Cancel</el-button>
                <el-button type="primary" @click="inviteCollaborators()">Invite</el-button>
            </span>
            <!-- Team members -->
            <div v-if="isMobile" class="team-members">
                <div class="heading">Team Members</div>
                <img
                    v-for="member in project.teamMembers" :key="member.id"
                    class="user-avatar" :src="member.photo_url">
            </div>
        </el-dialog>

        <!-- Image Preview Dialog -->
        <el-dialog
            :visible.sync="showImagePreviewDialog"
            class="image-preview">
            <img :src="imagePreviewUrl">
        </el-dialog>

        <!-- Upload Final Files Dialog -->
        <final-files-dialog 
            v-if="showFinalFilesDialog"
            :showFinalFilesDialog="showFinalFilesDialog"
            :currentRole="current_role"
            :project="project"
            :revisionId="revision_id"
            v-on:closeFinalFilesDialog="showFinalFilesDialog = false"
            v-on:filesSended="finalFilesSended">
        </final-files-dialog>

        <!--Prooflo Simplified Dialog-->
        <plugin-install-dialog v-if="promptPluginInstall"
                               :promptPluginInstall="promptPluginInstall"
                               :pluginInstalled="pluginInstalled"
                               v-on:continue="promptPluginInstall = false"
                               v-on:takeScreenshots="captureScreen"
                               v-on:installPlugin="installProofloPlugin">
        </plugin-install-dialog>

    </el-container>
</template>


<script>

import Konva from "konva";
import { Rate } from 'element-ui';

export default {
    
    props: [ 'project_id', 'revision_id', 'proof_id', 'issue_id' ],

    mixins: [],

    components: {
        'final-files-dialog': require('./partials/FinalFilesDialog'),
        'plugin-install-dialog': require('./partials/PluginInstallDialog'),
        'pdf-converting': require('./partials/PdfConverting'),
    },

    data() {
        return {

            // Loading
            fullscreenLoading: false,
            
            // User Data
            current_user: Spark.state.user,
            current_role: '',

            // Project Data
            project: {},
            current_revision: {},
            current_proof: {},
            current_issue: {},
            current_comment: {},
            creative_brief: '',

            // Canvas Data
            stage: {},
            layer: {},
            proof_image: {},

            // Canvas Status
            zoom: 0,
            zoom_old: 0,
            pen_active: false,
            showTooltip: true,

            // Issue Data
            newIssueForm: {
                description: '',
                active: false
            },
            isIssueDetails: false,
            newCommentForm: {
                description: ''
            },

            // Upload data
            uploadFileList: [],
            uploadFormData: {},
            imagePreviewUrl: '',
            pdfLoader: false,

            // Dialogs
            showUploadDialog: false,
            showSendCorrectionsDialog: false,
            showNewRevisionDialog: false,
            showNewCollaboratorDialog: false,
            showFinalFilesDialog: false,
            showCreativeBriefDialog: false,
            showImagePreviewDialog: false,

            // New Collaborator data
            newCollaborators: [],
            collaboratorInputVisible: false,
            collaboratorInputValue: '',

            // Mobile
            isMobile: false,
            mobileStatus: 'revision',

            // Quill Editor
            editor_option: {},

            // Prooflo Simplified
            pluginInstalled: false,
            promptPluginInstall: false,
        }
    },

    filters: {
        utc_to_local: function (date) {
            if (!date) return '';
            var utc = moment.utc(date).toDate();
            return moment(utc).local().format('dddd, MMMM DD, YYYY HH:mm:A');
        }
    },

    methods: {

        //////////////////
        // Handle Error //
        //////////////////
        handle_error(errors) {
            this.$notify.error({
                title: 'Error',
                message: 'An error occurs!'
            });
            this.fullscreenLoading = false;
        },
        
        ////////////////////
        // Goto Functions //
        ////////////////////

        goToDashboard() {
            // Goto Projects Page
            this.$router.push({ path: '/projects' });
        },

        goToRevision() {
            // Go To Proofer Page
            this.$router.push({
                name: 'proofer',
                params: {
                    project_id: this.project_id,
                    revision_id: this.current_revision.id
                }
            });

            // Load Revision Project
            this.loadInitialRevision(this.project_id, this.current_revision.id);
        },

        goToProof(proof) {
            var project_id = this.project_id;
            var revision_id = this.current_revision.id;
            var proof_id = proof.id;
            
            // Go To Proofer Page
            this.$router.push({
                path: `/proofer/${project_id}/${revision_id}/${proof_id}`
            });

            // Load Revision Project
            this.loadProof(proof);
        },

        goToIssue(issue) {
            var project_id = this.project_id;
            var revision_id = this.current_revision.id;
            var proof_id = this.current_proof.id;
            var issue_id = issue.id;
            
            // Go To Proofer Page
            this.$router.push({
                path: `/proofer/${project_id}/${revision_id}/${proof_id}/${issue_id}`
            });

            // Load Revision Project
            // this.loadInitialRevision(this.project_id, this.current_revision.id);
        },

        /////////////////////////
        // Load Init Functions //
        /////////////////////////

        loadInitialRevision(project_id, revision_id) {
            this.fullscreenLoading = true;
            axios
            .get('/api/projects/load/' + project_id + '/' + revision_id)
            .then(response => {
                if (response.data.status == 1) {

                    // Init project, revision, creative brief
                    this.project = response.data.data;
                    this.current_revision = this.project.last_revision;
                    this.creative_brief = this.project.creative_brief;

                    // Init plugin installation for client
                    if (this.project.firstOpen && this.project.project_type == 'website' && this.current_role == 'client') {
                        this.promptPluginInstall = true;
                    }

                    if (this.proof_id) {
                        this.current_revision.proofs.filter(proof => {
                            if (proof.id == this.proof_id) {
                                this.loadProof(proof);
                                proof.issues.forEach(issue => {
                                    if (issue.id == this.issue_id) {
                                        this.current_issue = issue;
                                        this.showIssueDetails(issue);
                                    }
                                })
                            }
                        });
                    }
                    else if (this.current_revision.proofs[0]) {
                        // Load first proof
                        this.loadProof(this.current_revision.proofs[0]);
                    } else {
                        this.fullscreenLoading = false;
                    }

                } else {
                    this.handle_error(response.data.errors);
                }
            })
            .catch(error => {
                console.log(error);
            });

            // Init upload form data
            this.uploadFormData = {
                file_type: 'picture',
                project_id: this.project_id,
                user_id: this.current_user.id,
                comment_id: '',
                owner_type: ''
            };
        },

        loadProof(proof) {
            // Set current proof
            this.current_proof = proof;
            this.current_proof.issues.forEach(issue => {
                if (this.project.project_type == 'website' && this.current_role == 'freelancer') {
                    issue.isCheck = issue.status == 'review';
                } else {
                    issue.isCheck = issue.status == 'done';
                }
            });
            this.initNewIssue();
            this.hideIssueDetails();

            // Init stage
            this.initializeStage(proof);
        },

        deleteProof(proof) {
            this.$confirm('This will permanently delete the proof. Continue?', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'warning'
            }).then(() => {
                var index = this.current_revision.proofs.indexOf(proof);
                axios
                .delete('/api/proof/delete_proof/' + proof.id)
                .then(response => {
                    if (response.data.status == 1) {
                        this.current_revision.proofs.splice(index, 1);
                        this.loadInitialRevision(this.project_id, this.revision_id);
                        
                        this.$message({
                            type: 'success',
                            message: 'Delete completed'
                        });
                    } else {
                        self.handle_error(response.data.errors);
                    }
                })
                .catch(error => {
                    console.log(error);
                });

            });
        },

        downloadProof(proof) {
            var link = document.createElement('a');
            link.href = '/storage/' + proof.project_files.path;
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        },

        getCurrentRole(project_id) {
            axios
            .get('/api/auth/getCurrentRole/' + project_id)
            .then(response => {
                if (response.data.status == 1) {
                    this.current_role = response.data.data.user_role;
                } else {
                    this.handle_error(response.data.errors);
                }
            })
            .catch(error => {
                console.log(error);
            });
        },

        getunreadComments(proof) {
            let unread = 0;
            proof.issues.forEach((issue) => {
                if (issue.unread_comments) unread += issue.unread_comments.length;
            });
            return unread;
        },

        getReviewCount(proof) {
            let count = 0;
            proof.issues.forEach((issue) => {
                if (this.current_role == 'freelancer') {
                    if (issue.status == 'todo') count ++;
                }
                if (this.current_role == 'client') {
                    if (issue.status == 'review') count ++;
                }
            });
            return count;
        },

        ////////////////////////////
        // Canvas Stage Functions //
        ////////////////////////////

        initializeStage(proof) {
            var self = this;

            var imageObj = new Image();
            imageObj.src = '/storage/' + proof.project_files.path;
            imageObj.onload = function() {
                if (!proof.canvas_data) {
                    self.stage = new Konva.Stage({
                        container: 'stage'
                    });
                    self.layer = new Konva.Layer({
                        draggable: true
                    });
                    self.stage.add(self.layer);
                    self.proof_image = new Konva.Image();
                    self.layer.add(self.proof_image);
                } else {
                    self.stage = Konva.Node.create(proof.canvas_data, 'stage');
                    self.layer = self.stage.get('Layer')[0];
                    self.proof_image = self.layer.get('Image')[0];
                    self.layer.getChildren((node) => {
                        if (node.getClassName() == 'Group') {
                            self.enableAnchorsDrag(node);
                            node.on('dragend', () => { self.saveStage() });
                        }
                    });
                }
                self.proof_image.setImage(imageObj);
                self.fitStageSize();
                self.fitImageSize();
                self.layer.batchDraw();
                self.initPen();
                self.fullscreenLoading = false;
            };
        },

        saveStage() {
            var self = this;
            
            var form_data = {
                revision_id: self.current_proof.revision_id,
                id: self.current_proof.id,
                canvas_data: self.stage.toJSON()
            };

            axios
            .post('/api/proof/save', form_data)
            .then(response => {
                if (response.data.status == 1) {
                    self.current_proof.canvas_data = response.data.data[0].canvas_data;
                } else {
                    self.handle_error(response.data.errors);
                }
            })
            .catch(error => {
                console.log(error);
            });
        },

        initPen() {
            var self = this;
            var is_dragging = false;

            ////////////////
            // Drag Group //
            ////////////////
            var colors = {
                main: self.current_role == 'freelancer' ? '#545C64' : '#EF5B5B',
                white: '#FAFAFA'
            };

            var bt_group = new Konva.Group({
                name: 'bt-group',
                draggable: true
            });

            var bt_outline = new Konva.Rect({
                name: 'bt-outline',
                stroke: colors.white
            });
            bt_group.add(bt_outline);

            var bt_rect = new Konva.Rect({
                name: 'bt-rect',
                stroke: colors.main
            });
            bt_group.add(bt_rect);

            var bt_anchors = [];
            for (var i = 0; i < 3; i ++) {
                var bt_anchor = new Konva.Circle({
                    name: 'bt-anchor',
                    fill: colors.main,
                    stroke: colors.white,
                    draggable: true
                });

                bt_anchors.push(bt_anchor);
                bt_group.add(bt_anchor);
            }

            var bt_circle = new Konva.Circle({
                name: 'bt-circle',
                fill: colors.main,
                stroke: colors.white
            });
            bt_group.add(bt_circle);

            var bt_text = new Konva.Text({
                name: 'bt-text',
                fill: colors.white
            });
            bt_group.add(bt_text);

            /////////////////
            // Mouse Event //
            /////////////////

            self.layer.on('mousedown touchstart', function(e) {
                if (
                    !self.pen_active || 
                    e.target.getClassName() != 'Image' ||
                    self.newIssueForm.active
                ) return;

                this.draggable(false);
                is_dragging = true;

                startDrag( getMousePos(e) );
            });

            self.layer.on('mousemove touchmove', function(e) {
                if (!is_dragging) return;

                updateDrag( getMousePos(e) );
            });

            self.layer.on('mouseup touchend', function(e) {
                if (!is_dragging) return;

                this.draggable(true);
                is_dragging = false;

                saveDrag();
            });

            ////////////////////
            // Drag Functions //
            ////////////////////

            function startDrag(pos) {
                var scale = self.layer.scaleX();

                var issues = self.current_proof.issues;
                var count = issues.length 
                    ? Number(issues[issues.length - 1].label) + 1
                    : 1;

                bt_group.setAttrs({
                    x: pos.x,
                    y: pos.y,
                    label: count
                });

                bt_text.setAttrs({
                    text: count,
                    align: 'center'
                });

                self.adjustScaleofGroup(bt_group);

                self.layer.add(bt_group);
            }

            function updateDrag(pos) {
                var width = pos.x - bt_group.x();
                var height = pos.y - bt_group.y();

                bt_rect.setAttrs({
                    width: width,
                    height: height
                });

                bt_outline.setAttrs({
                    width: width,
                    height: height
                });

                bt_anchors[0].x(width);
                bt_anchors[1].setAttrs({
                    x: width,
                    y: height
                });
                bt_anchors[2].y(height);

                self.layer.batchDraw();
            }

            function saveDrag() {
                // Add group into layer
                var group = self.convertGroup(bt_group);
                self.enableAnchorsDrag(group);
                group.on('dragend', () => { self.saveStage() });
                self.layer.add(group);

                // Remove drag group
                bt_rect.setAttrs({
                    width: 0,
                    height: 0
                });
                bt_outline.setAttrs({
                    width: 0,
                    height: 0
                });
                bt_anchors.forEach((anchor) => {
                    anchor.position({ x: 0, y: 0 });
                });
                bt_group.remove();

                // Draw
                self.layer.batchDraw();

                // Ready new group
                self.newIssueForm.group = group;
                self.newIssueForm.active = true;
                // Hide issue details
                self.hideIssueDetails();
                // Show right sidebar
                if ($('.sidebar-right').hasClass('collapsed')) {
                    $('.sidebar-right').removeClass('collapsed');
                }
                // Focus on the input
                Vue.nextTick(function () {
                    self.$refs.newIssueForm_description.focus();
                });

                // For mobile
                if (self.isMobile) self.mobileStatus = 'comments';
            }

            function getMousePos(e) {
                var scale = self.zoom / 100;
                var pos = self.stage.getPointerPosition();

                pos = {
                    x: (pos.x - self.layer.x()) / scale,
                    y: (pos.y - self.layer.y()) / scale
                };
                pos = self.convertPosition(pos);

                return pos;
            }
        },

        convertGroup(drag_group) {
            var group = drag_group.clone({
                id: 'group_' + drag_group.getAttr('label')
            });
            return group;
        },

        adjustScaleofGroup(group) {
            var scale = this.layer.scaleX();
            group.getChildren((node) => {
                switch (node.name()) {
                    case 'bt-rect':
                        node.setAttrs({
                            strokeWidth: 2 / scale
                        });
                        break;
                    case 'bt-outline':
                        node.setAttrs({
                            strokeWidth: 6 / scale
                        });
                        break;
                    case 'bt-circle':
                        node.setAttrs({
                            radius: 16 / scale,
                            strokeWidth: 3 / scale
                        });
                        break;
                    case 'bt-text':
                        node.setAttrs({
                            fontSize: 16 / scale,
                            offsetX: 9 / scale,
                            offsetY: 7 / scale,
                            width: 18 / scale,
                        });
                        break;
                    case 'bt-anchor':
                        node.setAttrs({
                            radius: 4 / scale,
                            strokeWidth: 2 / scale
                        });
                        break;
                }
            })
        },

        enableAnchorsDrag(group) {
            var circles = group.get('Circle');

            var rect = group.get('Rect')[1];
            var outline = group.get('Rect')[0];
            var text = group.get('Text')[0];

            circles.forEach((circle, index) => {
                var indexes = [
                    (index + 2) % 4,
                    (index % 2) ? (index + 3) % 4 : (index + 1) % 4, 
                    (index % 2) ? (index + 1) % 4 : (index + 3) % 4,
                ];

                circle.on('dragmove', function(e) {
                    var pos = circles[indexes[0]].position();
                    var current_pos = circle.position();

                    rect.position(pos);
                    rect.width(current_pos.x - pos.x);
                    rect.height(current_pos.y - pos.y);

                    outline.position(pos);
                    outline.width(current_pos.x - pos.x);
                    outline.height(current_pos.y - pos.y);

                    circles[indexes[1]].setX(current_pos.x);
                    circles[indexes[2]].setY(current_pos.y);

                    if (index == 0) {
                        text.y(circles[indexes[2]].y());
                    } else if (index == 2) {
                        text.x(circles[indexes[1]].x());
                    }
                });
            });
        },

        convertPosition(pos) {
            switch (this.layer.rotation()) {
                case 90:
                    pos = {
                        x: pos.y,
                        y: -pos.x
                    };
                    break;
                case 180:
                    pos = {
                        x: -pos.y,
                        y: pos.x
                    };
                case 270:
                    pos = {
                        x: -pos.y,
                        y: pos.x
                    };
            }
            return pos;
        },

        convertLengths(width, height) {
            switch (this.layer.rotation()) {
                case 0:
                    return {width: width, height: height};
                case 90:
                    return {width: -height, height: width};
                case 180:
                    return {width: -width, height: -height};
                case 270:
                    return {width: height, height: -width};
            }
        },

        ////////////////////////
        // Set size Functions //
        ////////////////////////

        fitStageSize() {
            var container = this.isMobile
                ? document.getElementsByClassName('proof-container')[0]
                : this.stage.container();

            this.stage.width(container.offsetWidth);
            this.stage.height(container.offsetHeight);
        },

        fitImageSize(option) {
            var self = this;

            var project_type = self.project.project_type;

            var stageWidth = self.stage.width();
            var stageHeight = self.stage.height() - 120;

            var imageWidth = self.proof_image.width();
            var imageHeight = self.proof_image.height();

            if (self.layer.rotation() == 90 || self.layer.rotation() == 270) {
                var temp = imageWidth;
                imageWidth = imageHeight;
                imageHeight = temp;
            }

            var scale = 
                ( project_type == 'website'  || option == 'fullwidth' )
                ? stageWidth / imageWidth
                : Math.min(stageWidth / imageWidth, stageHeight / imageHeight);

            if (option == 'fitscreen') {
                scale = Math.min(stageWidth / imageWidth, stageHeight / imageHeight);
            }

            self.zoom = Math.floor(scale * 100);
            self.zoom_old = self.zoom;
            scale = self.zoom / 100;

            var lengths = self.convertLengths(self.proof_image.width(), self.proof_image.height());
            var pos = {
                x: project_type == 'website' ? 0 : (stageWidth - lengths.width * scale) / 2,
                y: project_type == 'website' ? 60 : (stageHeight - lengths.height * scale) / 2 + 60
            };

            if (option == 'fitscreen') {
                pos = {
                    x: (stageWidth - lengths.width * scale) / 2,
                    y: (stageHeight - lengths.height * scale) / 2 + 60
                };
            }

            self.layer.setAttrs({
                scaleX: scale,
                scaleY: scale,
                x: pos.x,
                y: pos.y
            });
            self.layer.batchDraw();
        },

        setZoom(zoom) {
            var self = this;

            if (zoom) self.zoom = zoom;

            var scale = self.zoom / 100;
            var old_scale = self.zoom_old / 100;

            if (typeof self.layer.scale !== 'function') return;

            /////////////////
            // Zoom Center //
            /////////////////

            self.layer.scale({ x: scale, y: scale });

            var pos_bg = self.layer.position();
            var pos_center = {
                x: self.stage.width() / 2,
                y: self.stage.height() / 2
            };

            var diff_scale = scale - old_scale;

            var diff = {
                x: diff_scale / old_scale * (pos_bg.x - pos_center.x),
                y: diff_scale / old_scale * (pos_bg.y - pos_center.y),
            };
            self.layer.move(diff);
            self.zoom_old = self.zoom;

            /////////////////////
            // Scale Of Groups //
            /////////////////////

            self.layer.getChildren((node) => {
                if (node.getClassName() == 'Group') {
                    self.adjustScaleofGroup(node);
                }
            });

            // Draw
            self.layer.batchDraw();
        },

        rotateImage() {
            this.layer.rotate(90);
            if (this.layer.rotation() == 360) this.layer.rotation(0);

            var scale = this.layer.scaleX();
            var width = this.proof_image.width() * scale;
            var height = this.proof_image.height() * scale;

            var diff = {
                x: -width + height,
                y: -height - width
            };

            switch (this.layer.rotation()) {
                case 90:
                    diff = {
                        x: width + height,
                        y: height - width
                    };
                    break;
                case 180:
                    diff = {
                        x: width - height,
                        y: height + width
                    };
                    break;
                case 270:
                    diff = {
                        x: -width - height,
                        y: -height + width
                    };
                    break;
            }

            this.layer.move({
                x: diff.x / 2,
                y: diff.y / 2
            });

            this.layer.batchDraw();
        },

        togglePen() {
            this.pen_active = !this.pen_active;
            this.showTooltip = false;
        },

        handlePageChange(page_number) {
            this.loadProof(this.current_revision.proofs[page_number - 1]);
        },

        /////////////////////
        // Issue Functions //
        /////////////////////

        reverse: function (array) {
            if (!array) return;
            return array.slice().reverse();
        },

        addNewIssue() {
            var self = this;

            self.$refs['newIssueForm'].validate((valid) => {
                if (valid) {
                    self.fullscreenLoading = true;

                    var form_data = {
                        proof_id: self.current_proof.id,
                        description: self.newIssueForm.description,
                        group: self.newIssueForm.group.getAttr('id'),
                        label: self.newIssueForm.group.getAttr('label'),
                        owner_type: self.current_role,
                        project_type: self.project.project_type
                    };
                    axios
                    .post('/api/proof/create_issue', form_data)
                    .then(response => {
                        if (response.data.status == 1) {
                            self.current_issue = response.data.data[0];
                            self.current_issue.isCheck = false;
                            self.current_issue.comments = [];

                            // Add issue to the list
                            self.current_proof.issues.push(self.current_issue);
                            self.saveStage();

                            // Init new issue form
                            self.initNewIssue();

                            // Update progress
                            self.project.total_issues ++;

                            // Upload attach file
                            self.uploadFormData.owner_type = 'issue';
                            self.uploadFormData.issue_id = response.data.data[0].id;
                            self.$refs['upload_new_issue'].submit();

                            self.fullscreenLoading = false;
                        } else {
                            self.handle_error(response.data.errors);
                        }
                    })
                    .catch(error => {
                        console.log(error);
                    });
                } else {
                    return false;
                }
            });
        },

        cancelNewIssue() {
            // Remove group from canvas
            this.newIssueForm.group.remove();
            this.layer.batchDraw();

            // Init new issue form
            this.initNewIssue();
        },

        initNewIssue() {
            this.newIssueForm.active = false;

            setTimeout(() => {
                this.$refs['newIssueForm'].resetFields();
                this.newIssueForm = {
                    description: ''
                };
            }, 100);
        },

        toggleIssueStatus(issue, status) {
            var self = this;

            if (status) {
                issue.status = 'done';
            } else {
                if (self.project.project_type == 'website') {
                    if (self.current_role == 'freelancer') {
                        issue.status = issue.isCheck ? 'review' : 'todo';
                    } else {
                        issue.status = issue.isCheck ? 'done' : 'todo';
                    }
                } else {
                    issue.status = issue.isCheck ? 'done' : 'todo';
                }
            }

            var colors = {
                main: issue.status == 'done'
                    ? '#80B4A0' 
                    : (issue.owner_type == 'freelancer' ? '#545C64' : '#Ef5B5B'),
                cover: issue.status == 'done' ? 'rgba(128,180,160, .5)' : ''
            };

            self.fullscreenLoading = true;
            axios
            .get('/api/proof/change_issue_status/' + issue.id + '/' + issue.status + '/' + this.project.project_type)
            .then(response => {
                if (response.data.status == 1) {
                    this.current_proof.status = response.data.data[0].status;

                    // Update canvas group
                    var group = self.layer.get('#' + issue.group)[0];
                    group.getChildren((node) => {
                        if (node.name() == 'bt-rect') {
                            node.setAttrs({
                                stroke: colors.main,
                                fill: colors.cover
                            });
                        } else if (node.getClassName() == 'Circle') {
                            node.setAttr('fill', colors.main);
                        }
                    });
                    self.layer.batchDraw();
                    self.saveStage();

                    // Update progress bar
                    if (issue.status == 'done') {
                        self.project.solved_issues ++;
                    }
                    if (issue.status == 'todo') {
                        if (self.project.project_type == 'website' && self.current_role == 'freelancer') {
                            // 
                        } else {
                            self.project.solved_issues --;
                        }
                    }
                    self.project.percentage = Math.round(
                        self.project.solved_issues / self.project.total_issues * 100
                    );

                    self.fullscreenLoading = false;
                } else {
                    self.handle_error(response.data.errors);
                }
            })
            .catch(error => {
                this.handle_error(error);
            });
        },

        showIssueGroup(group_id) {
            var self = this;

            // Set fullscreen
            var scale = self.stage.width() / self.proof_image.width();
            self.setZoom( Math.floor(scale * 100) );

            var group = self.layer.get('#' + group_id)[0];
            var scale = self.zoom / 100;

            var lengths = self.convertLengths(
                group.x() * scale,
                group.y() * scale );
            
            var pos = {
                x: self.layer.x() + lengths.width,
                y: self.layer.y() + lengths.height
            };

            var diff = {
                x: self.stage.width() / 2 - pos.x,
                y: self.stage.height() / 2 - pos.y
            };

            moveScreen();

            var i = 0;
            function moveScreen() {
                var sec = 20;
                if (i == (sec - 1)) return;

                self.layer.move({
                    x: diff.x / sec,
                    y: diff.y / sec
                });
                self.layer.batchDraw();

                i++;
                setTimeout(moveScreen, 1);
            }
        },

        showIssueDetails(issue) {
            var self = this;

            self.current_issue = issue;
            self.isIssueDetails = true;

            this.goToIssue(issue);

            axios
            .delete('/api/proof/delete_issue_unread_comments/' + issue.id)
            .then(response => {
                if (response.data.status == 1) {
                    issue.unread_comments = [];
                } else {
                    self.handle_error(response.data.errors);
                }
            })
            .catch(error => {
                self.handle_error(error);
            });
        },

        hideIssueDetails() {
            this.isIssueDetails = false;
        },

        editIssue(issue, index) {
            if (issue.user_id != this.current_user.id) return;
            this.current_issue = issue;

            if (index < 0) {
                $('.current-issue').addClass('editable');
            } else {
                $($('.issue-box')[index]).addClass('editable');
            }
        },

        deleteIssue(issue) {
            this.$confirm('This will permanently delete the issue. Continue?', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'warning'
            }).then(() => {
                var index = this.current_proof.issues.indexOf(issue);
                axios
                .delete('/api/proof/delete_issue/' + issue.id)
                .then(response => {
                    if (response.data.status == 1) {
                        this.current_proof.issues.splice(index, 1);

                        this.layer.get('#' + issue.group)[0].remove();
                        this.saveStage();

                        this.$message({
                            type: 'success',
                            message: 'Delete completed'
                        });
                    } else {
                        self.handle_error(response.data.errors);
                    }
                })
                .catch(error => {
                    console.log(error);
                });
            });
        },

        updateIssue(issue, index) {

            if (index < 0) {
                $('.current-issue').removeClass('editable');
            } else {
                $($('.issue-box')[index]).removeClass('editable');
            }

            var formData = {
                id: issue.id,
                description: issue.description
            };
            axios
            .post('/api/proof/create_issue', formData)
            .then(response => {
                if (response.data.status == 1) {
                    this.uploadFormData.owner_type = 'issue';
                    this.uploadFormData.issue_id = issue.id;

                    if (index < 0) {
                        this.$refs['upload_current_issue_' + issue.id].submit();
                    } else {
                        this.$refs['upload_issue_' + issue.id][0].submit();
                    }
                } else {
                    self.handle_error(response.data.errors);
                }
            })
            .catch(error => {
                console.log(error);
            });
        },

        onIssueImageUploaded(response) {
            this.current_issue.files.push({
                id: response.data.id,
                path: response.data.path,
                thumb_path: response.data.thumb
            });
            
            // this.uploadFileList = [];
        },

        ///////////////////////
        // Comment Functions //
        ///////////////////////
        addNewComment() {
            var self = this;

            var form_data = {
                project_id: self.project_id,
                revision_id: self.revision_id,
                issue_id: self.current_issue.id,
                description: self.newCommentForm.description,
            };
            self.$refs['newCommentForm'].resetFields();

            self.fullscreenLoading = true;
            axios
            .post('/api/proof/add_comment', form_data)
            .then(response => {
                if (response.data.status == 1) {
                    self.current_comment = response.data.data[0];
                    self.current_issue.comments.push(self.current_comment);

                    if (self.project.project_type == 'website' && self.current_issue.status != 'todo') {
                        self.current_issue.isCheck = false;
                        self.toggleIssueStatus(self.current_issue);
                    }

                    self.uploadFormData.owner_type = 'comment';
                    self.uploadFormData.comment_id = self.current_comment.id;
                    self.$refs['upload_new_comment'].submit();

                    this.fullscreenLoading = false;
                } else {
                    self.handle_error(response.data.errors);
                }
            })
            .catch(error => {
                console.log(error);
            });
        },

        onCommentImageUploaded(response) {
            this.current_comment.files.push({
                id: response.data.id,
                path: response.data.path,
                thumb_path: response.data.thumb
            });

            // this.uploadFileList = [];
        },

        editComment(comment, index) {
            if (comment.user_id != this.current_user.id) return;
            this.current_comment = comment;
            $($('.comment-box')[index]).addClass('editable');
        },

        deleteComment(comment) {
            this.$confirm('This will permanently delete the comment. Continue?', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'warning'
            }).then(() => {
                var index = this.current_issue.comments.indexOf(comment);
                axios
                .delete('/api/proof/delete_comment/' + comment.id)
                .then(response => {
                    if (response.data.status == 1) {
                        this.current_issue.comments.splice(index, 1);

                        this.$message({
                            type: 'success',
                            message: 'Delete completed'
                        });
                    } else {
                        self.handle_error(response.data.errors);
                    }
                })
                .catch(error => {
                    console.log(error);
                });
            });
        },

        updateComment(comment, index) {
            $($('.comment-box')[index]).removeClass('editable');
            this.fullscreenLoading = true;

            var formData = {
                id: comment.id,
                description: comment.description,
                issue_id: this.current_issue.id,
                revision_id: this.revision_id,
                project_id: this.project_id
            };
            axios
            .post('/api/proof/add_comment', formData)
            .then(response => {
                if (response.data.status == 1) {
                    this.uploadFormData.owner_type = 'comment';
                    this.uploadFormData.comment_id = comment.id;
                    this.uploadFormData.issue_id = this.current_issue.id;
                    this.$refs['upload_comment_' + comment.id][0].submit();

                    this.fullscreenLoading = false;
                } else {
                    self.handle_error(response.data.errors);
                }
            })
            .catch(error => {
                console.log(error);
            });
        },

        //////////////////////
        // Dialog Functions //
        //////////////////////

        onCloseUploadDialog() {
            this.uploadFileList = [];
        },

        // Upload Dialog
        openUploadDialog() {            
            this.uploadFormData.owner_type = 'proof';
            this.showUploadDialog = true;
        },
        handleUpload(file) {
            if (file.type == 'application/pdf') {
                this.pdfLoader = true;
            }
        },
        handleSuccess(response) {
            var self = this;
            if (response.status == 1) {
                if (response.data.length) {
                    response.data.forEach(function (element, index) {
                        self.uploadFileList.push({
                            name: 'Converted Image-' + (index + 1),
                            url: '/storage/' + element.path,
                        })
                    })
                    setTimeout(function () {
                        self.pdfLoader = false;
                    }, 500)
                }
            } else {
                this.pdfLoader = false;
                toastr['error'](response.error, 'Error');
            }
        },
        sendUploadFiles() {
            this.showUploadDialog = false;
            this.loadInitialRevision(this.project_id, this.revision_id);
        },

        // Creative Brief Dialog
        openCreativeBriefDialog() {
            this.showCreativeBriefDialog = true;
        },
        saveCreativeBrief() {
            this.fullscreenLoading = true;
            let formData = {
                project_id: this.project_id,
                creative_brief: this.creative_brief
            };
            axios.post('/api/projects/save-creative-brief', formData)
                .then(response => {
                    this.fullscreenLoading = false;
                    if (response.data.status) {
                        this.showCreativeBriefDialog = false;
                        this.loadInitialRevision(this.project_id, this.revision_id);
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
                    this.fullscreenLoading = false;
                    this.$notify({
                        title: 'Error',
                        message: 'Something went wrong please try again later',
                        type: 'error'
                    });
                })
        },
        clearEditor() {
            this.creative_brief = '';
        },
        resetDefault() {
            this.creative_brief = this.project.creative_brief;
        },

        // New Revision Dialog
        openNewRevisionDialog() {
            axios
            .post('/api/revisions/create', {
                project_id: this.project_id
            })
            .then(response => {
                if (response.data.status == 1) {
                    this.uploadFormData.owner_type = 'proof';
                    this.showNewRevisionDialog = true;
                } else {
                    self.handle_error(response.data.errors);
                }
            })
            .catch(error => {
                console.log(error);
            });
        },
        sendNewRivision() {
            this.showNewRevisionDialog = false;
            this.fullscreenLoading = true;
            
            axios
            .get('/api/projects/send_project/' + this.project_id + '/' + this.current_role)
            .then(response => {
                if (response.data.status == 1) {
                    this.fullscreenLoading = false;
                    var revision_id = response.data.data[0];
                    this.loadInitialRevision(this.project_id, revision_id);
                    this.$alert(
                        'Revision round has been sent by email successfully',
                        'Success',
                        {
                            confirmButtonText: 'OK'
                        }
                    );
                } else {
                    self.handle_error(response.data.errors);
                }
            })
            .catch(error => {
                console.log(error);
            });
        },
        cancelNewRevision() {
            axios
            .delete('/api/revisions/delete/' + this.project_id)
            .then(response => {
                if (response.data.status == 1) {
                    this.showNewRevisionDialog = false;
                } else {
                    self.handle_error(response.data.errors);
                }
            })
            .catch(error => {
                console.log(error);
            });
        },

        sendCorrections() {
            let self = this
            this.showSendCorrectionsDialog = false;
            this.fullscreenLoading = true;

            axios
            .get('/api/projects/submit_corrections/' + this.project_id)
            .then(response => {
                if (response.data.status == 1) {

                    // Identify project owner on Gist
                    convertfox.identify(response.data.data.owner.id, {
                        email: response.data.data.owner.email,
                        name: response.data.data.owner.name
                    });

                    // Trigger 'Send Corrections' event
                    convertfox.track("Send Corrections", {
                        userId: response.data.data.owner.id,
                        projectId: this.project_id
                    });

                    this.fullscreenLoading = false;
                    this.current_revision.status_revision = response.data.data.revision.status_revision;
                    this.project.project_status = response.data.data.revision.status_revision;
                    this.$notify({
                        title: 'Success',
                        message: 'Corrections sent successfully',
                        type: 'success'
                    });
                } else {
                    self.handle_error(response.data.errors);
                }
            })
            .catch(error => {
                console.log(error);
            });
        },

        openImagePreview(image_url) {
            this.imagePreviewUrl = image_url;
            this.showImagePreviewDialog = true;
        },

        deleteImage(item, file, type) {
            console.log(item);
            let self = this;
            this.$confirm('This will permanently delete the file. Continue?', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'warning'
            }).then(() => {
                axios
                .delete('/api/files/delete/' + file.id, {
                    params: {
                        type: type
                    }
                })
                .then(response => {
                    if (response.data.status == 1) {
                        item.files.forEach(function (element, index) {
                            if (element.id == file.id) {
                                item.files.splice(index, 1);
                                return;
                            }
                        });
                        this.$message({
                            type: 'success',
                            message: 'Delete completed'
                        });
                    } else {
                        this.handle_error(response.data.errors);
                    }
                })
                .catch(error => {
                    console.log(error);
                });
            });
        },

        finalFilesSended(message) {
            this.showFinalFilesDialog = false;
            swal({
                title: 'Success',
                text: message,
                type: 'success'
            })
            .then((result) => {
                window.location.href = "/projects";
            });
        },

        ////////////////////////////////
        // New Collaborator Functions //
        ////////////////////////////////
        handleClose(collaborator) {
            this.newCollaborators.splice(
                this.newCollaborators.indexOf(collaborator), 1
            );
        },

        showInput() {
            this.collaboratorInputVisible = true;
            this.$nextTick(_ => {
                this.$refs.saveTagInput.$refs.input.focus();
            });
        },

        handleInputConfirm() {
            let inputValue = this.collaboratorInputValue;
            if (inputValue) {
                this.newCollaborators.push(inputValue);
            }
            this.collaboratorInputVisible = false;
            this.collaboratorInputValue = '';
        },

        inviteCollaborators() {
            if (this.newCollaborators.length > 0) {
                this.fullscreenLoading = true;

                var formData = {
                    project_id: this.project_id,
                    collaborators: this.newCollaborators
                };
                axios
                .post('/api/projects/inviteCollaborators', formData)
                .then(response => {
                    if (response.data.status == 1) {
                        this.showNewCollaboratorDialog = false;
                        this.$notify({
                            title: 'Success',
                            message: 'Your invite sent successfully',
                            type: 'success'
                        });
                        this.loadInitialRevision(this.project_id, this.revision_id);
                        this.fullscreenLoading = false;
                    } else {
                        self.handle_error(response.data.errors);
                    }
                })
                .catch(error => {
                    console.log(error);
                });
            } else {
                this.$notify({
                    title: 'Warning',
                    message: 'Please add some collaborators',
                    type: 'warning'
                });
            }
        },

        onCloseNewCollaboratorDialog() {
            this.newCollaborators = [];
        },

        toggleSidebar(sidebar) {
            $('.sidebar-' + sidebar).toggleClass('collapsed');
            setTimeout(() => {
                this.fitStageSize();
            }, 300);
        },

        ////////////////////////
        // Revision Functions //
        ////////////////////////
        approveProject() {
            this.$confirm('Are you sure you want to approve this project?', {
                confirmButtonText: 'OK',
                cancelButtonText: 'Cancel',
                type: 'warning'
            }).then(() => {
                this.fullscreenLoading = true;
                var formData = {
                    project_id: this.project_id,
                    revision_id: this.revision_id,
                    decision: 'approved'
                };
                axios
                .post('/api/projects/approve_project', formData)
                .then(response => {
                    if (response.data.status == 1) {
                        // Identify client on Gist
                        convertfox.identify(response.data.data.client.id, {
                            email: response.data.data.client.email,
                            name: response.data.data.client.name
                        });

                        // Trigger 'Approve Project' event
                        convertfox.track("Approve Project", {
                            userId: response.data.data.client.id,
                            projectId: this.project_id
                        });

                        this.loadInitialRevision(this.project_id, this.revision_id);
                        this.fullscreenLoading = false;

                        this.$notify({
                            title: 'Success',
                            message: 'The decision has been sent by email successfully',
                            type: 'success'
                        });
                    } else {
                        self.handle_error(response.data.errors);
                    }
                })
                .catch(error => {
                    self.handle_error(error);
                });
            });
        },

        //////////////////////
        // Mobile Functions //
        //////////////////////
        detectMobile() {            
            var testExp = new RegExp(
                'Android | webOS | iPhone | iPad |' +
                'BlackBerry | Windows Phone' +
                'Opera Mini | IEMobile | Mobile' +
                'i'
            );
            if (testExp.test(navigator.userAgent)) this.isMobile = true;
        },

        enableStageEditable() {
            if (this.mobileStatus == 'revision') this.mobileStatus = 'canvas';
        },

        toggleComments() {
            this.mobileStatus = this.mobileStatus == 'comments'
                ? 'canvas'
                : 'comments';
        },

        //////////////////////
        // jQuery Functions //
        //////////////////////

        jqueryFunctions() {
            // Listen Stage Size
            this.listenStageSize();

            // Listen Zoom Buttons
            this.listenZoomButtons();

            // Enable Scroll
            this.listenScroll();
        },

        listenStageSize() {
            var self = this;
            // var resizeTimer;

            $(window).on('resize', function() {
                // clearTimeout(resizeTimer);
                // resizeTimer = setTimeout(function() {
                //     self.fitStageSize()
                // }, 250);

                self.fitStageSize();
            });
        },

        listenZoomButtons() {
            var self = this;

            $('.el-input-number__increase').click(function(event) {
                event.stopImmediatePropagation();

                if (self.zoom % 50 == 0) self.zoom += 50;
                else self.zoom += 50 - self.zoom % 50;
            });
            $('.el-input-number__decrease').click(function(event) {
                event.stopImmediatePropagation();
                
                if (self.zoom % 50 == 0) self.zoom -= 50;
                else self.zoom -= self.zoom % 50;

                if (self.zoom < 25) self.zoom = 25;
            });
        },

        listenScroll() {
            var self = this;
            
            let isFirefox = typeof InstallTrigger !== 'undefined';
            var event = isFirefox ? 'DOMMouseScroll' : 'mousewheel';

            $('#stage').bind(event, function(event){
                event.stopImmediatePropagation();
                event.preventDefault();

                var scale = self.layer.scaleX();
                var d = -60 * scale;

                if (event.originalEvent.wheelDelta > 0 || event.originalEvent.detail < 0) {
                    d = -d;
                }

                self.layer.move({ y: d });
                self.layer.batchDraw();
            });
        },

        urlify(text) {
            var urlRegex = /(https?:\/\/[^\s]+)/g;
            return text.replace(urlRegex, function(url) {
                return '<a href="' + url + '" target="_blank">' + url + '</a>';
            });
        },

        ///////////////////////////////////
        // Prooflo Simplified functions //
        /////////////////////////////////
        checkExtension() {
            var img, self = this;
            img = new Image();
            img.src = "chrome-extension://bdfmjjdclbpikogcdcfniohphenobffn/images/Icon_Initial_32.png";
            img.onload = function () {
                self.pluginInstalled = true;
            };
            img.onerror = function () {
                self.pluginInstalled = false;
            };
        },

        captureScreen() {
            // Send a message to the extension via the prooflo_ext_invoker.
            var prooflo_ext_invoker = document.getElementById('prooflo_ext_invoker');

            // Get required data for Prooflo Extension
            axios.get(`/api/v1/extension-data/${this.current_user.id}/${this.project_id}`)
                .then(response => {
                    if (response.status == 200) {
                        let data = response.data.data;
                        let extensionData = {
                            url: this.project.websiteURI,
                            loginToken: data.token,
                            projectId: this.project_id,
                            redirectUrl: data.redirect_url
                        };

                        // Call Extension
                        prooflo_ext_invoker.contentWindow.postMessage(JSON.stringify(extensionData), '*');
                    } else {
                        this.$notify({
                            title: 'Error',
                            message: 'API exception error occurred',
                            type: 'success'
                        });
                    }
                })
                .catch(error => {
                    this.$notify({
                        title: 'Error',
                        message: 'API exception error occurred',
                        type: 'error'
                    });
                })
        },

        installProofloPlugin() {
            this.promptPluginInstall = false;
            this.loadInitialRevision(this.project_id, this.revision_id);

            window.open('https://chrome.google.com/webstore/detail/prooflo-simplified/bdfmjjdclbpikogcdcfniohphenobffn', '_blank')
        },

    },
    
    watch: {
        pluginInstalled() {
            if (this.pluginInstalled) {
                let iFrame = '<iframe style="display:none" id="prooflo_ext_invoker" src="chrome-extension://bdfmjjdclbpikogcdcfniohphenobffn/invoker.html"></iframe>';
                $('.page-proof').append(iFrame);
            } else {
                $('#prooflo_ext_invoker').remove();
            }
        }
    },

    computed: {
        //
    },

    created() {
        // Get current role
        this.getCurrentRole(this.project_id);
        // Init project
        this.loadInitialRevision(this.project_id, this.revision_id);
        // Detect Mobile
        this.detectMobile();
    },

    mounted() {
        let self = this;

        // Remove navbar when from dashboard
        $('.with-navbar').css('padding', 0);
        $('.navbar').css('display', 'none');

        // Check Prooflo Simplified extension status
        setInterval(function() {
            self.checkExtension();
        }, 1000);

        // jQuery Functions
        this.jqueryFunctions();
    },

    updated() {
        // 
    },

    destroyed() {
        // Recover navbar when to dashboard
        $('.navbar').css('display', 'block');
    },

    beforeRouteEnter (to, from, next) {
        //Check if project owner subscription is not expired
        if (to.params.project) {
            if (to.params.project.active) {
                next();
            } else {
                next(false);
            }
        } else {
            axios.get(`/api/projects/details/${to.params.project_id}`)
                .then(response => {
                    if (response.data.status) {
                        if (response.data.data.active) {
                            next();
                        } else {
                            window.location.href = '/projects';
                        }
                    } else {
                        window.location.href = '/projects';
                    }
                })
                .catch(error => {
                    window.location.href = '/projects';
                })
        }
    },

}
</script>


<style lang="scss">

#captureWrapper {
    display: none;
}

//////////////////////////
// Variables and Mixins //
//////////////////////////

$white: #FAFAFA;
$black: #545C64;
$red: #Ef5B5B;
$green: #80B4A0;
$grey: #c0c4cc;
$border-color: rgba(0, 0, 0, .1);
$box-shadow: 0 2px 12px 0 $border-color;

@mixin transition($args...) {
    -webkit-transition: $args;
    -moz-transition: $args;
    -ms-transition: $args;
    -o-transition: $args;
    transition: $args;
}

.page-proof {
    height: 100%;
    background-color: $white;
}

//////////////////
// Proof Header //
//////////////////

.proof-header {
    padding: 0;
    box-shadow: 0 1px 12px $border-color;
    z-index: 2;

    .header-left {
        width: 200px;
    }
    .header-main {
        padding: 10px;
        text-align: center;
    }
}

// Nav Dashboard
.nav-dashboard {
    display: flex;
    align-items: center;
    padding: 15px;
    background-color: $black;
    color: $white;
    cursor: pointer;

    .menu-icon {
        font-size: 30px;
        margin-right: 15px;
        @include transition(.3s);
    }
    .menu-text {
        font-weight: 400;
        @include transition(.3s);
    }
    &:hover {
        .menu-icon {
            font-weight: bold;
        }
        .menu-text {
            margin-left: -7px;
        }
    }
}

.select-revision {
    margin-right: 10px;

    &:last-child {
        margin-right: 0;
    }
}

// User avatar
.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    padding: 0;
}

// Collaborators
.collaborators {
    display: flex;
    align-items: center;
    flex-direction: row-reverse;
    padding: 10px;

    .collaborator {
        margin-left: 10px;
    }
    .add-button {
        padding: 0;
        width: 34px;
        height: 34px;
        margin-left: 0;
    }
}

//////////////////
// Left Sidebar //
//////////////////

.proof-container {
    height: calc(100% - 60px);
}

.sidebar-left {
    width: 200px;
    box-shadow: 1px 12px 12px $border-color;
    z-index: 2;
    display: flex;
    flex-direction: column;
    @include transition(.3s);

    &.collapsed {
        margin-left: -200px;
    }
    .el-header {
        padding: 0;
    }
    .progress-area {
        padding: 15px;
        background-color: #F1F1F2;
    }
    .buttons-area {
        padding: 15px 15px 0 15px;
        background-color: $white;
        text-align: center;
    }
    .creative-brief {
        padding: 15px;
    }
    .el-main {
        padding: 0 15px;
    }
}

.project-progress {
    padding: 10px 0;

    .el-progress-bar__outer {
        background-color: $grey;
    }
    .el-progress-bar__inner {
        @include transition(.5s);
    }
}

.progress-info {
    font-size: 12px;
    text-align: center;
    .text-danger,
    .text-success {
        font-weight: bold;
        font-size: 120%;
    }
}

.team-members {
    margin-top: 5px;

    .user-avatar {
        width: 35px;
        height: 35px;
        margin-right: -2px;
    }
}

.button-group {
    width: 100%;

    .upload-button {
        width: 50%;
        font-weight: bold;
        padding: 0;

        div {
            margin-bottom: 5px;
        }
        img {
            width: 24px;
        }
        i {
            font-size: 26px;
            color: $black;
        }
    }
}
.upload-button {
    border-style: dashed;
    border-width: 2px;
    width: 100%;
    height: 50px;
    i {
        font-size: 20px;
        vertical-align: middle;
    }
    span {
        vertical-align: middle;
        font-weight: 400;
    }
}

.creative-brief-button {
    border-style: dashed;
    border-width: 2px;
    width: 100%;
    height: 50px;
    i {
        font-size: 20px;
        vertical-align: middle;
    }
    span {
        vertical-align: middle;
        font-weight: 400;
    }
}

.proof-thumbs {
    height: 100%;
    overflow: auto;
    padding: 15px;
}

.proof-thumb {
    height: 150px;
    margin-bottom: 15px;
    border-width: 2px;
    cursor: pointer;
    position: relative;

    &:last-child {
        margin-bottom: 0;
    }
    &.active {
        border-color: $red;

        .proof-number {
            background-color: $red;
        }
    }
    .el-card__body {
        height: 100%;
        padding: 0px;
    }
    img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    .proof-number {
        padding: 2px 5px;
        background-color: $black;
        color: $white;
        position: absolute;
        top: 0;
        left: 0;
        border-bottom-right-radius: 3px;
        width: 25px;
        height: 25px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .buttons-area {
        opacity: 0;
        @include transition(.3s);
        position: absolute;
        top: 0;
        right: 0;
        padding: 2px;
        background-color: transparent;
        .delete-button,
        .download-button {
            padding-left: 10px;
            padding-right: 10px;
        }
        .el-button + .el-button {
            margin-left: 5px;
        }
    }
    &:hover .buttons-area {
        opacity: 1;
    }
    .proof-unread {
        position: absolute;
        bottom: 0;
        right: 0;
        padding: 1px 7px;
        background-color: $red;
        color: $white;
        border-top-left-radius: 3px;
    }
    .review-comment {
        position: absolute;
        bottom: 10px;
        left: 0;
        width: 100%;
        text-align: center;
        .el-button {
            padding: 6px 12px;
            span {
                margin-left: 0;
            }
        }
    }
    .proof-check {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 137, 82, .4);
        color: $white;
        font-size: 100px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
}

////////////////
// Proof Main //
////////////////

.proof-main {
    position: relative;
    background-color: #ECECEC;
    padding: 0;
    overflow: hidden;
}

#stage {
    width: 100%;
    height: 100%;
}

.canvas-tools {
    position: absolute;
    top: 0;
    width: 100%;
    height: 60px;
    background-color: rgba(236, 236, 236, .9);
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;

    .canvas-tool {
        min-width: 40px;
        margin-left: 20px;

        &:first-child {
            margin-left: 0;
        }
    }
    .pen-tool {
        position: relative;
        .tool-tip {
            position: absolute;
            background-color: rgba(0, 0, 0, .5);
            color: $white;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 5px;
            width: 110px;
            text-align: center;
            margin-top: 10px;
            left: -35px;
            &:before {
                content: "";
                top: -15px;
                left: 50%;
                border: solid transparent;
                height: 0;
                width: 0;
                position: absolute;;
                border-bottom-color: rgba(0, 0, 0, .5);
                border-width: 8px;
                margin-left: -9px;
            }
        }
    }
}

.zoom-slider {
    width: 210px;
    margin-left: 20px;

    .el-slider__runway {
        margin-right: 120px;
        background-color: $white;
    }
    .el-slider__input {
        width: 110px;
    }
}

.proof-pagination {
    position: absolute;
    bottom: 0;
    width: 100%;
    height: 60px;
    background-color: rgba(236, 236, 236, .9);
    z-index: 2;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toggle-button {
    position: absolute;
    top: 0;
    width: 40px;
    height: 40px;
    font-size: 18px;
    padding: 5px;

    &.left {
        left: 0;
        margin-left: -2px;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
    }
    &.right {
        right: 0;
        margin-right: -2px;
        border-top-right-radius: 0;
        border-bottom-right-radius: 0;
    }
}

///////////////////
// Right Sidebar //
///////////////////

.sidebar-right {
    width: 300px;
    box-shadow: -1px 12px 12px $border-color;
    z-index: 1;
    position: relative;
    overflow: hidden;
    @include transition(.3s);

    &.collapsed {
        margin-right: -300px;
    }
    .comments-slide-enter-active, .comments-slide-leave-active {
        @include transition(.3s);
    }
    .comments-slide-enter, .comments-slide-leave-to {
        transform: translateX(100%);
    }
}

.issue-list {
    height: 100%;
    overflow: auto;
}

.getting-started {
    position: absolute;
    bottom: 0;
    width: 100%;
    z-index: 999;
    padding: 5px;
    text-align: center;
    .link-button {
        box-shadow: $box-shadow;
    }
    a {
        color: inherit;
        text-decoration: none;
        &:hover {
            color: inherit;
        }
    }
}

.new-issue {
    padding-top: 10px;

    textarea {
        resize: none;
    }
    .form-buttons {
        margin-bottom: 0;
        text-align: right;

        .el-form-item__content {
            line-height: initial;
        }
        .add-button,
        .cancel-button {
            margin-left: 10px;
        }
    }
}

.issue-box {
    position: relative;

    .el-card__body {
        padding: 15px;
    }

    // Box header
    .box-header {
        z-index: 2;
        margin-bottom: 15px;
    }
    // Nav button
    .nav-button {
        width: 40px;
        height: 40px;
        padding: 0;
        box-shadow: $box-shadow;
    }
    // Check button
    .check-button {
        background-color: #FFF;
        margin-bottom: 0;
        box-shadow: $box-shadow;
        text-transform: capitalize;
    }
    // Issue label
    .issue-label {
        width: 40px;
        height: 40px;
        padding: 0;
        box-shadow: $box-shadow;
    }
    // Description
    .description {
        margin-left: 5px;
        padding-left: 5px;
        padding-right: 5px;
        word-break: break-word;
        border-radius: 5px;
        &:hover {
            background-color: #f1f1f1;
        }
    }
    .attach-image {
        width: 140px;
        height: 90px;
        margin-top: 15px;
        margin-left: auto;
        overflow: hidden;
        border-radius: 5px;
        box-shadow: $box-shadow;
        position: relative;
        img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .attach-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, .2);
            opacity: 0;
            @include transition(.3s);

            .delete-button {
                margin-left: 5px;
            }
            .attach-button {
                font-size: 20px;
                color: $white;
                font-weight: 700;
                margin-left: 5px;
                @include transition(.3s);
                i {
                    font-weight: 700;
                }
                &:first-child {
                    margin-left: 0;
                }
                &:hover {
                    cursor: pointer;
                    color: $grey;
                }
            }
        }
        &:hover .attach-overlay {
            opacity: 1;
        }
        @media (max-width: 550px) {
            position: relative;
            width: 100%;
            height: unset;
            padding-top: 66%;
            img {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
            }
            .attach-overlay .attach-button {
                margin-left: 15px;
                &:first-child {
                    margin-left: 0;
                }
            }
        }
    }
    &.done {
        background-color: rgba(0, 137, 82, .4) !important;
        .more-button i {
            color: $white;
        }
    }
    &.review {
        background-color: rgba(230, 162, 60, .4) !important;
        .more-button i {
            color: $white;
        }
    }
    .more-button {
        cursor: pointer;
        margin-left: -3px;
        margin-top: 5px;
        i {
            font-size: 22px;
            color: $black;
            color: #cecfcf;
            transform: rotate(-90deg);
        }
    }
    .el-col-19:hover + .el-col-1 .more-button {
        display: none;
    }
    .update-button {
        margin-left: 10px;
    }
    // Edit issue
    .edit-issue {
        display: none;

        textarea {
            resize: none;
        }
        .edit-description {
            margin-bottom: 15px;
        }
    }
    &.editable {
        .edit-issue {
            display: block;
        }
        .description {
            display: none;
        }
        .more-button {
            display: none;
        }
    }
    .datetime {
        font-size: 8px;
        text-transform: uppercase;
        margin-left: 5px;
        color: rgba(0, 0, 0, .75);
    }
    .author-name {
        margin-left: 5px;
        font-size: 13px;
    }
}

// Issue details
.issue-details {
    position: absolute;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: #E8E8E8;
    z-index: 3;
    overflow: auto;
    .el-card {
        background-color: $white;
    }
}

////////////
// Dialog //
////////////
.upload-dialog {
    .el-dialog__body {
        text-align: center;
    }
    input[type="file"] {
        display: none;
    }
}
.creative-brief-dialog {
    .el-dialog__body {
        text-align: center;
    }
}

.final-upload {
    input[type="file"] {
        display: none;
    }
    .el-upload-dragger {
        width: 100%;
    }
}

.el-message-box__headerbtn {
    z-index: 10;
}

@media (max-width: 500px) {
    .el-dialog__wrapper .el-dialog {
        width: 90%;
        .el-dialog__body {
            text-align: center;
        }
    }
    .collaborator-dialog {
        .collaborators {
            flex-direction: unset;
            justify-content: center;
            .collaborator:first-child {
                margin-left: 0;
            }
        }
        .el-dialog__footer {
            padding-bottom: 120px;
        }
        .team-members {
            margin-top: 40px;
            text-align: left;
            padding: 20px;
            bottom: 0;
            position: absolute;
            width: 100%;
            height: 100px;
            .heading {
                line-height: 24px;
                font-size: 18px;
            }
        }
    }
}

.image-preview img {
    width: 100%;
}

////////////////////
// Mobile Version //
////////////////////

.mobile-header {
    padding: 0;
    box-shadow: 0 1px 12px $border-color;
    z-index: 2;

    .el-main {
        padding: 0;
    }

    .nav-dashboard {
        justify-content: center;

        .menu-icon {
            margin-right: 5px;
        }
    }
    .user {
        padding: 10px 15px;
        text-align: center;
    }

    // Revision buttons
    .revision-buttons {
        padding: 13px;
        display: flex;
        align-items: center;
        justify-content: center;

        .select-revision {
            width: 80px;
        }
    }

    // Canvas tools
    .canvas-tools {
        position: initial;
        background-color: $white;

        .zoom-slider {
            width: 100px;
        }
    }

    // Comments header
    .comments-header {
        text-align: center;
        padding: 16px 0;
        background-color: #e0dfdf;
        color: $white;
        font-weight: 400;
        font-size: 18px;

        span {
            margin-left: 10px;
        }
    }

    .upload-button {
        border-style: solid;
        border-width: 1px;
        width: unset;
        height: unset;
        i {
            font-size: 12px;
        }
    }
}

.mobile-footer {
    padding: 0;
    box-shadow: 0 -1px 12px $border-color;
    z-index: 2;

    .footer-main {
        padding: 10px;
        display: flex;
        flex-direction: row;
        overflow: auto;
    }
    .footer-right {
        width: 50px;
    }
    .proof-thumb {
        height: 80px;
        width: 100px;
        min-width: 100px;
        margin-bottom: 0;
        margin-right: 15px;
    }
    .toggle-comments {
        transform: rotate(-90deg);
        transform-origin: top left;
        position: relative;
        top: 100px;
        height: 50px;
        width: 100px;
        padding: 13px 6px;
        background-color: #F1F1F2;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;

        span {
            font-size: 12px;
            font-weight: 400;
            margin-right: 2px;
        }
    }
}

// Canvas
.canvas-overwrap {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, .1);
    display: flex;
    align-items: center;
    justify-content: center;

    .tip-edit {
        color: $white;
        background-color: $black;
        padding: 20px;
    }
}

// Mobile sidebar
.mobile-sidebar {
    width: 100%;
    box-shadow: none;
}
@media (max-width: 500px) {
    .el-message-box {
        width: 90%;
    }
    .zoom-slider .el-slider__runway {
        background-color: $grey;
    }
    .proof-thumb {
        .delete-button {
            opacity: 1;
            background-color: $red;
            color: $white;
            width: 25px;
            height: 25px;
            display: flex;
            justify-content: center;
            font-weight: 500;
        }
        .review-comment {
            .el-button {
                padding: 2px 5px;
            }
        }
    }
    .el-dialog__wrapper.upload-dialog {
        .el-upload,
        .el-upload-dragger {
            width: 100%;
        }
    }
}

</style>
