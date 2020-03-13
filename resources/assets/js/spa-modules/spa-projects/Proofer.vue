<template>
    <div v-loading.fullscreen.lock="fullscreenLoading" style="margin-top: 0px">
        <el-row type="flex">
            <el-col class="navbar-dashboard w3-sidebar w3-bar-block w3-card w3-animate-left">
                <div class="grid-content" @click="goBack()" style="cursor: pointer">
                    <!--<i class="el-icon-back" style="font-size: 30px; font-weight: 900; color: #5a5555af; margin-left: 25%; margin-top: 20%"></i>-->
                    <i class="el-icon-menu" style="font-size: 30px; font-weight: 900; margin-left: 10%;"></i>
                    <span style="margin-left: 10%">
                            <i class="el-icon-arrow-left"></i>
                            <span style="font-weight: 900px">DASHBOARD</span>
                        </span>
                </div>
            </el-col>
            <el-col class="w3-card" id="project-actions" style="height: 60px; width: 100%;">
                <el-row type="flex" justify="center">
                    <el-col v-if="project_type == 'design'" class="navtop-button">
                        <el-select @change="loadRevisionById()" v-model="value" placeholder="version"
                                   style="margin-top: 10px; margin-left: 5%;">
                            <el-option v-for="(item, key) in versions" :key=key :label="item.label"
                                       :value="item.id">
                            </el-option>
                        </el-select>
                        <!-- <el-radio-group size="mini" v-model="showSideBar" style="margin-left: 5px">
                            <el-radio-button :label="false">|</el-radio-button>
                            <el-radio-button :label="true">|</el-radio-button>
                        </el-radio-group> -->
                    </el-col>

                    <!--Freelancer Buttons-->
                    <el-col v-if="current_rol == 'freelancer' && project_type != 'website' && project_status == 'progress' && !uploadProofsStatus"
                            class="navtop-button">
                        <el-button
                                style="margin-top: 10px; background-color: #fa5555; color: #fff; font-weight: bold; border: 0px; padding: 9px 15px"
                                @click="openNewRevisionDialog()" type="primary">
                            <i class="el-icon-upload" style="font-size: 20px"></i>
                            <span>Upload New Proofs</span>
                        </el-button>
                    </el-col>
                    <el-col v-if="current_rol == 'freelancer' && project_status == 'progress' && uploadProofsStatus"
                            class="navtop-button">
                        <el-button
                                style="margin-top: 10px; background-color: #80B4A0; color: #fff; font-weight: bold; border: 0px; padding: 9px 15px"
                                type="primary">
                            <i class="fa fa-paper-plane-o" style="font-size: 20px"></i>
                            <span>Your new proofs has been Sent</span>
                        </el-button>
                    </el-col>
                    <el-col v-if="current_rol == 'freelancer' && project_type != 'website' && project_status == 'approved'"
                            class="navtop-button">
                        <el-button
                                style="margin-top: 10px; background-color: #fa5555; color: #fff; font-weight: bold; border: 0px; padding: 9px 15px"
                                @click="openFinalFilesDialog" type="primary">
                            <i class="el-icon-upload" style="font-size: 20px"></i>
                            <span>Upload Final Files</span>
                        </el-button>
                    </el-col>
                    <el-col v-if="current_rol == 'freelancer' && project_status == 'completed'" class="navtop-button">
                        <el-button
                                style="background-color: #80B4A0; color: #fff; font-weight: bold; border: 0px"
                                type="primary">
                            <i class="el-icon-success"></i> {{(project_type == 'design') ? 'DESIGN APPROVED' :
                            (project_type == 'video') ? 'VIDEO APPROVED' : 'WEBSITE APPROVED'}}
                        </el-button>
                    </el-col>

                    <!--Client Buttons-->
                    <el-col v-if="current_rol == 'client' && current_review_id == last_version_number && project_type != 'website' && project_status == 'revision'"
                            class="navtop-button">
                        <div style="margin-top: 10px">
                            <el-button :style="correctionButtonStyle" @click="showSubmitOptionsModal()"
                                       type="primary">
                                <i class="el-icon-edit"></i> {{ correctionButton }}
                            </el-button>
                        </div>
                    </el-col>
                    <el-col v-if="current_rol == 'client' && current_review_id == last_version_number && project_status == 'revision'"
                            class="navtop-button">
                        <div style="margin-top: 10px">
                            <el-button v-if="!approveStatus" :style="approveButtonStyle"
                                       @click="showApproveDesignModal()" type="primary">
                                <i class="el-icon-success"></i> {{ approveButton }}
                            </el-button>
                            <el-button v-else
                                       style="background-color: #80B4A0; color: #fff; font-weight: bold; border: 0px"
                                       type="primary">
                                <i class="el-icon-success"></i> {{(project_type == 'design') ? 'DESIGN APPROVED' :
                                (project_type == 'video') ? 'VIDEO APPROVED' : 'WEBSITE APPROVED'}}
                            </el-button>
                        </div>
                    </el-col>
                    <el-col v-if="current_rol == 'client' && project_status == 'approved'" class="navtop-button">
                        <div style="margin-top: 10px">
                            <el-button
                                    style="background-color: #80B4A0; color: #fff; font-weight: bold; border: 0px"
                                    type="primary">
                                <i class="el-icon-success"></i> {{(project_type == 'design') ? 'DESIGN APPROVED' :
                                (project_type == 'video') ? 'VIDEO APPROVED' : 'WEBSITE APPROVED'}}
                            </el-button>
                        </div>
                    </el-col>
                    <el-col v-if="current_rol == 'client' && project_status == 'completed'" class="navtop-button">
                        <div style="margin-top: 10px">
                            <el-button @click="openFinalFilesDialog()"
                                       style="background-color: #5FBFFF; color: #fff; font-weight: bold; border: 0px"
                                       type="primary">
                                <i class="el-icon-download"></i> DOWNLOAD FINAL FILES
                            </el-button>
                        </div>
                    </el-col>

                    <el-col v-if="current_rol == 'client' && project_status == 'completed'" class="navtop-button">
                        <div style="margin-top: 10px">
                            <el-button @click="moveToInProgress()"
                                       style="background-color: #FFC400;; color: #fff; font-weight: bold; border: 0px"
                                       type="primary">
                                <i class="el-icon-time"></i> MOVE TO IN PROGRESS
                            </el-button>
                        </div>
                    </el-col>
                </el-row>
            </el-col>

            <!--Collaborators-->
            <el-col class="w3-sidebar w3-bar-block w3-card" id="collaborators">
                <el-row type="flex" style="overflow-y: auto;" class="collabs">
                    <el-col v-if="current_rol == 'freelancer' || current_rol == 'client'" :xs="5" :md="5">
                        <div style="padding: 5px">
                            <el-tooltip content="Add new collaborator" placement="bottom" effect="light">
                                <div @click='openNewCollaboratorDialog()' class="img-circle special-img"
                                     style="font-size: 30px; display: flex; align-items: center; justify-content: center; width: 50px;height: 50px;padding: 2px;border-style: solid;border-color: #a5a3a3;border-width: 1px;">
                                    <i class="el-icon-plus"></i>
                                </div>
                            </el-tooltip>
                        </div>
                    </el-col>
                    <el-col v-if="collaborators.length" v-for="collaborator in collaborators" :key="collaborator.id"
                            :xs="5" :md="5">
                        <div style="padding: 5px">
                            <el-tooltip :content="(collaborator.id == current_user_id) ? 'Me' : collaborator.name"
                                        placement="bottom" effect="light">
                                <img :src="collaborator.photo_url"
                                     class="img-circle special-img"
                                     style="width: 50px;height: 50px;padding: 2px;border-style: solid;border-color: #a5a3a3;border-width: 1px;">
                            </el-tooltip>
                        </div>
                    </el-col>
                </el-row>
            </el-col>
        </el-row>
        <el-row type="flex">
            <!--Left Side-->
            <el-col id="leftSidebar" class="w3-sidebar w3-bar-block w3-card w3-animate-left">
                <button class="toggle-button"><i class="el-icon-picture"></i></button>
                <div ref="thumbTovideo" class="thumbTovideo">
                    <button ref="playThumbTovideoBtn" class="playThumbTovideoBtn" @click="thumbTovideo()"><img
                            src="/img/play-icon.png"></button>
                    <button ref="pauseThumbTovideoBtn" class="pauseThumbTovideoBtn" @click="thumbTovideo()"><img
                            src="/img/pause-icon.png"></button>
                </div>
                <div id="leftSidebarSub" style="height: 92%; padding: 10px; overflow: auto;">
                    <template v-if="project_type == 'website'">
                        <el-card v-if="project_status == 'approved' || project_status == 'completed'"
                                 @click.native="openFinalFilesDialog()"
                                 class="upload-container" style="padding: 20px !important">
                            <i v-if="current_rol == 'freelancer'" class="el-icon-upload" style="font-size: 80px"></i>
                            <i v-else class="el-icon-download" style="font-size: 80px"></i>
                            <p v-if="current_rol == 'freelancer'" style="font-size: 16px; font-weight: bold">
                                Upload Final Files</p>
                            <p v-else style="font-size: 16px; font-weight: bold">Download Final Files</p>
                        </el-card>
                        <el-card v-else-if="current_rol == 'freelancer'
                            || current_rol == 'client' && project_status == 'revision'"
                                 class="upload-container">
                            <el-row type="flex" style="height: 180px">
                                <el-col class="plugin-col" :class="{'not-installed' : !pluginInstalled, 'installed' : pluginInstalled}" :md="12"
                                        @click.native="!pluginInstalled ? installProofloPlugin() : takeScreenshots()">
                                    <p v-if="!pluginInstalled" style="font-size: 13px; font-weight: bold">INSTALL PROOFLO PLUGIN</p>
                                    <p v-else style="font-size: 13px; font-weight: bold">
                                        CAPTURE IMAGE
                                        <br>
                                        <span style="color: #80B4A0">PLUGIN</span>
                                    </p>
                                    <div class="plugin-logo" :class="{'logo-white' : !pluginInstalled, 'logo-gray' : pluginInstalled}">
                                        <img src="/img/p.png" alt="p">
                                    </div>
                                    <div class="files-rollover">
                                        <div class="plus-circle">
                                            <i v-if="pluginInstalled" class="el-icon-plus"></i>
                                            <i v-else class="el-icon-download"></i>
                                        </div>
                                    </div>
                                </el-col>
                                <el-col class="computer-col" :md="12" @click.native="openUploadSlideDialog()">
                                    <p style="font-size: 13px; font-weight: bold">
                                        UPLOAD IMAGE
                                        <br>
                                        <span style="color: #80B4A0">COMPUTER</span>
                                    </p>
                                    <i class="el-icon-upload" style="font-size: 50px"></i>
                                    <div class="files-rollover">
                                        <div class="plus-circle">
                                            <i class="el-icon-plus"></i>
                                        </div>
                                    </div>
                                </el-col>
                            </el-row>
                        </el-card>
                    </template>
                    <template v-else>
                        <el-card v-if="project_status == 'approved' || project_status == 'completed'"
                                 @click.native="openFinalFilesDialog()"
                                 class="upload-container" style="padding: 20px !important">
                            <i v-if="current_rol == 'freelancer'" class="el-icon-upload" style="font-size: 80px"></i>
                            <i v-else class="el-icon-download" style="font-size: 80px"></i>
                            <p v-if="current_rol == 'freelancer'" style="font-size: 16px; font-weight: bold">
                                Upload Final Files
                            </p>
                            <p v-else style="font-size: 16px; font-weight: bold">Download Final Files</p>
                        </el-card>
                        <el-card v-else-if="current_rol == 'freelancer'
                            || current_rol == 'client' && project_type != 'design' && project_status == 'revision'"
                                 @click.native="openUploadSlideDialog()"
                                 class="upload-container" style="padding: 20px !important">
                            <i class="el-icon-upload" style="font-size: 80px"></i>
                            <p style="font-size: 16px; font-weight: bold">Click to add and upload image</p>
                        </el-card>
                    </template>
                    <el-card :id="'proof_'+key" v-for="(proof, key) in proofs" :key=key
                             :body-style="{ padding: '0px'}" @click.native="loadThumb(key); loadThumbFromVideo(key);"
                             style="position: relative; margin-bottom: 5px"
                             class="proof-slide slide-deck"
                             :class="{'regular-thumb' : !proof.active_thumb, 'active-thumb' : proof.active_thumb}">
                        <el-col :xs="4" :sm="2" :md="2" :lg="3"
                                :class="{'regular-thumb-number' : !proof.active_thumb, 'active-thumb-number' : proof.active_thumb}"
                                style="position: absolute; z-index: 999;">
                            <span style="font-size: 16px; color: #fff">
                                <b>{{key+1}}</b>
                            </span>
                        </el-col>
                        <div v-if="project_type == 'website' && proof.status == 'approved'" class="proof-status">
                            <i class="el-icon-check"></i>
                        </div>
                        <div class="rollover">
                            <el-col v-if="
                                current_rol == 'client' || 
                                (proof.user_id == current_user_id && current_rol == 'freelancer')
                                "
                                :xs="20" :sm="22" :lg="21">
                                <div class="slide-delete-btn">
                                    <el-dropdown @command="handleDropdownCommand" trigger="click"
                                                 class="slide-dropdown">
                                        <i class="el-icon-more icon-center"
                                           style="font-size: 24px; color: #faf9f9"></i>
                                        <el-dropdown-menu slot="dropdown">
                                            <el-dropdown-item :command="{action: 'delete', proof_id: proof.id}">
                                                Delete File
                                            </el-dropdown-item>
                                        </el-dropdown-menu>
                                    </el-dropdown>
                                </div>
                            </el-col>
                        </div>
                        <div v-if="proof.capture_time != 'null'" class='captured-image'><span class='time-value'>{{proof.capture_time}}</span>
                        </div>
                        <div class="slide-img-thumb"
                             :style="{'background-image' : 'url('+ proof.picture_url +')'}"></div>
                        <!--Unread Comments-->
                        <div style="background: #fff; width: 100%; height: 40px; position: relative">
                            <el-tag v-if="getUnreadComments(proof) > 0" size="small"
                                    style="background-color: #f07f7f; border-radius: 30px; color: #fff; position: absolute; top: 7px; right: 5px"
                                    title="Issues">
                                <i class="fa fa-comment icon-center"
                                   style="font-size: 30px; color: #f07f7f; position: relative; top: -15px; left: 3px"></i>
                                <div style="position: relative; top: -45px; right: -10px; color: #fff; font-weight: bold">
                                    {{ getUnreadComments(proof) }}
                                </div>
                            </el-tag>
                        </div>
                    </el-card>
                </div>

                 <!--Team Members-->
                 <div class="web-progress" style="overflow-y: auto">
                     <el-row type="flex">
                         <el-col v-if="teamMembers.length" v-for="member in teamMembers" :key="member.id"
                                 :xs="5" :md="5" style="margin-right: 15px">
                             <div style="padding: 5px">
                                 <el-tooltip :content="member.name" placement="bottom" effect="light">
                                     <img :src="member.photo_url"
                                          class="img-circle special-img"
                                          style="width: 50px;height: 50px;padding: 2px;border-style: solid;border-color: #a5a3a3;border-width: 1px;">
                                 </el-tooltip>
                             </div>
                         </el-col>
                     </el-row>
                 </div>
            </el-col>
            <!--Main Content-->
            <div id="main">
                <div ref="buttonsBarPanel" id="buttons_bar" class="buttons-bar">
                    <div class="w3-container">
                        <el-row type="flex" justify="center">
                            <!--<el-col :sm="9" :md="9" :offset="5">-->
                            <!--<div style="margin-left: 20px">-->
                            <!--</div>-->
                            <!--</el-col>-->

                            <el-col id="boxtool-info"
                                    v-if="(current_rol == 'client' || current_rol == 'collaborator') && current_review_id == last_version_number && project_status == 'revision'
                                        || current_rol == 'freelancer' && current_review_id == last_version_number && (project_status == 'progress' || project_status == 'revision')"
                                    class="tool-button" style="text-align: center; margin-right: 20px">
                                <button :class="{'edition-btn-active' : boxtool_active, 'edition-btn' : !boxtool_active}"
                                        @click="activate_boxtool()"
                                        style="float: right"
                                        :disabled="showIssueDialog">
                                    <i class="el-icon-news icon-center" style="font-size: 20px"></i>
                                </button>
                            </el-col>

                            <el-col id="pen-info"
                                    v-if="(current_rol == 'client' || current_rol == 'collaborator') && current_review_id == last_version_number && project_status == 'revision'
                                        || current_rol == 'freelancer' && current_review_id == last_version_number && (project_status == 'progress' || project_status == 'revision')"
                                    class="tool-button" style="text-align: center; margin-right: 20px">
                                <button :class="{'edition-btn-active' : pen_active, 'edition-btn' : !pen_active}"
                                        @click="activate_pen()"
                                        style="float: right"
                                        :disabled="showIssueDialog">
                                    <i class="el-icon-edit icon-center" style="font-size: 20px"></i>
                                </button>
                                <p id="info" v-if="penInfoVisible">
                                    SELECT TO ADD CORECTION(S)
                                </p>
                            </el-col>
                            <el-col class="tool-button">
                                <button :class="{'edition-btn-active' : zoom_out_active, 'edition-btn' : !zoom_out_active}"
                                        @mousedown="activate_zoom_in()"
                                        style="float: right" @mouseup="deactivate_zoom_in()">
                                    <i class="el-icon-zoom-out icon-center" style="font-size: 20px"></i>
                                </button>
                            </el-col>
                            <el-col class="zoomtool-bar">
                                <el-slider v-model="zoom_level" :min="0.25" :max="4" :step="0.1" :height="'2px'"
                                           :show-tooltip="false" style="margin-top: 2px !important">
                                </el-slider>
                            </el-col>
                            <el-col class="tool-button">
                                <button :class="{'edition-btn-active' : zoom_in_active, 'edition-btn' : !zoom_in_active}"
                                        @mousedown="activate_zoom_out()"
                                        style="float: left" @mouseup="deactivate_zoom_out()">
                                    <i class="el-icon-zoom-in icon-center" style="font-size: 20px"></i>
                                </button>
                            </el-col>

                            <el-col class="tool-button extra">
                                <button class="edition-btn" @click="fitToScreen('width')">
                                    <i class="fa fa-arrows-h icon-center"></i>
                                </button>
                            </el-col>

                            <el-col class="tool-button extra">
                                <button class="edition-btn" @click="fitToScreen()">
                                    <i class="fa fa-arrows-v icon-center"></i>
                                </button>
                            </el-col>

                            <el-col class="tool-button extra">
                                <button class="edition-btn" @click="rotateCanvas()">
                                    <i class="fa fa-refresh icon-center"></i>
                                </button>
                            </el-col>

                        </el-row>
                    </div>
                </div>
                <div id="stage-parent" style="background-color: rgb(224, 223, 223)">
                    <button id="clickFromExt" @click="loadNewCaptureImage()"></button>
                    <div ref="isNotVideo" id="stage"></div>
                    <div ref="divVideoTagRef" class="divVideoTagRef"></div>
                    <div ref="proofloTakeScreenshotFromVideoRef" id="ProofloTakeScreenshotFromVideo">
                        <button class="btn-gray" id="playVideoImage" @click="switchPlayResumevideoImage()"
                                ref="playVideoImage"><img src="/img/play-icon.png" alt=""/></button>
                        <button class="btn-gray" id="pauseVideoImage" @click="switchPlayResumevideoImage()"
                                ref="pauseVideoImage"><img src="/img/pause-icon.png" alt=""/></button>
                    </div>
                    <input type="hidden" id="HdnProofloCapturedImageData"/>
                    <input ref="videoUrlFromDatabaseRef" type="hidden" id="videoUrlFromDatabaseRef" value=""/>
                </div>

                <!--Slide Pagination-->
                <div class="slide-pagination">
                    <el-row type="flex" justify="center" style="">
                        <el-col :sm="6" style="text-align: center">
                            <div class="el-pagination is-background">
                                <button type="button" :disabled="(slide_page_number > 1) ? false : true"
                                        @click="loadThumb(slide_page_number - 2)" class="btn-prev">
                                    <i class="el-icon el-icon-arrow-left"></i>
                                </button>
                                <ul class="el-pager">
                                    <li class="">
                                        <el-input-number v-model="slide_page_number" @change="handlePaginationChange"
                                                         class="slide-number" :min="1" :max="proofs.length"
                                                         :controls="false" size='small'></el-input-number>
                                    </li>
                                    <li class="disabled" style="color: #606266 !important">
                                        <p>of</p>
                                    </li>
                                    <li class="number active" style="background-color: #545c64 !important"
                                        @click="loadThumb(proofs.length - 1)">{{ proofs.length }}
                                    </li>
                                </ul>
                                <button type="button" :disabled="(slide_page_number == proofs.length) ? true : false"
                                        @click="loadThumb(slide_page_number)" class="btn-next">
                                    <i class="el-icon el-icon-arrow-right"></i>
                                </button>
                            </div>
                        </el-col>
                    </el-row>
                </div>
            </div>

            <!--Right Side-->
            <el-col id="rightSidebar" class="w3-sidebar w3-bar-block w3-card">
                <button class="toggle-button"><i class="fa fa-bars"></i></button>
                <el-row>
                    <!-- <el-col :xs="24"> -->
                    <!-- <div class="grid-content side-menu-right"> -->
                    <!-- <transition-group name="el-fade-in"> -->
                    <div :key="1" class="issue-listing listing-with-progress-bar">
                        <el-row>
                            <el-col>

                                <div v-show="showIssueDialog" style="cursor: pointer; border: 3px solid #f07f7f;"
                                     class="new-issue">
                                    <i @click="cancelIssue()" class="el-icon-circle-close-outline"
                                       style="float: right;margin-right: 10px;font-size: 21px;margin-top: 5px;margin-bottom: 10px;color: #999; position:absolute;top: 3px;right: 5px; z-index: 987654;"></i>
                                    <el-row style="padding:30px 10px 15px; margin-bottom: 0px;">
                                        <el-col :xs="3" :md="3" style=" position: relative;" class="user-avatar">
                                            <div style="padding: 0px;position: relative;float: left;width: 45px;height: 45px;">
                                                <img :src="user.photo_url" class="img-circle special-img"
                                                     style="width: 100%; height: auto;padding: 0;border-style: solid;border-color: #a5a3a3;border-width: 0px;">
                                            </div>
                                        </el-col>
                                        <el-col :xs="20" :md="20" style="margin-left:10px;" class="comment-input">
                                            <el-input
                                                    type="textarea" class="text-border"
                                                    ref="autofocus_issue_create"
                                                    v-model="new_issue.description"
                                                    :autosize="{ minRows: 2, maxRows: 4}"
                                                    @keydown.native.enter.prevent="addIssue"
                                                    @input="addIssueIcons = '/img/send-red.png'"
                                                    placeholder="Type comment here"
                                            >
                                            </el-input>
                                            <el-button @click="addIssue()"
                                                       style="border: 0px;background: none;float: right;top: 8px;right: -3px; padding:0;">
                                                <img :src="addIssueIcons" style="width:23px;margin-top:10px;">
                                            </el-button>

                                            <el-upload
                                                    :data="imageFormData"
                                                    :on-change="issueAttachmentAdded"
                                                    :on-remove="issueAttachmentRemoved"
                                                    :on-success="issueImageUploaded"
                                                    :auto-upload="false"
                                                    :file-list="fileList"
                                                    :limit="1"
                                                    action="/api/files/upload"
                                                    ref="issue_attachment"
                                                    name="photos"
                                                    style="float:right; text-align:right; margin-bottom:0px;"
                                            >
                                                <el-button
                                                        style="border: 0px;background: none;float: right; padding:0;">
                                                    <i class="fa fa-paperclip"
                                                       style="color: #cecfcf; font-size: 22px;margin-top:10px; margin-right:10px;"></i>
                                                </el-button>
                                            </el-upload>
                                            <span v-if="errors.issue_description"
                                                  style="float: left; color: rgb(211, 12, 12); font-size: 13px;">{{errors_msg.issue_description}}</span>


                                            <!-- <el-button  style="border: 0px;background: none;float: right;top: 10px;right: 30px;padding:0;">
                                                <i class="fa fa-paperclip" style="color: #999; font-size: 22px;margin-top:10px; margin-right:10px;"></i>
                                            </el-button> -->

                                        </el-col>
                                        <el-col :xs="3" :md="3">

                                        </el-col>
                                    </el-row>
                                </div>

                                <div class="issue-container" v-for="(_current_issue, key ) in current_proof.issues"
                                     :key=key
                                     :class="{'current_issue_active' : _current_issue.active_issue, 'current_issue_normal' : !_current_issue.active_issue, 'current_issue_solved' : _current_issue.status == 'solved'}">

                                    <div v-show="_current_issue.show_issues_del_conf" class="delete-confirm  issue-del">
                                        <div class="el-delete__text" style=" font-weight: 600;">Are you sure?</div>
                                        <div class="el-delete__text-2">You won't be able to revert this!</div>
                                        <el-row>
                                            <el-button @click="deleteIssue(_current_issue.id);" type="primary"
                                                       class="delete-red">Yes, delete
                                            </el-button>
                                            <el-button
                                                    @click="_current_issue.show_issues = !_current_issue.show_issues; _current_issue.show_issues_del_conf = !_current_issue.show_issues_del_conf">
                                                Cancel
                                            </el-button>
                                        </el-row>
                                    </div>

                                    <div v-show="_current_issue.show_issues_del_img_conf"
                                         class="delete-confirm  issue-del">
                                        <div class="el-delete__text" style=" font-weight: 600;">Are you sure?</div>
                                        <div class="el-delete__text-2">You won't be able to revert this!</div>
                                        <el-row>
                                            <el-button
                                                    @click="deleteIssueImage(_current_issue.id, _current_issue.project_files_id);"
                                                    type="primary" class="delete-red">Yes, delete
                                            </el-button>
                                            <el-button
                                                    @click="_current_issue.show_issues = !_current_issue.show_issues; _current_issue.show_issues_del_img_conf = !_current_issue.show_issues_del_img_conf">
                                                Cancel
                                            </el-button>
                                        </el-row>
                                    </div>

                                    <div v-if="!_current_issue.project_files_id"
                                         class="attachment-popup new-issue-attach"
                                         v-show="_current_issue.show_issues_attachment_uploader">
                                        <el-upload
                                                :data="imageFormData"
                                                :on-success="issueImageUploaded"
                                                :limit="1"
                                                :auto-upload="false"
                                                :file-list="fileList"
                                                :ref="'save_issue_image' + _current_issue.id"
                                                action="/api/files/upload"
                                                name="photos"
                                                drag>
                                            <i class="el-icon-upload"></i>
                                            <div class="el-upload__text">Click or drag here to uploads files</div>
                                        </el-upload>
                                        <el-row>
                                            <el-button
                                                    @click="_current_issue.show_issues = !_current_issue.show_issues; _current_issue.show_issues_attachment_uploader = !_current_issue.show_issues_attachment_uploader">
                                                Cancel
                                            </el-button>
                                            <el-button @click="saveIssueImage(_current_issue.id, key)" type="primary">
                                                Save
                                            </el-button>
                                        </el-row>
                                    </div>

                                    <div v-show="_current_issue.show_issues">
                                        <div v-if="_current_issue.status == 'solved'" class="issue-resolved-overlay"
                                             @click="IssueDetails(_current_issue.group)">
                                        </div>
                                        <el-checkbox class="resolved-checkbox" v-if="_current_issue.status == 'solved'"
                                                     @change="setIssueSolved(_current_issue, 'pending')"
                                                     :disabled="_current_issue.user_id != user.id"
                                                     checked></el-checkbox>
                                        <el-col :xs="3" :md="3">
                                            <el-button class="btn-issue-details"
                                                       @click="IssueDetails(_current_issue.id)" type="default">
                                                <i class="el-icon-arrow-right"></i>
                                                <img src="/img/ViewThread.png" alt="">
                                            </el-button>
                                        </el-col>
                                        <el-col :xs="18" :md="18">
                                            <div v-if="_current_issue.status == 'solved'"
                                                 style="color: #009688;display: block;float: none;font-weight: bold;text-align: center; ">
                                                <el-checkbox :label="(project_type == 'design') ? 'DONE' : 'RESOLVED'"
                                                             border checked
                                                             style="background:#eee;  border:none; height:45px; width:75%; padding:12px 0px;"></el-checkbox>
                                            </div>
                                            <div v-else>
                                                <div class="unresolved-box" v-if="_current_issue.user_id == user.id"
                                                     style="color: #da4545;; text-align: center; font-weight: bold; margin: 0px auto 0;background:#eee;  border:none; height:45px; width:75%; padding:12px 0px; border-radius:4px;">
                                                    <el-checkbox @change="setIssueSolved(_current_issue, 'solved')"
                                                                 label="" :disabled="_current_issue.user_id != user.id"
                                                                 style=""></el-checkbox>
                                                    <span v-if="project_type == 'design'"
                                                          style="color:#606266;font-weight:normal;">TODO</span>
                                                    <span v-else
                                                          style="color:#606266;font-weight:normal;">UNRESOLVED</span>
                                                </div>

                                                <div class="unresolved-box" v-else
                                                     style="color: #da4545;; text-align: center; font-weight: bold; padding: 5px;background:#eee;  border:none; height:45px; width:75%; padding:12px 0px;border-radius:4px;">
                                                    <el-checkbox label="" :disabled="_current_issue.user_id != user.id"
                                                                 style=""></el-checkbox>
                                                    <span v-if="project_type == 'design'"
                                                          style="color:#606266;font-weight:normal;">TODO</span>
                                                    <span v-else
                                                          style="color:#606266;font-weight:normal;">UNRESOLVED</span>
                                                </div>
                                            </div>
                                        </el-col>
                                        <el-col :xs="3" :md="3">
                                            <div style="color: #626364; padding-top: 0;float: right;">
                                                <div class="tag" v-bind:class="{
                                            'tag_solved' : _current_issue.status == 'solved',
                                            'tag_freelancer' : _current_issue.owner_type == 'freelancer',
                                            'tag_client' : _current_issue.owner_type == 'client'}"
                                                     @click="gotoIssue(_current_issue.group)">{{_current_issue.label}}
                                                </div>
                                            </div>
                                        </el-col>
                                        <div style="cursor: pointer; clear:both">
                                            <el-row style="padding-top: 3%; margin-bottom: 0px; clear:both;">
                                                <el-col :xs="3" :md="3" style=" position: relative;">
                                                    <div style="padding: 0px;position: relative;float: left;width: 45px;height: 45px;">
                                                        <div v-if="_current_issue.unread_comments && _current_issue.unread_comments.length > 0"
                                                             class="comment-counter">
                                                            {{_current_issue.unread_comments.length}}
                                                        </div>
                                                        <el-tooltip
                                                                :content="(_current_issue.user.id == current_user_id) ? 'Me' : _current_issue.user.name"
                                                                placement="bottom" effect="light">
                                                            <img v-if="_current_issue.user"
                                                                 :src="_current_issue.user.photo_url"
                                                                 class="img-circle special-img"
                                                                 style="width: 100%; height: auto;padding: 0px;border-style: solid;border-color: #a5a3a3;border-width: 0px;">
                                                        </el-tooltip>
                                                    </div>
                                                </el-col>
                                                <el-col :xs="18" :md="18" class="comment-space">
                                                    <div>
                                                        <div
                                                                @click="_current_issue.show_issues_controls = true"
                                                                @blur="syncIssueDesc(_current_issue.id, false)"
                                                                @input="updateIssue(_current_issue.id, $event)"
                                                                style="color: #626364;border: 1px solid transparent; padding-top: 0px;padding-left:0px;font-weight;500;border:1px solid transparent; min-height:30px;margin-left:10px; word-wrap:break-word;"
                                                                :contenteditable="current_rol == 'client' || _current_issue.user_id == user.id"
                                                                :ref="'issues_content' + _current_issue.id"
                                                                :id="'current_issue' + _current_issue.id"
                                                        >
                                                            {{_current_issue.description}}
                                                        </div>

                                                        <div v-if="_current_issue.status != 'solved' && (_current_issue.user_id == user.id || current_rol == 'client')"
                                                             class="dropdown issue-controls">
                                                            <i class="el-icon-more dropdown-toggle"
                                                               data-toggle="dropdown"
                                                               style="color: #cecfcf;font-size: 22px;position: absolute;transform: rotate(-90deg);right: 0px;top:20px;"></i>
                                                            <ul class="dropdown-menu issue-controls-menu">
                                                                <li>
                                                                    <a @click="inlineIssueEdit(_current_issue.id, 'current_issue')">Edit</a>
                                                                </li>
                                                                <li>
                                                                    <a @click="_current_issue.show_issues = !_current_issue.show_issues; _current_issue.show_issues_del_conf = !_current_issue.show_issues_del_conf">Delete</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <div v-show="_current_issue.show_issues_controls">
                                                            <i v-show="current_rol == 'client' || _current_issue.user_id == user.id"
                                                               @click="_current_issue.show_issues_controls = false"
                                                               class="el-icon-circle-check"
                                                               style="color: rgb(250, 115, 115); font-size: 25px; float:right; margin-left:10px;cursor: pointer;"></i>
                                                        </div>
                                                        <div @click="_current_issue.show_issues_attachment = !_current_issue.show_issues_attachment"
                                                             v-if="_current_issue.project_files_id">
                                                            <i class="fa fa-paperclip"
                                                               style="color: rgb(250, 115, 115); font-size: 22px; float:right;"></i>
                                                        </div>
                                                        <div v-show="_current_issue.show_issues_controls" v-else>
                                                            <i v-if="_current_issue.status != 'solved' && _current_issue.user_id == user.id"
                                                               @click="_current_issue.show_issues = !_current_issue.show_issues; _current_issue.show_issues_attachment_uploader = !_current_issue.show_issues_attachment_uploader"
                                                               class="fa fa-paperclip"
                                                               style="color: #cecfcf; font-size: 22px; float:right;"
                                                               id="__ADD__"></i>
                                                        </div>
                                                        <div class="new-issue-img"
                                                             style="position:relative; float:left; width:100%;"
                                                             v-if="_current_issue.project_files_id"
                                                             v-show="_current_issue.show_issues_attachment">
                                                            <img class="issue_img_thumb"
                                                                 :src="'/storage/' + _current_issue.project_files.thumb_path">
                                                            <img src="/img/rubbish-bin.png"
                                                                 v-if="_current_issue.user_id == user.id"
                                                                 @click="_current_issue.show_issues = false; _current_issue.show_issues_del_img_conf = true"
                                                                 class=""
                                                                 style="position: absolute;bottom: 10px; right: 10px; cursor: pointer; width: 33px;   padding: 4px; border-radius: 2px; z-index:987654;">
                                                            <a class="download-link"
                                                               :href="'/storage/' + _current_issue.project_files.thumb_path"
                                                               download>
                                                                <i class="el-icon-download"></i>
                                                            </a>
                                                        </div>
                                                    </div>

                                                </el-col>
                                                <el-col :xs="3" :md="3">
                                                    <!-- <div style="color: #626364; padding-top: 10px">
                                                        <div v-bind:class="{'tag' : _current_issue.status != 'solved', 'tag_solved' : _current_issue.status == 'solved'}" style="border-radius: 50px; width: 30px; height: 30px; padding-top: 6px; text-align: center;font-size:13px">{{_current_issue.label}}</div>
                                                    </div> -->
                                                </el-col>
                                            </el-row>
                                            <!-- <el-row style="margin-bottom: 0px">
                                                <el-col :xs="12" :xl="12">
                                                    <div v-if="_current_issue.comments" style="margin-left: 15px; margin-top: 5px">
                                                        <b>{{_current_issue.comments.length}}</b> comments
                                                    </div>
                                                </el-col>
                                            </el-row> -->
                                        </div>
                                        <el-row type="flex" style="margin-bottom: 0px; margin-top: 0px">
                                            <!--   <el-col v-if="_current_issue.project_files_id" :xs="8" :xl="8">
                                                  <el-button @click="showIssueImage(_current_issue.project_files.path, _current_issue.project_files.thumb_path, _current_issue.project_files.id)"
                                                      type="text" class="issue-button-details" icon="el-icon-picture-outline" style="float: left;margin-left: 15px;"
                                                      size="mini">
                                                      <span style="font-size: 14px; ">Image attached</span>
                                                  </el-button>
                                              </el-col>
                                              <el-col v-else :xs="8" :xl="8">
                                                  <el-button v-if="current_rol == 'client' && _current_issue.status != 'solved' && _current_issue.user_id == user.id" @click="editIssue(_current_issue.id)"
                                                      type="text" class="issue-button-details" style="float: left;margin-left: 15px"
                                                      size="mini">
                                                      <span class="fa fa-paperclip" style="font-size: 20px;"></span>
                                                      <span style="font-size: 14px;">Add image</span>
                                                  </el-button>
                                              </el-col> -->
                                            <!-- <el-col :xs="3" :xl="3" :offset="7">
                                                <el-button v-if="current_rol == 'client' && _current_issue.user_id == user.id && _current_issue.status != 'solved'" @click="setIssueSolved(_current_issue.id, 'solved')"
                                                    class="issue-button-solved" icon="el-icon-check" size="mini">
                                                </el-button>
                                            </el-col> -->
                                            <!-- <el-col v-if="" :xs="3" :xl="3">
                                                <el-button v-if="current_rol == 'client' && _current_issue.status != 'solved' && _current_issue.user_id == user.id" @click="editIssue(_current_issue.id)"
                                                    class="issue-button-edit" icon="el-icon-edit" size="mini">
                                                </el-button>
                                            </el-col> -->
                                            <!-- <el-col :xs="3" :xl="3">
                                                <el-button @click="IssueDetails(_current_issue.group)" class="issue-button-details" icon="el-icon-search" size="mini">
                                                </el-button>
                                            </el-col> -->
                                        </el-row>
                                    </div>
                                </div>
                            </el-col>
                        </el-row>
                    </div>
                    <div class="web-progress">
                        <el-row>
                            <el-col>
                                <el-row type="flex" align="middle" style="flex-wrap:wrap; height: 60px">
                                    <el-col :sm="24" style="margin-top: 10px">
                                        <el-progress :text-inside="true"
                                                     :stroke-width="25"
                                                     :percentage="percentage"
                                                     :color="(percentage == 100) ? '#80B4A0' : '#fa5555'"
                                                     style="width: 90%; margin: auto;"
                                        >
                                        </el-progress>
                                    </el-col>
                                    <el-col :sm="24">
                                        <div style="text-align: center;">
                                            <p style="font-size: 13px;"><b>{{solved_issues}} OF {{total_issues}}</b>
                                                ISSUES COMPLETED</p>
                                        </div>
                                    </el-col>
                                </el-row>
                            </el-col>
                        </el-row>
                    </div>
                    <!--  </transition-group> -->
                    <transition-group name="issuesDetailsListing">
                        <div :key="2" v-show="showIssueDetails" class="issue-thread">
                            <!-- <el-row style="padding: 5px; margin-left: 15px">
                                <el-col>
                                    <div @click="issuesList()" style="float: left">
                                        <i class="el-icon-back icon-center" style="font-size: 20px"></i>
                                    </div>
                                </el-col>
                            </el-row> -->
                            <el-row type="flex" justify="end" style="margin-bottom: 0px">
                                <el-col>
                                    <div v-show="issue_datils.show_issue_del_conf" class="delete-confirm">
                                        <div class="el-delete__text" style=" font-weight: 600;">Are you sure?</div>
                                        <div class="el-delete__text-2">You won't be able to revert this!</div>
                                        <el-row>
                                            <el-button @click="deleteIssue(issue_datils.id);" type="primary"
                                                       class="delete-red">Yes, delete
                                            </el-button>
                                            <el-button
                                                    @click="issue_datils.show_issue = !issue_datils.show_issue; issue_datils.show_issue_del_conf = !issue_datils.show_issue_del_conf">
                                                Cancel
                                            </el-button>
                                        </el-row>
                                    </div>

                                    <div v-show="issue_datils.show_issue_del_img_conf"
                                         class="delete-confirm  issue-del">
                                        <div class="el-delete__text" style=" font-weight: 600;">Are you sure?</div>
                                        <div class="el-delete__text-2">You won't be able to revert this!</div>
                                        <el-row>
                                            <el-button
                                                    @click="deleteIssueImage(issue_datils.id, issue_datils.project_files_id);"
                                                    type="primary" class="delete-red">Yes, delete
                                            </el-button>
                                            <el-button
                                                    @click="issue_datils.show_issues = !issue_datils.show_issues; issue_datils.show_issue_del_img_conf = !issue_datils.show_issue_del_img_conf">
                                                Cancel
                                            </el-button>
                                        </el-row>
                                    </div>

                                    <div style="padding-bottom:10px !important;" v-if="!issue_datils.project_files_id"
                                         class="attachment-popup" v-show="issue_datils.show_issue_attachment_uploader">
                                        <el-upload
                                                :data="imageFormData"
                                                :on-success="issueImageUploaded"
                                                :limit="1"
                                                :auto-upload="false"
                                                :file-list="fileList"
                                                :ref="'save_issue_img' + issue_datils.id"
                                                action="/api/files/upload"
                                                name="photos"
                                                drag>
                                            <i class="el-icon-upload"></i>
                                            <div class="el-upload__text">Click or drag here to uploads files</div>
                                        </el-upload>
                                        <el-row>
                                            <el-button
                                                    @click="issue_datils.show_issue = !issue_datils.show_issue; issue_datils.show_issue_attachment_uploader = !issue_datils.show_issue_attachment_uploader">
                                                Cancel
                                            </el-button>
                                            <el-button @click="saveIssueImage(issue_datils.id, false)" type="primary">
                                                Save
                                            </el-button>
                                        </el-row>
                                    </div>

                                    <div v-show="issue_datils.show_issue" style=""
                                         :class="{'current_issue_active' : issue_datils.active_issue, 'current_issue_normal' : !issue_datils.active_issue, 'current_issue_solved' : issue_datils.status == 'solved'}">
                                        <el-col :md="3" :xs="3">
                                            <el-button class="btn-backtolist" @click="issuesList()" type="danger">
                                                <i class="el-icon-arrow-right"></i>
                                            </el-button>
                                        </el-col>
                                        <el-col :md="18" :xs="18">
                                            <div v-if="issue_datils.status == 'solved'"
                                                 style="color: #009688;display: block;float: none;font-weight: bold;text-align: center;width: 100%; ">
                                                <el-checkbox @change="setIssueSolved(issue_datils, 'pending')"
                                                             :label="(project_type == 'design') ? 'DONE' : 'RESOLVED'"
                                                             border checked
                                                             style="background:#eee;  border:none; height:45px; width:75%; padding:12px 0px;"></el-checkbox>
                                            </div>
                                            <div v-else
                                                 style="color: #da4545;; text-align: center; font-weight: bold; padding: 0px">
                                                <div class="unresolved-box" v-if="issue_datils.user_id == user.id"
                                                     style="color: #da4545;; text-align: center; font-weight: bold; padding:0;background:#eee; height:45px; width:75%; padding:12px 0px; border:none;border-radius:4px;">
                                                    <el-checkbox @change="setIssueSolved(issue_datils, 'solved')"
                                                                 label="" :disabled="issue_datils.user_id != user.id"
                                                                 style=""></el-checkbox>
                                                    <span v-if="project_type == 'design'"
                                                          style="color:#606266;font-weight:normal;">TODO</span>
                                                    <span v-else
                                                          style="color:#606266;font-weight:normal;">UNRESOLVED</span>
                                                </div>

                                                <div class="unresolved-box" v-else
                                                     style="color: #da4545;; text-align: center; font-weight: bold; padding: 5px;background:#eee; height:45px; width:75%; padding:12px 0px;border:none;border-radius:4px;">
                                                    <el-checkbox label="" :disabled="issue_datils.user_id != user.id"
                                                                 style=""></el-checkbox>
                                                    <span v-if="project_type == 'design'"
                                                          style="color:#606266;font-weight:normal;">TODO</span>
                                                    <span v-else
                                                          style="color:#606266;font-weight:normal;">UNRESOLVED</span>
                                                </div>
                                            </div>
                                        </el-col>
                                        <el-col :md="3" :xs="3">
                                            <div style="color: #626364; padding-top: 0;float: right;">
                                                <!-- <div v-bind:class="{'tag' : issue_datils.status != 'solved', 'tag_solved' : issue_datils.status == 'solved'}" style="border-radius: 50px;width: 45px;height: 45px;padding-top: 8px;text-align: center;font-size: 17px;font-weight: 600;background:#ccc;box-shadow:0 4px 7px #ddd;">{{issue_datils.label}}</div> -->
                                                <div class="tag" v-bind:class="{
                                            'tag_solved' : issue_datils.status == 'solved',
                                            'tag_freelancer' : issue_datils.owner_type == 'freelancer',
                                            'tag_client' : issue_datils.owner_type == 'client'}"
                                                     @click="gotoIssue(issue_datils.group)">{{issue_datils.label}}
                                                </div>
                                            </div>
                                        </el-col>
                                        <el-row style="padding-top: 3%; margin-bottom: 0px; clear:both">
                                            <el-col :md="3" :xs="3">
                                                <div style="padding: 0px;position: relative;float: left;width: 45px;height: 45px;">
                                                    <el-tooltip v-if="issue_datils.user"
                                                                :content="(issue_datils.user.id == current_user_id) ? 'Me' : issue_datils.user.name"
                                                                placement="bottom" effect="light">
                                                        <img :src="issue_datils.user.photo_url"
                                                             class="img-circle special-img"
                                                             style="width: 100%; height: auto;padding: 0px;border-style: solid;border-color: #a5a3a3; border-width: px;">
                                                    </el-tooltip>
                                                </div>
                                            </el-col>
                                            <el-col :md="18" :xs="18" class="comment-space">
                                                <div>
                                                    <div
                                                            @click="issue_datils.show_issue_controls = true"
                                                            @blur="syncIssueDesc(issue_datils.id, true)"
                                                            @input="updateIssue(issue_datils.id, $event)"
                                                            style="color: #626364; padding-top: 0px;    border: 1px solid transparent;padding-left:0px;font-weight;500;margin-left:10px; min-height:30px; word-wrap:break-word;"
                                                            :contenteditable="current_rol == 'client' || issue_datils.user_id == user.id"
                                                            ref="issue_content"
                                                            :id="'_current_issue' + issue_datils.id">
                                                        {{issue_datils.description}}
                                                    </div>

                                                    <div v-if="current_rol == 'client' && issue_datils.status != 'solved' && issue_datils.user_id == user.id"
                                                         class="dropdown issue-controls">
                                                        <i class="el-icon-more dropdown-toggle" data-toggle="dropdown"
                                                           style="color: #cecfcf;font-size: 22px;position: absolute;transform: rotate(-90deg);right: 0px;top:20px;"></i>
                                                        <ul class="dropdown-menu issue-controls-menu">
                                                            <li>
                                                                <a @click="inlineIssueEdit(issue_datils.id, '_current_issue')">Edit</a>
                                                            </li>
                                                            <li>
                                                                <a @click="issue_datils.show_issue = !issue_datils.show_issue; issue_datils.show_issue_del_conf = !issue_datils.show_issue_del_conf">Delete</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>

                                                <div>
                                                    <div v-show="issue_datils.show_issue_controls">
                                                        <i v-show="current_rol == 'client' || issue_datils.user_id == user.id"
                                                           @click="issue_datils.show_issue_controls = false"
                                                           class="el-icon-circle-check"
                                                           style="color: rgb(250, 115, 115); font-size: 25px; float:right; margin-left:10px;cursor: pointer;"></i>
                                                    </div>
                                                    <div @click="issue_datils.show_issue_attachment = !issue_datils.show_issue_attachment"
                                                         v-if="issue_datils.project_files_id">
                                                        <i class="fa fa-paperclip"
                                                           style="color: rgb(250, 115, 115); font-size: 22px; float:right;"></i>
                                                    </div>
                                                    <div v-show="issue_datils.show_issue_controls" v-else>
                                                        <i v-if="current_rol == 'client' && issue_datils.status != 'solved' && issue_datils.user_id == user.id"
                                                           @click="issue_datils.show_issue = !issue_datils.show_issue; issue_datils.show_issue_attachment_uploader = !issue_datils.show_issue_attachment_uploader"
                                                           class="fa fa-paperclip"
                                                           style="color: #cecfcf; font-size: 22px; float:right;"></i>
                                                    </div>
                                                    <div class="new-issue-img"
                                                         style="position:relative;float:left; width:100%;"
                                                         v-if="issue_datils.project_files_id"
                                                         v-show="issue_datils.show_issue_attachment">
                                                        <img class="issue_img_thumb"
                                                             :src="'/storage/' + issue_datils.project_files.thumb_path">
                                                        <img src="/img/rubbish-bin.png"
                                                             v-if="issue_datils.user_id == user.id"
                                                             @click="issue_datils.show_issue = false; issue_datils.show_issue_del_img_conf = true"
                                                             class=""
                                                             style="position: absolute;bottom: 10px; right: 10px; cursor: pointer; width: 33px; z-index:987654;  padding: 4px; border-radius: 2px;">
                                                    </div>
                                                </div>

                                                <!-- <i class="el-icon-more" style="color: #999;font-size: 22px;position: absolute;transform: rotate(-90deg);right: 10px;top:20px;"></i> -->
                                            </el-col>
                                            <el-col :md="4">
                                                <!-- <div style="color: #626364; padding-top: 10px">
                                                    <div v-bind:class="{'tag' : issue_datils.status != 'solved', 'tag_solved' : issue_datils.status == 'solved'}" style="border-radius: 50px; width: 30px; height: 30px; padding-top: 6px; text-align: center;font-size:13px">{{issue_datils.label}}</div>
                                                </div> -->
                                            </el-col>
                                        </el-row>
                                        <!-- <el-row type="flex" style="margin-bottom: 0px; margin-top: 15px">
                                            <el-col v-if="issue_datils.project_files_id" :xs="8" :xl="8">
                                                <el-button @click="showIssueImage(issue_datils.project_files.path, issue_datils.project_files.thumb_path, issue_datils.project_files.id)"
                                                    type="text" class="issue-button-details" icon="el-icon-picture-outline" style="float: left;margin-left: 15px"
                                                    size="mini">
                                                    <span style="font-size: 14px;">Image attached</span>
                                                </el-button>
                                            </el-col>
                                            <el-col v-else :xs="8" :xl="8">
                                                <el-button v-if="current_rol == 'client' && issue_datils.status != 'solved'" @click="editIssue(issue_datils.id)" type="text"
                                                    class="issue-button-details" style="float: left;margin-left: 15px" size="mini">
                                                    <span class="fa fa-paperclip" style="font-size: 20px;"></span>
                                                    <span style="font-size: 14px">Add image</span>
                                                </el-button>
                                            </el-col>
                                            <el-col :xs="3" :xl="3" :offset="10">
                                                <el-button v-if="current_rol == 'client' && issue_datils.status != 'solved'" @click="editIssue(issue_datils.id)" class="issue-button-details"
                                                    icon="el-icon-edit" size="mini">
                                                </el-button>
                                            </el-col>
                                            <el-col :xs="3" :xl="3">
                                                <el-button v-if="current_rol == 'client' && issue_datils.status != 'solved'" @click="deleteIssue()" class="issue-button-edit"
                                                    icon="el-icon-delete" size="mini">
                                                </el-button>
                                            </el-col>
                                        </el-row> -->
                                    </div>
                                </el-col>
                            </el-row>
                            <el-row v-if="issue_datils.status != 'solved'"
                                    style="padding:0 10px; margin-bottom: 0px; background: rgba(0, 0, 0, 0) linear-gradient(#fe7373, #fd9797) repeat scroll 0 0; min-height:50px;">
                                <el-input v-model="current_comment.description"
                                          @keydown.native.enter.prevent="addComment(issue_datils.id)"
                                          class="comment_textarea" type="textarea" :autosize="{ minRows: 1, maxRows: 6}"
                                          placeholder="Post a comment..."
                                          v-bind:class="{'error_textarea' : errors.comment_description}">
                                </el-input>
                                <span v-if="errors.comment_description"
                                      style="float: left; color: rgb(211, 12, 12); font-size: 13px">{{errors_msg.comment_description}}</span>
                                <el-upload
                                        :data="imageFormData"
                                        :on-change="commentAttachmentAdded"
                                        :on-remove="commentAttachmentRemoved"
                                        :on-success="commentAttachmentUploaded"
                                        :limit="1"
                                        :auto-upload="false"
                                        ref="upload_comment_attachment"
                                        action="/api/files/upload"
                                        name="photos"
                                        style="margin-bottom: 10px; float:left;"
                                >
                                    <el-button slot="trigger"
                                               style="border: 0px; background: none; position: absolute; float: right; top: 6px; right: 50px; padding: 10px 0px;">
                                        <i class="fa fa-paperclip" style="color: #fff; font-size: 22px;"></i>
                                    </el-button>
                                </el-upload>

                                <!-- <el-button  style="border: 0px;background: none;position: absolute;float: right;top: 10px;right: 30px;">
                                   <i class="fa fa-paperclip" style="color: #fff; font-size: 22px"></i>
                               </el-button> -->
                                <el-button @click="addComment(issue_datils.id)"
                                           style="border: 0px;background: none;position: absolute;float: right;top: 15px;right: 15px;padding: 0;">
                                    <!--  <i class="fa fa-send" style="color: #fff; font-size: 22px"></i> -->
                                    <img src="/img/send.png" style="width:23px;">
                                </el-button>

                            </el-row>
                            <el-row v-if="issue_datils.status != 'solved'" type="flex" justify="end"
                                    style="padding-bottom: 0px; margin-bottom: 0px">
                                <!--  <el-col :sm="4">
                                                            <el-button style="border: 0px">
                                                            <i class="fa fa-paperclip fa-2x" style="color: #8f9091; font-size: 22px"></i>
                                                            </el-button>
                                                        </el-col> -->
                                <el-col :sm="4">
                                    <!--  <el-button @click="addComment(issue_datils.id)" style="border: 0px">
                                         <i class="fa fa-send" style="color: #8f9091; font-size: 22px"></i>
                                     </el-button> -->
                                </el-col>
                            </el-row>
                            <el-row style="padding: 5px;padding-bottom:20px;">
                                <div v-for="(comment, key) in issue_datils.comments" :key=key>
                                    <div :class="{'comment right' : comment.user_id == user.id, 'comment left' : comment.user_id != user.id}"
                                         style="margin-left: 0px">
                                        <el-row style="margin-bottom: 0px">
                                            <el-col :md="18" :xs="18"
                                                    :class="{'right-desc' : comment.user_id == user.id, 'left-desc' : comment.user_id != user.id}">
                                                <span>{{ comment.user.name }}</span>

                                                <div v-show="comment.show_comment">
                                                    <div>
                                                        <p>{{comment.description}}</p>
                                                        <span style="width: auto; text-align: right; display: block;">
                                                      <div v-if="comment.project_files_id">
                                                        <i @click="comment.show_attachment = !comment.show_attachment"
                                                           class="fa fa-paperclip fa-2x"
                                                           style="color: rgb(250,115,115); font-size: 22px;"
                                                           v-if="comment.user_id == user.id"></i>
                                                        <i @click="comment.show_attachment = !comment.show_attachment"
                                                           class="fa fa-paperclip fa-2x"
                                                           style="color: #ffffff; font-size: 22px;" v-else></i>
													  </div>
                                                      <div v-else>
                                                        <i v-if="comment.user_id == user.id"
                                                           @click="comment.show_comment = !comment.show_comment; comment.show_attachment_uploader = !comment.show_attachment_uploader"
                                                           class="fa fa-paperclip fa-2x"
                                                           style="color: #cecfcf; font-size: 22px;"></i>
                                                      </div>
                                                    </span>
                                                    </div>

                                                    <div v-if="comment.project_files_id"
                                                         v-show="comment.show_attachment" class="comment_attachm">
                                                        <img :src="'/storage/' + comment.project_files.thumb_path"
                                                             :alt="comment.description" class="comment_img_thumb">
                                                        <img src="/img/rubbish-bin.png"
                                                             v-if="comment.user_id == user.id"
                                                             @click="comment.show_comment = !comment.show_comment; comment.show_attachment_del_conf = !comment.show_attachment_del_conf"
                                                             class=""
                                                             style="position: absolute;bottom: 10px; right: 10px; cursor: pointer; width: 33px; padding: 4px; border-radius: 2px;z-index:987654;">
                                                    </div>

                                                </div>

                                                <div v-if="!comment.project_files_id" class="attachment-popup"
                                                     v-show="comment.show_attachment_uploader">
                                                    <el-upload
                                                            :data="imageFormData"
                                                            :on-success="commentAttachmentUploaded"
                                                            :limit="1"
                                                            :auto-upload="false"
                                                            :file-list="fileList"
                                                            :ref="'save_comment_image' + comment.id"
                                                            action="/api/files/upload"
                                                            name="photos"
                                                            drag>
                                                        <i class="el-icon-upload"></i>
                                                        <div class="el-upload__text">Click or drag here to uploads
                                                            files
                                                        </div>
                                                    </el-upload>
                                                    <el-row>
                                                        <el-button
                                                                @click="comment.show_comment = !comment.show_comment; comment.show_attachment_uploader = !comment.show_attachment_uploader">
                                                            Cancel
                                                        </el-button>
                                                        <el-button @click="saveCommentImage(comment.id, key)"
                                                                   type="primary">Save
                                                        </el-button>
                                                    </el-row>
                                                </div>

                                                <div v-if="comment.project_files_id"
                                                     v-show="comment.show_attachment_del_conf" class="delete-confirm">
                                                    <div class="el-delete__text" style=" font-weight: 600;">Are you
                                                        sure?
                                                    </div>
                                                    <div class="el-delete__text-2">You won't be able to revert this!
                                                    </div>
                                                    <el-row>
                                                        <el-button
                                                                @click="deleteCommentImage(comment.id, comment.project_files_id);"
                                                                type="primary" class="delete-red">Yes, delete
                                                        </el-button>
                                                        <el-button
                                                                @click="comment.show_comment = !comment.show_comment; comment.show_attachment_del_conf = !comment.show_attachment_del_conf">
                                                            Cancel
                                                        </el-button>
                                                    </el-row>
                                                </div>

                                                <div v-show="comment.show_comment_del_conf"
                                                     class="delete-confirm-comment">
                                                    <el-row>
                                                        <el-button @click="deleteComment(issue_datils.id, comment.id)"
                                                                   type="primary" class="yes-delete">Yes
                                                        </el-button>
                                                        <el-button
                                                                @click="comment.show_comment = !comment.show_comment; comment.show_comment_del_conf = !comment.show_comment_del_conf">
                                                            Cancel
                                                        </el-button>
                                                    </el-row>
                                                </div>

                                            </el-col>
                                            <el-col :md="5" :xs="5"
                                                    :class="{'right-img' : comment.user_id == user.id, 'left-img' : comment.user_id != user.id}">
                                                <div style="padding: 0px;position: relative;float: left;width: 45px;height: 45px;">
                                                    <span>{{comment.created_at | utc_to_local }}</span>
                                                    <el-tooltip v-if="comment.user"
                                                                :content="(comment.user.id == current_user_id) ? 'Me' : comment.user.name"
                                                                placement="bottom" effect="light">
                                                        <img :src="comment.user.photo_url"
                                                             class="img-circle special-img"
                                                             style="width: 100%; height: auto; padding: 0px; border-style: solid; border-color: rgb(165, 163, 163); border-width: 0px;">
                                                    </el-tooltip>
                                                    <img v-else src="" class="img-circle special-img"
                                                         style="width: 100%; height: auto; padding: 0px; border-style: solid; border-color: rgb(165, 163, 163); border-width: 0px;">

                                                    <div v-if="issue_datils.status != 'solved' && comment.user_id == user.id"
                                                         class="dropdown comment-controls">
                                                        <i class="el-icon-more dropdown-toggle" data-toggle="dropdown"
                                                           style="color: #cecfcf;font-size: 22px;display:block;"></i>
                                                        <ul class="dropdown-menu comment-controls-menu">
                                                            <li><a @click="editComment(comment.issue_id, comment.id)">Edit</a>
                                                            </li>
                                                            <!-- <li><a @click="deleteComment(issue_datils.id, comment.id)">Delete</a></li> -->
                                                            <li>
                                                                <a @click="comment.show_comment = !comment.show_comment; comment.show_comment_del_conf = !comment.show_comment_del_conf">Delete</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </el-col>
                                        </el-row>
                                        <!-- <el-row type="flex" style="margin-bottom: 0px; margin-left: 0px">
                                            <el-col v-if="comment.project_files_id" :xs="8" :xl="8">
                                                <el-button @click="showIssueImage(comment.project_files.path, comment.project_files.thumb_path, comment.project_files.id)"
                                                    type="text" class="issue-button-details" icon="el-icon-picture-outline" style="float: left"
                                                    size="mini">
                                                    <span style="font-size: 14px;">Image attached</span>
                                                </el-button>
                                            </el-col>
                                            <el-col v-else :xs="8" :xl="8">
                                                <el-button v-if="issue_datils.status != 'solved' && comment.user_id == user.id" @click="editComment(comment.issue_id, comment.id)"
                                                    type="text" class="issue-button-details" style="float: left" size="mini">
                                                    <span class="fa fa-paperclip" style="font-size: 20px;"></span>
                                                    <span style="font-size: 14px;">Add image</span>
                                                </el-button>
                                            </el-col>
                                            <el-col :xs="3" :xl="3" :offset="10">
                                                <el-button v-if="issue_datils.status != 'solved' && comment.user_id == user.id" @click="editComment(comment.issue_id, comment.id)"
                                                    class="issue-button-details" icon="el-icon-edit" size="mini">
                                                </el-button>
                                            </el-col>
                                            <el-col :xs="3" :xl="3">
                                                <el-button v-if="issue_datils.status != 'solved' && comment.user_id == user.id" @click="deleteComment(issue_datils.id, comment.id)"
                                                    class="issue-button-edit" icon="el-icon-delete" size="mini">
                                                </el-button>
                                            </el-col>
                                        </el-row> -->
                                        <!--    <el-row v-if="comment.project_files_id" type="flex" style="margin-bottom: 0px; margin-left: 10px">
                                                                <el-col :xs="24" :xl="24">
                                                                <el-button @click="showIssueImage(comment.project_files.path, comment.project_files.thumb_path, comment.project_files.id)"
                                                                    type="text" class="issue-button-details" icon="el-icon-picture-outline" style="float: left" size="mini">
                                                                    <span style="font-size: 14px;">Image attached</span>
                                                                </el-button>
                                                                </el-col>
                                                            </el-row> -->
                                    </div>
                                </div>
                            </el-row>
                        </div>
                    </transition-group>
                    <!--  </div> -->
                    <!--  </el-col> -->
                </el-row>
            </el-col>
        </el-row>
        <!--<div style="margin-top: 63px !important">-->
        <!---->
        <!--</div>-->
        <!-- <el-dialog title="New issue" :top="'5px'" :close-on-click-modal="false" :close-on-press-escape="false"
                   :show-close="false"
                   :visible="showIssueDialog" :open="jqueryCssAdjust()" @open="onOpenDialogNewIssue"
                   @close="onCloseDialogNewIssue"
                   width="30%" center>
            <el-input type="textarea" ref="autofocus_issue_create" :autofocus="true" :rows="5"
                      placeholder="Type issue description..."
                      v-model="new_issue.description" v-bind:class="{'error_textarea' : errors.issue_description}">
            </el-input>
            <span v-if="errors.issue_description" style="float: right; color: rgb(211, 12, 12); font-size: 13px">{{errors_msg.issue_description}}</span>
            <span slot="footer" class="dialog-footer">
                <el-button @click="cancelIssue()">Cancel</el-button>
                <el-button type="primary" @click="addIssue()">Save</el-button>
            </span>
        </el-dialog> -->

        <el-dialog title="Add Image" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false"
                   :visible.sync="showEditableIssueDialog"
                   @open="onOpenDialogEditIssue" @close="onCloseDialogEditIssue" width="30%" center>
            <!-- <el-input type="textarea" ref="autofocus_issue_edit" :autofocus="true" :rows="5" v-model="editable_issue.description" v-bind:class="{'error_textarea' : errors.editable_issue_description}">
            </el-input> -->
            <!-- <span v-if="errors.editable_issue_description" style="float: right; color: rgb(211, 12, 12); font-size: 13px">{{errors_msg.editable_issue_description}}</span> -->
            <!-- </el-input> -->
            <span slot="footer" class="dialog-footer">
                <el-button @click="cancelEditableIssue()">Cancel</el-button>
                <!-- <el-button type="primary" @click="updateIssue()">Save</el-button> -->
                <el-button type="primary" @click="showEditableIssueDialog = !showEditableIssueDialog">Save</el-button>
            </span>
            <el-row v-if="!editable_issue.project_files_id">
                <el-col style="text-align: center">
                    <div class="uploader" style="width: 100%; margin-top: 0px">
                        <el-upload ref="upload" drag :data="imageFormData" action="/api/files/upload"
                                   :on-preview="handlePreview" :on-error="handleError"
                                   :on-remove="deleteFile" :on-success="issueImageUploaded" :auto-upload="true"
                                   :file-list="fileList" name="photos"
                                   list-type="picture">
                            <i class="el-icon-upload"></i>
                            <div class="el-upload__text">Drop file here or
                                <em>click to upload</em>
                            </div>
                        </el-upload>
                    </div>
                </el-col>
            </el-row>
        </el-dialog>

        <el-dialog title="Edit comment" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false"
                   :visible.sync="showEditableCommentDialog"
                   @open="onOpenDialogEditComment" @close="onCloseDialogEditComment" width="30%" center>
            <el-input type="textarea" ref="autofocus_comment" :autofocus="true" :rows="5"
                      v-model="editable_comment.description"
                      v-bind:class="{'error_textarea' : errors.editable_comment_description}">
            </el-input>
            <span v-if="errors.editable_comment_description"
                  style="float: right; color: rgb(211, 12, 12); font-size: 13px">{{errors_msg.editable_comment_description}}</span>
            <span slot="footer" class="dialog-footer">
                <el-button @click="cancelEditableComment()">Cancel</el-button>
                <el-button type="primary" @click="updateComment()">Save</el-button>
            </span>
            <el-row v-if="!editable_comment.project_files_id">
                <el-col style="text-align: center">
                    <div class="uploader" style="width: 100%; margin-top: 15px">
                        <el-upload ref="upload" drag :data="imageFormData" action="/api/files/upload"
                                   :on-preview="handlePreview" :on-error="handleError"
                                   :on-remove="deleteFile" :on-success="handleSuccess" :auto-upload="true"
                                   :file-list="fileList" name="photos"
                                   list-type="picture">
                            <i class="el-icon-upload"></i>
                            <div class="el-upload__text">Drop file here or
                                <em>click to upload</em>
                            </div>
                        </el-upload>
                    </div>
                </el-col>
            </el-row>
        </el-dialog>

        <el-dialog :close-on-click-modal="true" :close-on-press-escape="true" :show-close="true"
                   :visible.sync="showIssueImageFlag"
                   width="60%" center>
            <el-row>
                <el-col :xs="4">
                    <div style="padding: 15px">
                        <span v-if="current_issue.user_id == current_user_id && current_issue.status != 'solved'"
                              @click="deletePicture(image_id)"
                              class="fa fa-trash" style="font-size: 24px; cursor: pointer"></span>
                    </div>
                </el-col>
                <el-col :xs="20">
                    <img :src="image_preview" style="width: 100%; height: auto">
                </el-col>
            </el-row>
        </el-dialog>
        <el-dialog title="Submit Corrections" :close-on-click-modal="true" :close-on-press-escape="true"
                   :show-close="true"
                   :visible.sync="showSubmitOptions"
                   width="40%" center>
            <el-row>
                <el-col style="text-align: center">
                    <h3>
                        {{user.name}}, please make your decision below.
                    </h3>
                    <h5>
                        Are these your corrections for <span style="color: #24a158">Revision Round {{current_version_number}}</span>?
                    </h5>
                </el-col>
            </el-row>
            <el-row>
                <el-col :xs="24" :md="16" :offset="5" style="margin-top: 20px; margin-bottom: 20px">
                    <el-button style="background-color: #fa5555; color: #fff; font-weight: bold; border: 0px"
                               @click="submitCorrections()" type="primary">Yes, submit corrections
                    </el-button>
                    <el-button
                            style="background-color: rgb(224, 223, 223); color: rgb(87, 86, 86); font-weight: bold; border: 0px"
                            @click="cancelSubmitCorrections()"
                            type="primary">Cancel
                    </el-button>
                </el-col>
            </el-row>
        </el-dialog>

        <el-dialog
                :title="(project_type == 'design') ? 'Approve Design' : (project_type == 'video') ? 'Approve Video' : 'Approve Website'"
                :close-on-click-modal="true" :close-on-press-escape="true" :show-close="true"
                :visible.sync="showApproveDesign"
                width="40%" center>
            <el-row>
                <el-col style="text-align: center">
                    <h3>
                        {{user.name}}, please make your decision below.
                    </h3>
                    <h5 v-if="project_type == 'design'">
                        Are you sure you want to approve this design for <span style="color: #24a158">Revision Round {{current_version_number}}</span>?
                    </h5>
                    <h5 v-else-if="project_type == 'website'">
                        <span>Are you sure you want to approve this website</span>
                    </h5>
                </el-col>
            </el-row>
            <el-row style="text-align: center">
                <el-col :xs="24" :md="16" :offset="4" style="margin-top: 20px; margin-bottom: 20px">
                    <el-button style="background-color: #fa5555; color: #fff; font-weight: bold; border: 0px"
                               @click="approveProject()" type="primary">Yes, approve {{project_type}}
                    </el-button>
                    <el-button
                            style="background-color: rgb(224, 223, 223); color: rgb(87, 86, 86); font-weight: bold; border: 0px"
                            @click="cancelApproveCorrections()"
                            type="primary">Cancel
                    </el-button>
                </el-col>
            </el-row>
        </el-dialog>
        <el-dialog title="Upload New Image" :close-on-click-modal="false" :close-on-press-escape="false"
                   :show-close="false" :visible.sync="showSlideUploadDialog"
                   @open="onOpenDialogSlideUpload" @close="onCloseDialogSlideUpload" width="30%" center>
            <pdf-converting v-if="pdfLoader"></pdf-converting>
            <el-row>
                <el-col style="text-align: center">
                    <div class="uploader" style="width: 100%; margin-top: 0px">
                        <el-upload ref="upload" drag :data="imageFormData" action="/api/files/upload"
                                   :on-preview="handlePreview" :on-error="handleError"
                                   :on-remove="deleteFile" :on-success="handleSuccess"
                                   :before-upload="handleUpload" :auto-upload="true"
                                   :file-list="fileList" name="photos"
                                   list-type="picture">
                            <i class="el-icon-upload"></i>
                            <div class="el-upload__text">Drop file here or
                                <em>click to upload</em>
                            </div>
                            <div class="el-upload__tip" slot="tip">jpg/png/pdf files with a size less than 500kb</div>
                        </el-upload>
                    </div>
                </el-col>
            </el-row>
            <el-row>
                <el-col :xs="24" :md="16" :offset="8" style="margin-top: 20px">
                    <!-- <el-button type="primary" @click="finishCreateProject()" :loading="saveLoading">Finish</el-button> -->
                    <el-button type="primary" @click="sendProjectSlides()">Finish & Send</el-button>
                    <el-button @click="cancelSlideUpload()">Cancel</el-button>
                </el-col>
            </el-row>
        </el-dialog>

        <el-dialog title="Upload New Proofs" :close-on-click-modal="false" :close-on-press-escape="false"
                   :show-close="false" :visible.sync="showNewRevisionDialog"
                   @open="onOpenDialogNewRevision" @close="onCloseDialogNewRevision" width="30%" center>
            <pdf-converting v-if="pdfLoader"></pdf-converting>
            <el-row>
                <el-col style="text-align: center">
                    <div class="uploader" style="width: 100%; margin-top: 0px">
                        <el-upload ref="upload" drag :data="imageFormData" action="/api/files/upload"
                                   :on-preview="handlePreview" :on-error="handleError"
                                   :on-remove="deleteFile" :on-success="handleSuccess"
                                   :before-upload="handleUpload" :auto-upload="true"
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
                <el-col :xs="24" :md="24" style="margin-top: 20px; text-align: center">
                    <!-- <el-button type="primary" @click="finishCreateProject()" :loading="saveLoading">Finish</el-button> -->
                    <el-button type="primary" @click="sendNewRevision()">Finish & Send</el-button>
                    <el-button @click="cancelNewRevision()">Cancel</el-button>
                </el-col>
            </el-row>
        </el-dialog>

        <!--Final Files-->
        <final-files-dialog v-if="showFinalFilesDialog"
                            :showFinalFilesDialog="showFinalFilesDialog"
                            :currentRole="current_rol"
                            :projectStatus="project_status"
                            :projectId="project_id"
                            :revisionId="revision_id"
                            v-on:closeFinalFilesDialog="showFinalFilesDialog = false"
                            v-on:filesSended="finalFilesSended">
        </final-files-dialog>

        <el-dialog title="New Collaborators" :close-on-click-modal="false" :close-on-press-escape="false"
                   :show-close="false"
                   :visible.sync="showNewCollaboratorDialog"
                   @open="onOpenDialogNewCollaborators" @close="onCloseDialogNewCollaborators" width="50%" center>
            <el-row>
                <el-col style="text-align: center">

                    <h4>
                        Please add other collaborators here by email
                    </h4>
                    <!--<i style="font-style: italic;" v-if="current_rol == 'freelancer'">EX: Dropbox, Google Drive, Box</i>-->

                    <div>
                        <el-tag
                                :key="newCollaborator"
                                v-for="newCollaborator in newCollaborators"
                                :disable-transitions="false"
                                :closable="true"
                                @close="handleClose(newCollaborator)">
                            {{newCollaborator}}
                        </el-tag>
                        <el-input
                                class="input-new-tag"
                                v-if="inputVisible"
                                v-model="inputValue"
                                ref="saveTagInput"
                                size="mini"
                                @keyup.enter.native="handleInputNewCollab"
                                @blur="handleInputNewCollab"
                        >
                        </el-input>
                        <el-button v-else class="button-new-tag" size="small" @click="showInput">+ New Collaborator
                        </el-button>
                    </div>
                </el-col>
            </el-row>
            <el-row>
                <el-col :xs="24" :md="16" :offset="8" style="margin-top: 20px">
                    <el-button type="primary" @click="inviteCollabs()">Invite</el-button>
                    <el-button @click="cancelInviteCollabs()">Cancel</el-button>
                </el-col>
            </el-row>
        </el-dialog>
        <plugin-install-dialog v-if="promptPluginInstall"
                               :promptPluginInstall="promptPluginInstall"
                               :pluginInstalled="pluginInstalled"
                               v-on:continue="promptPluginInstall = false"
                               v-on:takeScreenshots="takeScreenshots"
                               v-on:installPlugin="installProofloPlugin">
        </plugin-install-dialog>
    </div>

</template>

<script>
    /*   import 'element-ui/lib/theme-chalk/index.css'; */
    import ElementUI from 'element-ui'
    import Konva from "konva";
    import moment from "moment";
    import Auth from '../../services/auth'
    import {mapGetters} from "vuex";

    export default {
        props: ["project_id", "revision_id", "project_hash", 'project'],
        mixins: [Konva],
        components: {
            'pdf-converting': require('./partials/PdfConverting'),
            'plugin-install-dialog': require('./partials/PluginInstallDialog'),
            'final-files-dialog': require('./partials/FinalFilesDialog')
        },
        data() {
            return {
                issue_have_attachment: false,
                comment_have_attachment: false,
                pdfLoader: false,
                collaborators: [],
                teamMembers: [],
                stage: {},
                background: {},
                rect: {},
                background_image: {},
                image: {},
                group: {},
                background_group: {},
                text: {},
                selectRect: {},
                draggedDistance: {},
                group_counter: 1,
                current_group_id: "",
                current_group_label: "",
                isActiveIssue: false,
                user: Spark.state.user,
                versions: [],
                current_version_number: "",
                last_version_number: "",
                fullscreenLoading: false,
                value: "",
                current_review_id: "",

                proofs: [],
                current_proof: {},
                current_issue: {
                    id: "",
                    group_id: "",
                    proof_id: "",
                    description: "",
                    label: "",
                    user: "",
                    status: "",
                    active_issue: "",
                    comments: []
                },
                new_issue: {
                    id: "",
                    group_id: "",
                    proof_id: "",
                    description: "",
                    label: "",
                    user: "",
                    status: "",
                    active_issue: "",
                    comments: []
                },
                editable_issue: {
                    id: "",
                    group_id: "",
                    proof_id: "",
                    description: "",
                    label: "",
                    user: "",
                    status: '',
                    active_issue: "",
                    comments: [],
                    project_files_id: "",
                    project_files: {}
                },
                current_comment: {
                    id: "",
                    issue_id: "",
                    description: "",
                    label: "",
                    user: ""
                },
                editable_comment: {
                    id: "",
                    issue_id: "",
                    description: "",
                    label: "",
                    user: ""
                },

                issue_datils: {},

                pen_active: false,
                boxtool_active: false,
                penInfoVisible: true,
                eraser_active: false,
                zoom_in_active: false,
                zoom_out_active: false,
                remove_active: false,
                showIssueDialog: false,
                showIssueDetails: false,
                initialStageWidth: "",
                initialStageHeight: "",
                showEditableIssueDialog: false,
                showEditableCommentDialog: false,
                showSubmitOptions: false,
                showApproveDesign: false,
                showSlideUploadDialog: false,
                showNewRevisionDialog: false,
                showFinalFilesDialog: false,
                showNewCollaboratorDialog: false,
                posStart: 0,
                posNow: 0,
                headers: {},
                imageFormData: {
                    project_id: "",
                    file_type: "",
                    owner_type: "",
                },
                fileList: [],
                savedFiles: [],
                image_preview: "",
                image_thumb: "",
                image_id: "",
                showIssueImageFlag: false,
                disabled_remove: false,
                decision: 'approved',
                current_rol: '',
                current_user_id: '',
                showSubmitButtons: false,
                errors: {
                    comment_description: false,
                    issue_description: false,
                    editable_issue_description: false,
                    editable_comment_description: false
                },
                errors_msg: {
                    comment_description: false,
                    issue_description: false,
                    editable_issue_description: false,
                    editable_comment_description: false,
                },
                showSendBackButton: true,
                zoom_level: 1,
                zoom_level_old: 1,
                showSideBar: true,
                autofocus_textarea: true,
                correctionButton: 'SUBMIT CORRECTIONS',
                correctionButtonStyle: {
                    'background': '#e0dfdf',
                    'color': 'rgb(87, 86, 86)',
                    'font-weight': 'bold',
                    'border': '0px',
                    'padding': '12px 20px'
                },
                approveButton: 'APPROVE DESIGN',
                addIssueIcons: '/img/send-grey.png',
                approveButtonStyle: {
                    'background': '#e0dfdf',
                    'color': 'rgb(87, 86, 86)',
                    'font-weight': 'bold',
                    'border': '0px',
                },
                revision_status: '',
                project_status: '',
                project_type: '',
                exitingUsers: [],
                newCollaborators: [],
                inputVisible: false,
                inputValue: '',
                uploadProofsStatus: false,
                approveStatus: false,
                slide_page_number: 1,
                total_issues: 0,
                solved_issues: 0,
                percentage: 0,
                pluginInstalled: false,
                websiteURI: '',
                promptPluginInstall: false,
            };
        },
        filters: {
            utc_to_local: function (date) {
                if (!date) return '';
                var utc = moment.utc(date).toDate();
                return moment(utc).local().format('hh:mm A');
            }
        },
        methods: {
            getUnreadComments(proof) {
                let unread_comments = 0;

                proof.issues.forEach(function (issue) {
                    if (issue.unread_comments) {
                        unread_comments += issue.unread_comments.length;
                    }
                });

                return unread_comments;
            },
            handlePaginationChange(value) {
                if (value) {
                    this.loadThumb(value - 1);
                }
            },
            handleDropdownCommand(command) {
                if (command.action == 'delete') {
                    this.deleteProof(command.proof_id)
                }
            },
            deleteProof(proof_id) {
                var self = this;
                this.$confirm('Are you sure you want to delete this proof', 'Warning', {
                    confirmButtonText: 'Yes',
                    confirmButtonColor: "#ee5757",
                    cancelButtonText: 'Cancel',
                    type: 'warning',
                    title: ''
                }).then(() => {
                    this.fullscreenLoading = true;
                    axios.delete('/api/proof/delete_proof/' + proof_id).then(response => {
                        this.fullscreenLoading = false;
                        if (response.data.status) {
                            toastr['success'](response.data.message, 'Success');
                            this.proofs = [];
                            this.loadInitialRevision(this.project_id, this.revision_id);
                        } else {
                            toastr['error'](response.data.errors, 'Error');
                        }
                    })
                }).catch(() => {
                    // Canceled
                });
            },
            showInput() {
                this.inputVisible = true;
                this.$nextTick(_ => {
                    this.$refs.saveTagInput.$refs.input.focus();
                });
            },
            handleInputNewCollab() {
                let inputValue = this.inputValue;
                if (inputValue) {
                    if (this.newCollaborators.indexOf(inputValue) == -1 && this.exitingUsers.indexOf(inputValue) == -1) {
                        if (this.isEmail(inputValue)) {
                            this.newCollaborators.push(inputValue);
                        } else {
                            toastr['error']('Invalid email', 'Error');
                        }

                    } else {
                        toastr['error']('This email is already exist', 'Error');
                    }
                }

                this.inputVisible = false;
                this.inputValue = '';
            },
            isEmail(string) {
                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(String(string).toLowerCase())
            },
            initializeStage(img_url, image) {
                var self = this;
                self.group_counter = 1;
                this.showIssueDetails = false;
                if (self.current_proof.canvas_data) {
                    self.stage = Konva.Node.create(self.current_proof.canvas_data, "stage");
                    self.stage.width(self.initialStageWidth);
                    self.background = self.stage.getChildren()[0];
                    self.background.clearBeforeDraw(true);

                    self.background_group = self.background.getChildren()[0];
                    self.background_group.scale({
                        x: 1,
                        y: 1
                    });

                    //Image Drag and Drop for Design project
                    //Image Scroll For Website Project
                    // self.background_group.draggable(
                    //     (self.project_type == 'website') ? false : true
                    // );

                    this.resetPosition();

                    var imageObject = new Image();

                    imageObject.onload = function () {
                        if (self.project_type == 'design') {
                            //Setting canvas stage height for drag and drop
                            self.stage.height(self.initialStageHeight);

                            //Fit to screen
                            let srcWidth = imageObject.width;
                            let srcHeight = imageObject.height;
                            let ratio = Math.min(self.stage.getWidth() / srcWidth, self.stage.getHeight() / srcHeight);
                            self.background_image = new Konva.Image({
                                x: 0,
                                y: 0,
                                image: imageObject,
                                width: this.width * ratio,
                                height: this.height * ratio,
                                draggable: false
                            });

                            self.background_group.position({
                                x: (self.stage.getWidth() - this.width * ratio) / 2,
                                y: 0
                            });

                            self.fitToScreen();
                        } else {
                            self.background_image = new Konva.Image({
                                x: 0,
                                y: 0,
                                image: imageObject,
                                width: self.initialStageWidth,
                                height: self.initialStageWidth / (this.width / this.height),
                                draggable: false
                            });

                            //Setting canvas stage height for scroll
                            self.stage.height(self.initialStageWidth / (imageObject.width / imageObject.height));

                        }

                        self.background_group.on("click tap", function (e) {
                            if (e.target.getClassName() !== 'Circle' && e.target.getClassName() !== 'Text') {
                                if (self.current_rol == 'client' || self.current_rol == 'collaborator' || self.current_rol == 'freelancer') {
                                    if (e.evt.button === 2) {
                                        return false;
                                    }
                                    self.drawCircle();
                                }
                            } else {
                                return false;
                            }

                        });

                        var ratio = self.background_image.width() / self.background_group.get('Image')[0].width();
                        self.adjustPosOfTools(ratio);

                        self.background_group.on("dragmove", function (e) {
                            self.stage.container().style.cursor = 'move';
                        });
                        self.background_group.on("dragend", function (e) {
                            self.stage.container().style.cursor = 'default';
                        });

                        self.background_group.add(self.background_image);
                        self.background_image.moveToBottom();
                        self.background.draw();

                        self.drawBoxtool();
                    }

                    imageObject.src = img_url;

                    self.groups = self.background_group.getChildren(function (node) {
                        if (node.getClassName() === "Group") {
                            if (node.getAttr('owner_type') == self.current_rol) {
                                //If issue is added by current user
                                //he can drag and drop issue dot
                                node.setAttr('draggable', true);
                            } else {
                                //If issue is added by another user
                                //current user can not drag and drop issue dot
                                node.setAttr('draggable', false);
                            }
                            node.on("dragend", function () {
                                self.saveCanvasState();
                            });

                            self.adjustScaleOfTools(node);

                            node.on("click tap", function (event) {
                                /*  var scale = new Konva.Animation(function (frame) {
                                    node.scale({
                                        x: node.scaleX() + 0.01,
                                        y: node.scaleY() + 0.01,
                                    })
                                }, self.background);

                                var normal = new Konva.Animation(function (frame) {
                                    node.scale({
                                        x: 1,
                                        y: 1,
                                    })
                                }, self.background);

                                setTimeout(function(){ scale.start(); }, 100);
                                setTimeout(function(){ normal.start(); }, 1000); */


                                self.current_group_id = node.getAttr("id");
                                self.current_group_label = node.getAttr("label");
                                var circle = self.current_proof.issues.forEach(function (item) {
                                    if (item.group == event.target.getAttr("group_id")) {
                                        self.current_issue = item;
                                    }
                                });

                                self.IssueDetails(self.current_group_id);
                                self.activate_remove();
                            });
                        }
                        return node.getClassName() === "Group";
                    });

                    self.groups.forEach(function (group, index) {
                        if (group.getAttr("counter") >= self.group_counter) {
                            self.group_counter = group.getAttr("counter");
                            self.group_counter++;
                        }
                    });

                    self.texts = self.background.getChildren(function (node) {
                        return node.getClassName() == "Text";
                    });

                } else {
                    self.stage = new Konva.Stage({
                        container: "stage",
                        width: self.initialStageWidth,
                        height: self.initialStageWidth,
                        draggable: false
                    });

                    self.background = new Konva.Layer({});

                    self.background_group = new Konva.Group({
                        id: 'background_group',
                        name: 'background_group',
                        clearBeforeDraw: true,
                        draggable: true,
                        x: 0,
                        y: 0
                    });

                    var imageObject = new Image();

                    imageObject.onload = function () {

                        if (self.project_type == 'design') {

                            //Setting canvas stage height for drag and drop
                            self.stage.height(self.initialStageHeight);

                            //Fit to screen
                            let srcWidth = imageObject.width;
                            let srcHeight = imageObject.height;
                            let ratio = Math.min(self.stage.getWidth() / srcWidth, self.stage.getHeight() / srcHeight);

                            self.background_image = new Konva.Image({
                                x: 0,
                                y: 0,
                                image: imageObject,
                                width: this.width * ratio,
                                height: this.height * ratio,
                                draggable: false
                            });

                            self.background_group.position({
                                x: (self.stage.getWidth() - this.width * ratio) / 2,
                                y: 0
                            });
                        } else {
                            self.background_image = new Konva.Image({
                                x: 0,
                                y: 0,
                                image: imageObject,
                                width: self.initialStageWidth,
                                height: self.initialStageWidth / (this.width / this.height),
                                draggable: false
                            });

                            //Setting canvas stage height for scroll
                            self.stage.height(self.initialStageWidth / (imageObject.width / imageObject.height));

                        }

                        self.background_group.on("click tap", function (e) {
                            if (e.target.getClassName() !== 'Circle' && e.target.getClassName() !== 'Text') {
                                if (self.current_rol == 'client' || self.current_rol == 'collaborator' || self.current_rol == 'freelancer') {
                                    if (e.evt.button === 2) {
                                        return false;
                                    }
                                    self.drawCircle();
                                }
                            } else {
                                return false;
                            }
                        });

                        self.groups = self.background_group.getChildren(function (node) {
                            if (node.getClassName() === "Group") {
                                node.on("dragend", function () {
                                    self.saveCanvasState();
                                });
                                node.on("click tap", function (event) {
                                    var children = self.groups.getChildren(function (node) {
                                        /*   if (node.getClassName() === "Circle" || node.getClassName() === "Text") {
                                            if (node.getAttr('id') == event.target.getAttr('id')) {
                                              node.fill('#0a7738');
                                              node.stroke('#26d35131');
                                              self.stage.draw();
                                            } else {
                                              node.fill('#fff');
                                              node.stroke('#26d35131');
                                              self.stage.draw();
                                            }
                        
                                          } */
                                    });
                                    self.current_group_id = node.getAttr("id");
                                    self.current_group_label = node.getAttr("label");
                                    var circle = self.current_proof.issues.forEach(function (item) {
                                        if (item.group == event.target.getAttr("group_id")) {
                                            self.current_issue = item;
                                        }
                                    });

                                    self.IssueDetails(self.current_group_id);
                                    self.activate_remove();
                                });
                            }
                            return node.getClassName() === "Group";
                        });

                        self.drawBoxtool();

                        self.background_group.add(self.background_image);
                        self.background.add(self.background_group);
                        self.background_image.moveToTop();
                        self.stage.add(self.background);
                        self.saveCanvasState();
                    }

                    imageObject.src = img_url;

                    // draw a rectangle to be used as the rubber area
                    /*  self.selectRect = new Konva.Rect({ x: 0, y: 0, width: 0, height: 0, stroke: '#f07f7f', strokeWidth: 4 })
                       self.selectRect.listening(false); // stop r2 catching our mouse events.
                       self.background.add(self.selectRect);
                       self.stage.draw(); */

                    /* var mode = ''; */

                    /*    self.rect.on("click tap", function () {
                         self.drawCircle();
                       }); */


                    /* self.rect.on('mousedown', function (e) {
                        mode = 'drawing';
                        self.startDrag({ x: e.evt.layerX, y: e.evt.layerY })
                      }) */

                    // update the rubber rect on mouse move - note use of 'mode' var to avoid drawing after mouse released.
                    /*  self.rect.on('mousemove', function (e) {
                         if (mode === 'drawing') {
                           self.updateDrag({ x: e.evt.layerX, y: e.evt.layerY })
                         }
                       }) */

                    /*  self.rect.on('mouseup', function (e) {
                         mode = '';
                         self.selectRect.visible(true);
                         self.stage.draw();
                       }) */
                }

                /* this.fitStageIntoParentContainer();
                window.addEventListener("resize", this.fitStageIntoParentContainer); */
            },
            /*  startDrag(posIn) {
                 this.posStart = { x: posIn.x, y: posIn.y };
                 this.posNow = { x: posIn.x, y: posIn.y };
               }, */
            /* updateDrag(posIn) {
                // update rubber rect position
                this.posNow = { x: posIn.x, y: posIn.y };
                var posRect = this.reverse(this.posStart, this.posNow);
                this.selectRect.x(posRect.x1);
                this.selectRect.y(posRect.y1);
                this.selectRect.width(posRect.x2 - posRect.x1);
                this.selectRect.height(posRect.y2 - posRect.y1);
                this.selectRect.visible(true);
                this.stage.draw(); // redraw any changes.
        
              }, */
            /*  reverse(r1, r2) {
                 var r1x = r1.x, r1y = r1.y, r2x = r2.x, r2y = r2.y, d;
                 if (r1x > r2x) {
                   d = Math.abs(r1x - r2x);
                   r1x = r2x; r2x = r1x + d;
                 }
                 if (r1y > r2y) {
                   d = Math.abs(r1y - r2y);
                   r1y = r2y; r2y = r1y + d;
                 }
                 return ({ x1: r1x, y1: r1y, x2: r2x, y2: r2y }); // return the corrected rect.     
               }, */
            /*  fitStageIntoParentContainer() {
               var container = document.querySelector("#stage-parent");
       
               // now we need to fit stage into parent
               var containerWidth = container.offsetWidth;
               // to do this we need to scale the stage
               var scale = containerWidth / this.initialStageWidth;
       
               this.stage.width(this.initialStageWidth * scale);
               this.stage.height(this.initialStageHeight * scale);
               this.stage.scale({ x: scale, y: scale });
               this.stage.draw();
             }, */
            reset_group(node) {
                if (node.getClassName() === "Circle") {
                    node.fill('#f07f7f');
                    node.stroke('#f76565')
                    this.stage.draw();
                }
            },
            resetZomm() {
                this.zoom_level = 1;
            },
            resetPosition() {
                this.background_group.x(0);
                this.background_group.y(0);
                this.resetZomm();
            },
            drawCircle() {
                if (
                    !this.pen_active ||
                    this.showIssueDialog
                ) return;

                var self = this;
                var circleOptions = {
                    fill: "#f07f7f",
                    stroke: "#F8F8FF",
                };

                if (self.current_rol == 'freelancer') {
                    circleOptions = {
                        fill: "#565b64",
                        stroke: "#F8F8FF",
                    };
                }

                var pos = {
                    x: (self.stage.getPointerPosition().x - self.background_group.getAttr('x')) / self.background_group.scaleX(),
                    y: (self.stage.getPointerPosition().y - self.background_group.getAttr('y')) / self.background_group.scaleY()
                };
                pos = self.convertPosition(pos);
                var group = new Konva.Group({
                    id: "group_" + self.group_counter,
                    label: self.group_counter,
                    counter: self.group_counter,
                    owner_type: self.current_rol,
                    x: pos.x,
                    y: pos.y,
                    draggable: true
                });

                var circle = new Konva.Circle({
                    id: "circle_" + group.getAttr("id"),
                    group_id: group.getAttr("id"),
                    counter: group.getAttr("counter"),
                    radius: 18 / self.background_group.scaleX(),
                    fill: circleOptions.fill,
                    stroke: circleOptions.stroke,
                    strokeWidth: 3 / self.background_group.scaleX()
                });
                /*
                                    circle.scaleX(1);
                                    circle.scaleY(1); */

                //Detecting browser for Issue circle options
                let textCoords = {
                    x: (group.getAttr("counter") >= 10)
                        ? 9 / self.background_group.scaleX()
                        : 5 / self.background_group.scaleX(),
                    y: 8 / self.background_group.scaleY()
                };

                // Firefox
                let isFirefox = typeof InstallTrigger !== 'undefined';

                // Safari
                let isSafari = /constructor/i.test(window.HTMLElement) || (function (p) {
                    return p.toString() === "[object SafariRemoteNotification]";
                })(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification));

                if (isFirefox) {
                    textCoords.x = (group.getAttr("counter") >= 10)
                        ? 8 / self.background_group.scaleX()
                        : 4 / self.background_group.scaleX();
                    textCoords.y = 6 / self.background_group.scaleY();
                }

                var text = new Konva.Text({
                    id: "text_" + group.getAttr("id"),
                    group_id: group.getAttr("id"),
                    counter: group.getAttr("counter"),
                    text: self.group_counter,
                    fontSize: 15 / self.background_group.scaleX(),
                    fontFamily: "Arial",
                    fill: "white",
                    offsetX: textCoords.x,
                    offsetY: textCoords.y
                });

                this.current_group_id = group.getAttr("id");
                this.current_group_label = group.getAttr("label");
                /* this.resetZomm(); */
                group.add(circle);
                group.add(text);

                this.group_counter++;

                group.on("dragend", function () {
                    self.saveCanvasState();
                });

                group.on("click tap", function (event) {
                    self.current_group_id = event.target.getAttr("group_id");
                    self.current_group_label = group.getAttr("label");

                    self.current_proof.issues.forEach(function (item) {
                        if (item.group == event.target.getAttr("group_id")) {
                            self.current_issue = item;
                        }
                    });

                    self.IssueDetails(self.current_group_id);
                    /* self.activate_remove(); */
                });

                this.background_group.add(group);
                this.background.add(this.background_group);
                this.stage.add(this.background);
                this.showIssueDialog = true;


                this.showIssueDetails = false;
                this.imageFormData.owner_type = "issue";
                this.imageFormData.issue_id = this.current_issue.id;

                Vue.nextTick(function () {
                    self.$refs.autofocus_issue_create.focus();
                });

                if ($('#main').hasClass('right-expanded')) {

                    $('#rightSidebar .toggle-button').click();
                }
            },

            drawBoxtool() {
                var self = this;
                var bg_group = self.background_group;

                var is_dragging = false;

                bg_group.on('mousedown', function (e) {
                    if (
                        !self.boxtool_active ||
                        e.target.getClassName() != 'Image' ||
                        self.showIssueDialog
                    ) return;

                    this.draggable(false);
                    is_dragging = true;

                    startDrag(getMousePos(e));
                });

                bg_group.on('mousemove', function (e) {
                    if (!is_dragging) return;

                    updateDrag(getMousePos(e));
                });

                bg_group.on('mouseup', function (e) {
                    if (!is_dragging) return;

                    this.draggable(true);
                    is_dragging = false;

                    saveDrag();

                    if ($('#main').hasClass('right-expanded')) {

                        $('#rightSidebar .toggle-button').click();
                    }
                });

                function startDrag(pos) {
                    var scale = bg_group.scaleX();

                    bt_rect.setAttrs({
                        x: pos.x,
                        y: pos.y,
                        strokeWidth: 3 / scale
                    });

                    bt_outline.setAttrs({
                        x: pos.x,
                        y: pos.y,
                        strokeWidth: 6 / scale
                    });

                    bt_circle.setAttrs({
                        x: pos.x,
                        y: pos.y,
                        radius: 18 / scale,
                        strokeWidth: 3 / scale
                    });

                    bt_text.setAttrs({
                        x: pos.x,
                        y: pos.y,
                        offsetX: 6 / scale,
                        offsetY: 6 / scale,
                        fontSize: 16 / scale,
                        text: self.group_counter
                    });

                    bt_check.setAttrs({
                        x: pos.x + bt_rect.width() / 2,
                        y: pos.y + bt_rect.height() / 2,
                        width: 24 / scale,
                        height: 24 / scale,
                        offsetX: 12 / scale,
                        offsetY: 12 / scale,
                    });

                    initAnchors(pos);

                    bg_group.add(bt_group);
                }

                function updateDrag(pos) {

                    bt_rect.width(pos.x - bt_rect.x());
                    bt_rect.height(pos.y - bt_rect.y());

                    bt_outline.width(pos.x - bt_outline.x());
                    bt_outline.height(pos.y - bt_outline.y());

                    bt_check.setAttrs({
                        x: bt_rect.x() + bt_rect.width() / 2,
                        y: bt_rect.y() + bt_rect.height() / 2
                    });

                    updateAnchors(bt_rect);

                    bg_group.draw();
                }

                function saveDrag() {
                    var group = bt_group.clone();

                    setGroupID(group);
                    bg_group.add(group);

                    bt_rect.width(0);
                    bt_rect.height(0);

                    bt_outline.width(0);
                    bt_outline.height(0);

                    bt_group.remove();

                    group.on('dragend', function (e) {
                        self.saveCanvasState();
                    });

                    group.on("click tap", function (event) {
                        self.current_group_id = event.target.getAttr("group_id");
                        self.current_group_label = group.getAttr("label");

                        self.current_proof.issues.forEach(function (item) {
                            if (item.group == event.target.getAttr("group_id")) {
                                self.current_issue = item;
                            }
                        });

                        self.IssueDetails(self.current_group_id);
                    });

                    dragAnchors(group);

                    bg_group.draw();
                }

                function getMousePos(e) {
                    var pos, scale = bg_group.scaleX();

                    if (e.target.getClassName() == 'Circle') {

                        pos = e.target.position();
                    } else {

                        pos = {
                            x: (e.evt.layerX - bg_group.x()) / scale,
                            y: (e.evt.layerY - bg_group.y()) / scale
                        };

                        pos = self.convertPosition(pos);
                    }

                    return pos;
                }

                var colors = {
                    fill: self.current_rol == 'freelancer' ? '#565B64' : '#F07F7F',
                    stroke: '#F8F8FF'
                };

                var bt_group = new Konva.Group({
                    name: 'bt-group',
                    draggable: true
                });

                var bt_outline = new Konva.Rect({
                    name: 'bt-outline',
                    stroke: colors.stroke
                });
                bt_group.add(bt_outline);

                var bt_rect = new Konva.Rect({
                    name: 'bt-rect',
                    stroke: colors.fill
                });
                bt_group.add(bt_rect);

                var anchors = [];
                for (var i = 0; i < 3; i++) {
                    var anchor = new Konva.Circle({
                        name: 'bt-anchor',
                        fill: colors.stroke,
                        stroke: colors.fill,
                        draggable: true
                    });

                    anchors[i] = anchor;
                    bt_group.add(anchors[i]);
                }

                var bt_circle = new Konva.Circle({
                    name: 'bt-circle',
                    fill: colors.fill,
                    stroke: colors.stroke
                });
                bt_group.add(bt_circle);

                var bt_text = new Konva.Text({
                    name: 'bt-text',
                    fill: colors.stroke
                });
                bt_group.add(bt_text);

                var bt_check;
                var imageObj = new Image();
                imageObj.onload = function () {

                    bt_check = new Konva.Image({
                        name: 'bt-check',
                        image: imageObj,
                        visible: false
                    });
                    bt_group.add(bt_check);
                }
                imageObj.src = '/img/Checkbox.png';

                function initAnchors(pos) {
                    var scale = bg_group.scaleX();

                    anchors.forEach(function (item) {
                        item.setAttrs({
                            x: pos.x,
                            y: pos.y,
                            radius: 6 / scale,
                            strokeWidth: 2 / scale
                        });
                    });
                }

                function updateAnchors(rect) {

                    anchors[0].x(rect.x() + rect.width());

                    anchors[1].setAttrs({
                        x: rect.x() + rect.width(),
                        y: rect.y() + rect.height()
                    });

                    anchors[2].y(rect.y() + rect.height());
                }

                function dragAnchors(group) {
                    var circles = group.get('Circle');

                    var rect = group.get('Rect')[1];
                    var outline = group.get('Rect')[0];
                    var text = group.get('Text')[0];
                    var check = group.get('Image')[0];

                    circles.forEach((circle, index) => {
                        var indexes = [
                            (index + 2) % 4,
                            (index % 2) ? (index + 3) % 4 : (index + 1) % 4,
                            (index % 2) ? (index + 1) % 4 : (index + 3) % 4,
                        ];

                        circle.on('dragmove', function (e) {
                            var pos = circles[indexes[0]].position();

                            rect.position(pos);
                            rect.width(getMousePos(e).x - pos.x);
                            rect.height(getMousePos(e).y - pos.y);

                            outline.position(pos);
                            outline.width(getMousePos(e).x - pos.x);
                            outline.height(getMousePos(e).y - pos.y);

                            check.position({
                                x: pos.x + rect.width() / 2,
                                y: pos.y + rect.height() / 2
                            });

                            circles[indexes[1]].setX(getMousePos(e).x);
                            circles[indexes[2]].setY(getMousePos(e).y);

                            if (index == 0) {
                                text.y(circles[indexes[2]].y());
                            } else if (index == 2) {
                                text.x(circles[indexes[1]].x());
                            }
                        });
                    });
                }

                bg_group.getChildren(function (node) {
                    if (node.name() == 'bt-group') {
                        dragAnchors(node);
                    }
                });

                function setGroupID(group) {
                    group.setAttrs({
                        id: "group_" + self.group_counter,
                        label: self.group_counter,
                        counter: self.group_counter,
                        owner_type: self.current_rol
                    });

                    group.getChildren(function (node) {
                        node.setAttrs({
                            group_id: group.getAttr("id"),
                            counter: group.getAttr("counter")
                        });
                    });

                    self.current_group_id = group.getAttr("id");
                    self.current_group_label = group.getAttr("label");

                    self.group_counter++;
                    self.showIssueDialog = true;

                    Vue.nextTick(function () {
                        self.$refs.autofocus_issue_create.focus();
                    });
                }
            },

            adjustScaleOfTools(group) {
                var scale = this.background_group.scaleX();

                group.getChildren(function (node) {

                    if (group.name() == 'bt-group') {

                        switch (node.name()) {
                            case 'bt-rect':
                                node.setAttrs({
                                    strokeWidth: 3 / scale
                                });
                                break;
                            case 'bt-outline':
                                node.setAttrs({
                                    strokeWidth: 6 / scale
                                });
                                break;
                            case 'bt-circle':
                                node.setAttrs({
                                    radius: 18 / scale,
                                    strokeWidth: 3 / scale
                                });
                                break;
                            case 'bt-text':
                                node.setAttrs({
                                    offsetX: 6 / scale,
                                    offsetY: 6 / scale,
                                    fontSize: 16 / scale
                                });
                                break;
                            case 'bt-anchor':
                                node.setAttrs({
                                    radius: 6 / scale,
                                    strokeWidth: 2 / scale
                                });
                                break;
                            case 'bt-check':
                                node.setAttrs({
                                    width: 24 / scale,
                                    height: 24 / scale,
                                    offsetX: 12 / scale,
                                    offsetY: 12 / scale,
                                });
                                break;
                        }
                    } else {

                        if (node.getClassName() == 'Circle') {
                            node.radius(18 / scale);
                            node.strokeWidth(3 / scale);
                            // node.scaleX(scale);
                            // node.scaleY(scale);
                        }
                        if (node.getClassName() == 'Text') {
                            //Detecting browser for Issue circle options
                            let textCoords = {
                                x: (node.getAttr("counter") >= 10)
                                    ? 9 / scale
                                    : 5 / scale,
                                y: 8 / scale
                            };

                            // Firefox
                            let isFirefox = typeof InstallTrigger !== 'undefined';

                            // Safari
                            let isSafari = /constructor/i.test(window.HTMLElement) || (function (p) {
                                return p.toString() === "[object SafariRemoteNotification]";
                            })(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification));
                            if (isFirefox) {
                                textCoords.x = (node.getAttr("counter") >= 10)
                                    ? 8 / scale
                                    : 4 / scale;
                                textCoords.y = 6 / scale;
                            }
                            node.fontSize(15 / scale);
                            node.offsetX(textCoords.x);
                            node.offsetY(textCoords.y);
                            // node.scaleX(scale);
                            // node.scaleY(scale);
                        }
                    }
                });
            },

            adjustPosOfTools(ratio) {
                var self = this;

                self.background_group.getChildren(function (node) {
                    if (node.getClassName() != 'Group') return;

                    node.position({
                        x: node.x() * ratio,
                        y: node.y() * ratio
                    });

                    if (node.name() == 'bt-group') {
                        node.getChildren(function (shape) {

                            shape.position({
                                x: shape.x() * ratio,
                                y: shape.y() * ratio
                            });

                            if (shape.name() == 'bt-rect' || shape.name() == 'bt-outline') {
                                shape.width(shape.width() * ratio);
                                shape.height(shape.height() * ratio);
                            }
                        });
                    }
                });
            },

            rotateCanvas() {
                var self = this;

                var bg_group = self.background_group;

                if (bg_group.rotation() == 270) bg_group.rotation(0);
                else bg_group.rotate(90);

                self.fitToScreen();
            },

            fitToScreen(option) {
                var self = this;

                var bg_group = self.background_group;
                var bg_image = self.background_image;

                var scale = bg_group.scaleX();

                var width = bg_image.width() * scale;
                var height = bg_image.height() * scale;

                if (bg_group.rotation() == 90 || bg_group.rotation() == 270) {
                    var temp = width;

                    width = height;
                    height = temp;
                }

                var ratio = (option == 'width') ? self.initialStageWidth / width : Math.min(
                    self.initialStageWidth / width,
                    self.initialStageHeight / height
                );

                self.adjustRatio(ratio);
            },

            adjustRatio(ratio) {
                var self = this;

                var bg_group = self.background_group;
                var bg_image = self.background_image;

                var scale = bg_group.scaleX();

                bg_image.width(bg_image.width() * ratio);
                bg_image.height(bg_image.height() * ratio);

                self.adjustPosOfTools(ratio);

                var lengths = self.convertLengths(bg_image.width(), bg_image.height());

                bg_group.position({
                    x: (self.initialStageWidth - lengths.width * scale) / 2,
                    y: (self.initialStageHeight - lengths.height * scale) / 2
                });

                self.background.draw();
            },

            convertLengths(width, height) {
                switch (this.background_group.rotation()) {
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

            convertPosition(pos) {
                var bg_group = this.background_group;
                switch (bg_group.rotation()) {
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

            loadThumbFromVideo(index) {
                console.log(index);
                var self = this;
                if (self.project_type == 'video') {
                    this.switchPlayResumevideoImage('yes');
                    document.getElementById('VideoProofloUrlStreaming').style.display = "none";
                    this.$refs.buttonsBarPanel.style.display = "block";
                    this.$refs.isNotVideo.setAttribute("style", "visibility: visible; position: relative; z-index: 99");
                    this.$refs.thumbTovideoBtn.classList.add("without-pink");
                    this.$refs.issueListing.style.display = "block";
                    this.$refs.proofloTakeScreenshotFromVideoRef.style.display = "none";
                }
            },
            thumbTovideo() {
                var self = this;
                document.getElementById('VideoProofloUrlStreaming').style.display = "block";
                this.$refs.buttonsBarPanel.style.display = "none";
                this.$refs.isNotVideo.setAttribute("style", "visibility: hidden; position: absolute; z-index: 99");
                this.$refs.issueListing.style.display = "none";
                this.$refs.proofloTakeScreenshotFromVideoRef.style.display = "flex";

                this.switchPlayResumevideoImage();
            },
            loadNewCaptureImage() {
                let project_id = this.project_id;
                let revision_id = this.revision_id;

                var self = this;
                self.proofs = [];
                self.fullscreenLoading = true;
                axios
                    .get("/api/projects/load/" + project_id + '/' + revision_id)
                    .then(response => {
                        if (response.data.status == 1) {

                            self.websiteURI = response.data.data.websiteURI;
                            self.project_video_url = response.data.data.project_video_url;
                            self.project_capture_time = response.data.data.project_capture_time;

                            self.project_status = response.data.data.project_status;
                            self.project_type = response.data.data.project_type;
                            self.versions = response.data.data.versions;
                            self.current_version_number = response.data.data.last_revision.version;
                            self.value = 'Revision Round ' + response.data.data.last_revision.version;
                            self.current_review_id = response.data.data.last_revision.id;
                            self.last_version_number = response.data.data.last_revision.id;
                            self.revision_status = response.data.data.last_revision.status_revision;
                            self.total_issues = response.data.data.total_issues;
                            self.solved_issues = response.data.data.solved_issues;
                            self.percentage = response.data.data.percentage;

                            self.approveButton = (self.project_type == 'design')
                                ? 'APPROVE DESIGN'
                                : 'APPROVE WEBSITE';
                            if (response.data.data.last_revision.proofs.length > 0) {
                                response.data.data.last_revision.proofs.forEach(function (element, index) {
                                    var proof = {
                                        id: element.id,
                                        revision_id: element.revision_id,
                                        project_id: response.data.data.last_revision.project_id,
                                        active_thumb: false,
                                        picture_url: "/storage/" + element.project_files.path,
                                        thumb_url: "/storage/" + element.project_files.path,
                                        canvas_data: element.canvas_data,
                                        issues: element.issues,
                                        user_id: element.project_files.user_id,
                                        status: element.status,
                                        capture_time: element.project_files.capture_time
                                    };

                                    proof.issues.forEach(function (issue) {

                                        issue.show_issues = true;
                                        issue.show_issues_controls = false;
                                        issue.show_issues_del_conf = false;
                                        issue.show_issues_del_img_conf = false;
                                        issue.show_issues_attachment = false;
                                        issue.show_issues_attachment_uploader = false;

                                        issue.show_issue = true;
                                        issue.show_issue_controls = false;
                                        issue.show_issue_del_conf = false;
                                        issue.show_issue_del_img_conf = false;
                                        issue.show_issue_attachment = false;
                                        issue.show_issue_attachment_uploader = false;

                                        issue.comments.forEach(function (comment) {
                                            comment.show_comment = true;
                                            comment.show_attachment = false;
                                            comment.show_attachment_uploader = false;
                                            comment.show_comment_del_conf = false;
                                            comment.show_attachment_del_conf = false;
                                        });
                                    });

                                    self.proofs.push(proof);
                                });
                                console.log(self.proofs);
                                self.fullscreenLoading = false;
                                if (self.proofs.length > 0) {
                                    self.loadThumb(0);
                                    // self.pen_active = true;
                                    let location = window.location.href;
                                    if (location.indexOf('#') != -1) {
                                        let issue = location.substring(location.indexOf('#') + 1, location.indexOf('-'));
                                        let comment = '#comment_' + location.substring(location.indexOf('-') + 1);
                                        self.IssueDetails(issue);
                                        self.scrollToComment(comment);
                                    }
                                }
                            } else {
                                self.fullscreenLoading = false;
                            }
                        } else {
                            this.handle_error(response.data.errors);
                        }
                    })
                    .catch(error => {
                        self.sendLoading = false;
                    });


            },
            loadThumb(index) {
                var self = this;

                this.slide_page_number = index + 1;
                this.showSubmitButtons = true;
                self.current_proof = self.proofs[index];

                if (this.showIssueDialog == true) {
                    this.cancelIssue();
                }

                if (self.current_proof.issues) {
                    if (self.current_proof.issues.length > 0) {
                        self.current_proof.issues.sort(function (a, b) {
                            return b.label - a.label;
                        });
                    }
                }
                var img_url = self.current_proof.picture_url;
                self.current_proof.active_thumb = true;
                for (var i in self.proofs) {
                    if (i == index) {
                        self.proofs[i].active_thumb = true;
                    } else {
                        self.proofs[i].active_thumb = false;
                    }
                }
                self.resetZomm();
                self.initializeStage(img_url);
            },
            saveCanvasState() {
                var self = this;
                if (self.current_proof) {
                    var formData = {
                        revision_id: self.current_proof.revision_id,
                        id: self.current_proof.id,
                        canvas_data: self.stage.toJSON()
                    };
                    axios
                        .post("/api/proof/save", formData)
                        .then(response => {
                            if (response.data.status == 1) {
                                for (var i in self.proofs) {
                                    if (self.proofs[i].id == self.current_proof.id) {
                                        var proof = self.proofs[i];
                                        proof.canvas_data = self.stage.toJSON();
                                        self.proofs.splice(
                                            self.proofs.indexOf(self.current_proof),
                                            1,
                                            proof
                                        );
                                    }
                                }

                                self.fullscreenLoading = false;
                                self.$store.commit("SAVE_CURRENT_PROOF_DATA", self.proofs);
                            } else {
                                self.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            self.handle_error({});
                        });
                }
            },

            deleteIssue(issue_id = null) {
                var self = this;
                issue_id = (issue_id) ? issue_id : self.current_issue.id;
                if (issue_id > 0) {
                    axios
                        .delete("/api/proof/delete_issue/" + issue_id)
                        .then(response => {
                            if (response.data.status == 1) {
                                self.current_proof.issues.forEach(function (item, index) {
                                    if (item.id == issue_id) {
                                        var removed = self.current_proof.issues.splice(index, 1);
                                        if (removed) {
                                            var group = self.background.find(
                                                "#" + self.current_group_id
                                            );
                                            group.remove();
                                            self.background.draw();
                                            self.saveCanvasState();
                                            self.showIssueDialog = false;
                                            self.activate_pen();
                                            self.issuesList();
                                            self.resetIssue();
                                        }
                                    }
                                });
                            } else {
                                this.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            this.handle_error({});
                        });
                }
                // self.showIssueDialog = false;
                // swal({
                //     title: "Are you sure?",
                //     text: "You won't be able to revert this!",
                //     type: "warning",
                //     showCancelButton: true,
                //     confirmButtonColor: "#3085d6",
                //     cancelButtonColor: "#d33",
                //     confirmButtonText: "Yes, delete it!"
                // }).then(result => {
                //     if (result.value) {
                //         axios
                //             .delete("/api/proof/delete_issue/" + issue_id)
                //             .then(response => {
                //                 if (response.data.status == 1) {
                //                     self.current_proof.issues.forEach(function (item, index) {
                //                         if (item.id == issue_id) {
                //                             var removed = self.current_proof.issues.splice(index, 1);
                //                             if (removed) {
                //                                 var group = self.background.find(
                //                                     "#" + self.current_group_id
                //                                 );
                //                                 group.remove();
                //                                 self.background.draw();
                //                                 self.saveCanvasState();
                //                                 self.showIssueDialog = false;
                //                                 self.activate_pen();
                //                                 self.issuesList();
                //                                 self.resetIssue();
                //                             }
                //                         }
                //                     });
                //                 } else {
                //                     this.handle_error(response.data.errors);
                //                 }
                //             })
                //             .catch(error => {
                //                 this.handle_error({});
                //             });
                //     }
                // });
            },
            deleteIssueById(issue_id) {
            },
            openUploadSlideDialog() {
                this.showSlideUploadDialog = true;
            },
            openNewRevisionDialog() {
                var formData = {
                    project_id: this.project_id,
                }
                this.fullscreenLoading = true;
                axios
                    .post("/api/revisions/create", formData)
                    .then(response => {
                        if (response.data.status == 1) {
                            this.fullscreenLoading = false;
                            this.showNewRevisionDialog = true;
                        } else {
                            this.handle_error(response.data.errors);
                        }
                    })
                    .catch(error => {
                        this.handle_error({});
                    });
            },
            openFinalFilesDialog() {
                this.showFinalFilesDialog = true;
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
            openNewCollaboratorDialog() {
                this.showNewCollaboratorDialog = true;
            },
            switchPlayResumevideoImage(pausedOnly = '') {
                var a = document.getElementById('VideoProofloUrlStreaming');
                if (a.paused && pausedOnly != 'yes') {
                    this.$refs.playVideoImage.style.display = "none";
                    this.$refs.pauseVideoImage.style.display = "block";

                    this.$refs.playThumbTovideoBtn.style.display = "none";
                    this.$refs.pauseThumbTovideoBtn.style.display = "block";

                    a.play();
                } else {
                    this.$refs.playVideoImage.style.display = "block";
                    this.$refs.pauseVideoImage.style.display = "none";

                    this.$refs.playThumbTovideoBtn.style.display = "block";
                    this.$refs.pauseThumbTovideoBtn.style.display = "none";
                    a.pause();
                }
            },
            editIssue(issue_id) {
                var self = this;
                this.current_proof.issues.forEach(function (item, index) {
                    if (item.id == issue_id) {
                        self.editable_issue.id = item.id;
                        self.editable_issue.description = item.description;
                        self.editable_issue.project_files_id = item.project_files_id;
                        self.showEditableIssueDialog = true;
                    }
                });
            },
            editComment(issue_id, comment_id) {
                var self = this;
                this.current_proof.issues.forEach(function (item, index_i) {
                    if (item.id == issue_id) {
                        item.comments.forEach(function (comment, index_j) {
                            if (comment.id == comment_id) {
                                self.editable_comment.id = comment.id;
                                self.editable_comment.issue_id = comment.issue_id;
                                self.editable_comment.description = comment.description;
                                self.showEditableCommentDialog = true;
                            }
                        });
                    }
                });
            },
            issueAttachmentAdded() {
                this.issue_have_attachment = true;
            },
            issueAttachmentRemoved() {
                this.issue_have_attachment = false;
            },
            addIssue() {
                this.reset_errors();
                if (this.new_issue.description.trim() != '') {
                    var self = this;
                    var formData = {
                        proof_id: this.current_proof.id,
                        description: this.new_issue.description,
                        group: this.current_group_id,
                        label: this.current_group_label,
                        owner_type: this.current_rol,
                        project_type: this.project_type,
                    };
                    if (this.new_issue.id > 0) {
                        formData.id = this.new_issue.id;
                    }
                    this.fullscreenLoading = true;

                    axios
                        .post("/api/proof/create_issue", formData)
                        .then(response => {
                            if (response.data.status == 1) {
                                let new_issue = response.data.data[0];
                                var issue = {
                                    id: new_issue.id,
                                    proof_id: new_issue.proof_id,
                                    comments: new_issue.comments,
                                    status: new_issue.status,
                                    user_id: new_issue.user_id,
                                    user: new_issue.user,
                                    owner_type: new_issue.owner_type,
                                    description: new_issue.description,
                                    group: this.current_group_id,
                                    label: this.current_group_label,
                                    active_issue: true,

                                    show_issues: true,
                                    show_issues_controls: false,
                                    show_issues_del_conf: false,
                                    show_issues_del_img_conf: false,
                                    show_issues_attachment: false,
                                    show_issues_attachment_uploader: false,

                                    show_issue: true,
                                    show_issue_controls: false,
                                    show_issue_del_conf: false,
                                    show_issue_del_img_conf: false,
                                    show_issue_attachment: false,
                                    show_issue_attachment_uploader: false
                                };
                                this.editable_issue.id = issue.id;
                                this.imageFormData.issue_id = issue.id;
                                this.fullscreenLoading = false;
                                self.current_proof.status = 'issue';
                                if (this.new_issue.id > 0) {
                                    if (this.current_proof.issues) {
                                        if (this.current_proof.issues.length > 0) {
                                            this.current_proof.issues.forEach(function (item, index) {
                                                if (item.id == self.current_issue.id) {
                                                    self.current_proof.issues.splice(index, 1, issue);
                                                } else {
                                                    this.current_proof.issues.push(issue);
                                                }
                                            });
                                        }
                                    }
                                } else {
                                    if (this.current_proof.issues) {
                                        if (this.current_proof.issues.length > 0) {
                                            this.current_proof.issues.push(issue);
                                            this.current_proof.issues.sort(function (a, b) {
                                                return b.label - a.label;
                                            });
                                        }
                                        else {
                                            this.current_proof.issues.push(issue);
                                        }
                                    } else {
                                        this.current_proof.issues = [];
                                        this.current_proof.issues.push(issue);
                                    }
                                }

                                this.activate_issue(this.new_issue.id);
                                this.saveCanvasState();
                                this.issuesList();
                                this.resetNewIssue();
                                this.updateProgress('increase_total');
                                this.showIssueDialog = false;
                                this.isActiveIssue = false;
                                if (this.issue_have_attachment) {
                                    this.$refs.issue_attachment.submit();
                                }

                                this.addIssueIcons = '/img/send-grey.png';
                                this.$store.commit("SAVE_CURRENT_PROOF_DATA", this.proofs);

                            } else {
                                this.fullscreenLoading = false;
                                this.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            this.fullscreenLoading = false;
                            this.handle_error({});
                        });
                } else {
                    if (this.new_issue.description == '') {
                        this.errors.issue_description = true;
                        this.errors_msg.issue_description = 'Issue description is required';
                    }
                }
            },
            syncIssueDesc(issue_id, single_issue) {
                var content = (single_issue == false) ? this.$refs['issues_content' + issue_id][0].innerText : this.$refs.issue_content.innerText;
                this.current_proof.issues.forEach(function (item, index) {
                    if (item.id == issue_id) {
                        item.description = content;
                    }
                });
            },
            updateIssue(issue_id, e) {
                var content = e.target.innerText;
                content = $.trim(content);

                if (content != '') {
                    var formData = {
                        id: issue_id,
                        description: content
                    };

                    axios
                        .post("/api/proof/create_issue", formData)
                        .then(response => {
                            // if(response.data.status == 1)
                            // {
                            //   this.current_proof.issues.forEach(function (item, index) {
                            //       if (item.id == issue_id) {
                            //           item.description = content;
                            //       }
                            //   });
                            // }
                        })
                        .catch(error => {
                            //
                            self.handle_error({});
                        });
                }

            },

            updateComment() {
                this.reset_errors();
                if (this.editable_comment.description != '') {
                    var self = this;
                    var formData = {
                        id: this.editable_comment.id,
                        issue_id: this.editable_comment.issue_id,
                        description: this.editable_comment.description,
                        revision_id: this.revision_id,
                        project_id: this.project_id
                    };
                    this.fullscreenLoading = true;
                    axios
                        .post("/api/proof/add_comment", formData)
                        .then(response => {
                            if (response.data.status == 1) {
                                let updated_comment = response.data.data[0];
                                var comment = {
                                    id: updated_comment.id,
                                    issue_id: updated_comment.issue_id,
                                    project_files_id: updated_comment.project_files_id,
                                    user: updated_comment.user,
                                    user_id: updated_comment.user_id,
                                    description: updated_comment.description,
                                    project_file_id: updated_comment.project_file_id,
                                    project_files: updated_comment.project_files,
                                    show_comment: true,
                                    show_attachment: false,
                                    show_attachment_uploader: false,
                                    show_comment_del_conf: false,
                                    show_attachment_del_conf: false
                                };
                                self.fullscreenLoading = false;
                                self.current_proof.issues.forEach(function (item, index) {
                                    if (item.id == self.editable_comment.issue_id) {
                                        item.comments.forEach(function (_comment, index_j) {
                                            if (_comment.id == self.editable_comment.id) {
                                                item.comments.splice(index_j, 1, comment);
                                            }
                                        });
                                    }
                                });

                                self.resetEditableComment();
                                self.showEditableCommentDialog = false;
                            } else {
                                self.fullscreenLoading = false;
                                self.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            self.fullscreenLoading = false;
                            self.handle_error({});
                        });
                } else {
                    if (this.editable_comment.description == '') {
                        this.errors.editable_comment_description = true;
                        this.errors_msg.editable_comment_description = 'Comment description is required';
                    }

                }

            },

            addComment(issue_id) {
                this.reset_errors();
                if (this.current_comment.description != '') {
                    var self = this;

                    var formData = {
                        project_id: this.project_id,
                        revision_id: this.revision_id,
                        issue_id: issue_id,
                        description: self.current_comment.description
                    };
                    self.fullscreenLoading = true;

                    axios
                        .post("/api/proof/add_comment", formData)
                        .then(response => {
                            if (response.data.status == 1) {
                                let new_comment = response.data.data[0];
                                var comment = {
                                    id: new_comment.id,
                                    issue_id: new_comment.issue_id,
                                    user: new_comment.user,
                                    user_id: new_comment.user_id,
                                    description: new_comment.description,
                                    created_at: new_comment.created_at,
                                    show_comment: true,
                                    show_attachment: false,
                                    show_attachment_uploader: false,
                                    show_comment_del_conf: false,
                                    show_attachment_del_conf: false,
                                };

                                if (this.comment_have_attachment === true) {
                                    self.imageFormData.owner_type = "comment";
                                    self.imageFormData.comment_id = comment.id;
                                    self.$refs.upload_comment_attachment.submit();
                                }

                                self.fullscreenLoading = false;

                                if (self.current_proof.issues.length > 0) {
                                    self.current_proof.issues.forEach(function (issue_, index) {
                                        if (issue_.id == issue_id) {
                                            if (issue_.comments) {
                                                issue_.comments.push(comment);
                                                issue_.comments.sort(function (a, b) {
                                                    return b.id - a.id;
                                                });
                                            } else {
                                                issue_.comments = [];
                                                issue_.comments.push(comment);
                                            }
                                        }
                                    });
                                }

                                self.resetComment();
                            } else {
                                self.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            self.handle_error({});
                        });
                } else {
                    this.errors.comment_description = true;
                    this.errors_msg.comment_description = 'Comment description is required';
                }
            },
            commentAttachmentAdded() {
                this.comment_have_attachment = true;
            },
            commentAttachmentRemoved() {
                this.comment_have_attachment = false;
            },
            commentAttachmentUploaded(response, file, fileList) {
                var self = this;
                var comment_id = self.imageFormData.comment_id;
                var issue_id = self.current_issue.id;
                var file_id = response.data.id;
                var file_path = response.data.path;
                var file_thumb = response.data.thumb;
                self.issue_datils.comments.forEach(function (comment_, i) {
                    if (comment_.id == comment_id) {
                        var desc = comment_.description;
                        comment_.description = '*';
                        comment_.project_files_id = file_id;
                        comment_.project_files = {
                            path: file_path,
                            thumb_path: file_thumb,
                            id: file_id
                        };
                        comment_.description = desc;
                    }
                });
                self.$refs.upload_comment_attachment.clearFiles();
            },
            loadIssue() {
                for (var i in this.current_proof.issues) {
                    if (this.current_proof.issues[i].id == this.current_group_id) {
                        this.current_issue.description = this.current_proof.issues[
                            i
                            ].description;
                    }
                }
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
            deletePicture(id) {
                var self = this;
                self.fullscreenLoading = true;
                axios
                    .delete("/api/files/delete/" + id)
                    .then(response => {

                        if (response.data.status == 1) {
                            self.fullscreenLoading = false;
                            self.showIssueImageFlag = false;
                            if (response.data.data == "Issue") {

                                if (self.current_proof.issues.length > 0) {
                                    self.current_proof.issues.forEach(function (item, index) {
                                        if (item.project_files_id == id) {

                                            item.project_files_id = "";
                                        }
                                    });
                                }
                            }
                            if (response.data.data == "Comment") {
                                if (self.current_proof.issues.length > 0) {
                                    for (var i = 0; i < self.current_proof.issues.length; i++) {
                                        for (
                                            var j = 0;
                                            j < self.current_proof.issues[i].comments.length;
                                            j++
                                        ) {
                                            if (
                                                self.current_proof.issues[i].comments[j]
                                                    .project_files_id == id
                                            ) {
                                                self.current_proof.issues[i].comments[
                                                    j
                                                    ].project_files_id = "";
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            self.fullscreenLoading = false;
                            self.showIssueImageFlag = false;
                            self.handle_error(response.data.errors);
                        }
                    })
                    .catch(error => {
                        self.fullscreenLoading = false;
                        self.showIssueImageFlag = false;
                        self.handle_error({});
                    });
            },
            showIssueImage(issue) {
                this.current_issue = issue;
                this.image_preview = "/storage/" + issue.project_files.path;
                this.image_thumb = issue.project_files.thumb_path;
                this.image_id = issue.project_files.id;
                this.showIssueImageFlag = true;
            },
            showSubmitOptionsModal() {
                this.showSubmitOptions = true;
            },
            showApproveDesignModal() {
                this.showApproveDesign = true;
            },
            activate_pen() {
                this.pen_active = !this.pen_active;
                this.boxtool_active = false;
                this.zoom_in_active = false;
                this.zoom_out_active = false;
                this.remove_active = false;
                this.disabled_remove = true;
                this.penInfoVisible = false;
                if (this.pen_active) {
                    this.$el.querySelector('#stage').style.cursor = 'url("/img/add-comment.png") 20 60, auto';
                } else {
                    this.$el.querySelector('#stage').style.cursor = 'initial';
                }
            },
            activate_boxtool() {
                this.boxtool_active = !this.boxtool_active;
                this.pen_active = false;
                this.zoom_in_active = false;
                this.zoom_out_active = false;
                this.remove_active = false;
                this.disabled_remove = true;
                this.penInfoVisible = false;
                this.$el.querySelector('#stage').style.cursor = 'initial';
            },
            deactivate_zoom_in() {
                this.zoom_out_active = false;
            },
            deactivate_zoom_out() {
                this.zoom_in_active = false;
            },
            activate_zoom_in() {

                if (this.zoom_level <= 1) {
                    this.zoom_level -= 0.25;
                } else if (this.zoom_level <= 3) {
                    this.zoom_level -= 0.5;
                } else {
                    this.zoom_level -= 1;
                }
                this.zoom_out_active = true;
                /*this.pen_active = false;
                this.zoom_out_active = false;
                this.remove_active = false;
                this.disabled_remove = true; */
            },
            activate_zoom_out() {

                if (this.zoom_level < 1) {
                    this.zoom_level += 0.25;
                } else if (this.zoom_level < 3) {
                    this.zoom_level += 0.5;
                } else {
                    this.zoom_level += 1;
                }
                this.zoom_in_active = true;
                /*this.pen_active = false;
                this.zoom_in_active = false;
                this.disabled_remove = true;
                this.remove_active = false; */
            },
            activate_remove() {
                this.remove_active = true;
                this.disabled_remove = false;
                this.pen_active = false;
                this.zoom_in_active = false;
                this.zoom_out_active = false;
            },
            disable_buttons() {
                this.remove_active = false;
                this.disabled_remove = true;
                this.pen_active = false;
                this.zoom_in_active = false;
                this.zoom_out_active = false;
            },
            reset_errors() {
                this.errors.comment_description = false;
                this.errors.issue_description = false;
                this.errors.editable_issue_description = false;
                this.errors.editable_comment_description = false;
            },
            goBack() {
                /* this.$router.push({ name: 'projects_list' }); */
                window.location.href = "/projects";
            },
            approveProject() {
                if (this.revision_status == 'approved') {
                    return false;
                }
                var self = this;
                if (this.decision != '') {
                    var formData = {
                        project_id: self.project_id,
                        revision_id: self.current_proof.revision_id,
                        decision: this.decision
                    };
                    self.fullscreenLoading = true;
                    axios
                        .post("/api/projects/approve_project", formData)
                        .then(response => {
                            self.fullscreenLoading = false
                            if (response.data.status == 1) {
                                self.showApproveDesign = false;
                                self.revision_status = 'approved';
                                for (var i in self.current_proof.issues) {
                                    self.current_proof.issues[i].status = 'solved';
                                }
                                swal({
                                    title: '',
                                    text: 'The decision has been sent by email successfully',
                                    type: 'success'
                                })
                                // .then((result) => {
                                //     if (result.value) {
                                //         window.location.href = "/projects"
                                //     }
                                // });
                                self.approveStatus = true;
                            } else {
                                self.showApproveDesign = false;
                                this.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            self.showApproveDesign = false;
                            self.handle_error({});
                        });
                    /* if (_decision == 'approved') {
                        var msg_text = 'You are about to request a new revision. This means all the issues in the current one have been solved and you are ready to receive new revisions.'

                    }
                    if (_decision == 'finished') {
                        var msg_text = 'You are about to set this project as approved. This means all the issues has been resolved and you agree with final results.'
                    }
                    swal({
                        title: "Decision submit note",
                        text: msg_text,
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, send it!"
                    }).then(result => {
                        if (result.value) {
                            
                        }
                    }); */
                }
            },
            sendProjectSlides() {
                var self = this;
                this.fullscreenLoading = true;
                if (this.savedFiles.length > 0) {
                    this.fullscreenLoading = false;
                    this.showSlideUploadDialog = false;
                    this.savedFiles = [];
                    this.proofs = [];
                    this.loadInitialRevision(this.project_id, this.revision_id);
                } else {
                    toastr['error']('You should upload some proofs', 'Error');
                    this.fullscreenLoading = false;
                }
            },
            sendNewRevision() {
                this.fullscreenLoading = true;
                if (this.savedFiles.length > 0) {
                    axios
                        .get("/api/projects/send_project/" + this.project_id + '/' + this.current_rol)
                        .then(response => {
                            if (response.data.status == 1) {
                                var revision_id = response.data.data[0].id;
                                this.fullscreenLoading = false;
                                this.showNewRevisionDialog = false;
                                this.uploadProofsStatus = true;
                                this.savedFiles = [];
                                if (response.data.status == 1) {
                                    this.showSubmitOptions = false;
                                    swal(
                                        '',
                                        'The revision has been sent by email successfully',
                                        'success'
                                    );
                                    this.proofs = [];
                                    this.loadInitialRevision(this.project_id, revision_id);
                                } else {
                                    this.handle_error(response.data.errors);
                                }
                            } else {
                                this.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            this.showNewRevisionDialog = false;
                            this.handle_error({});
                        });
                } else {
                    toastr['error']('You should upload some proofs, before creating new revision round', 'Error');
                    this.fullscreenLoading = false;
                }
            },
            submitCorrections() {
                axios
                    .get("/api/projects/submit_corrections/" + this.project_id)
                    .then(response => {
                        if (response.data.status == 1) {
                            this.fullscreenLoading = false;
                            if (response.data.status == 1) {
                                this.showSubmitOptions = false;
                                swal(
                                    '',
                                    'The revision has been sent by email successfully',
                                    'success'
                                );
                                this.correctionButton = 'CORRECTIONS SUBMITTED';
                                this.correctionButtonStyle.background = '#fa5555';
                                this.correctionButtonStyle.color = '#fff';
                                this.correctionButtonStyle.padding = '12px 10px'
                            } else {
                                this.handle_error(response.data.errors);
                            }
                        } else {
                            this.handle_error(response.data.errors);
                        }
                    })
                    .catch(error => {
                        this.handle_error({});
                    });
            },
            setIssueSolved(issue_details, status) {
                var self = this;
                var issue_id = issue_details.id;
                var group_id = issue_details.group;

                self.fullscreenLoading = true;
                axios
                    .get("/api/proof/change_issue_status/" + issue_id + '/' + status + '/' + this.project_type)
                    .then(response => {
                        if (response.data.status == 1) {
                            self.fullscreenLoading = false;

                            if (response.data.data[0].id == self.current_proof.id) {
                                self.current_proof.status = response.data.data[0].status;
                            }

                            if (this.current_proof.issues.length > 0) {
                                this.current_proof.issues.forEach(function (item, index) {
                                    if (item.id == issue_id) {
                                        item.status = status;
                                    }
                                });
                            }
                            this.updateProgress('increase_solved');

                            var colors = {
                                unresolved: issue_details.owner_type == 'freelancer' ? '#565B64' : '#F07F7F',
                                resolved: '#86b5a0'
                            };
                            var color = (status == 'solved') ? colors.resolved : colors.unresolved;

                            this.background_group.getChildren(function (node) {
                                if (node.id() == group_id) {
                                    node.getChildren(function (shape) {

                                        if (shape.name() == 'bt-rect') {
                                            shape.setAttr('stroke', color);
                                            shape.setAttr('fill',
                                                status == 'solved' ? 'rgba(134,181,160, .5)' : 'transparent');
                                        } else if (shape.name() == 'bt-anchor') {

                                            shape.setAttr('stroke', color);

                                        } else if (shape.getClassName() == 'Circle') {

                                            shape.setAttr('fill', color);
                                        }

                                        if (shape.name() == 'bt-check') {

                                            if (status == 'solved') shape.visible(true);
                                            else shape.visible(false);
                                        }

                                        self.background.draw();
                                        self.saveCanvasState();
                                    });
                                }
                            });

                        } else {
                            self.handle_error(response.data.errors);
                        }
                    })
                    .catch(error => {
                        self.handle_error({});
                    });
            },
            cancelIssue() {
                var group = this.background.find("#" + this.current_group_id);
                group.remove();
                this.group_counter--;
                this.background.draw();
                /*   this.resetZomm(); */
                /*   this.applyZoom(); */
                /*  this.saveCanvasState(); */
                this.resetNewIssue();
                this.pen_active = false;
                this.$el.querySelector('#stage').style.cursor = 'initial';
                this.showIssueDialog = false;
                this.isActiveIssue = false;
            },
            cancelSlideUpload() {
                this.showSlideUploadDialog = false;
            },
            cancelNewRevision() {
                this.fullscreenLoading = true;
                axios
                    .delete("/api/revisions/delete/" + this.project_id)
                    .then(response => {
                        if (response.data.status == 1) {
                            this.fullscreenLoading = false;
                            this.showNewRevisionDialog = false;
                        } else {
                            this.handle_error(response.data.errors);
                        }
                    })
                    .catch(error => {
                        this.handle_error({});
                    });
            },
            cancelUploadFinalFiles() {
                this.showFinalFilesUploadDialog = false;
            },
            cancelEditableIssue() {
                this.resetEditableIssue();
                this.showEditableIssueDialog = false;
            },
            cancelEditableComment() {
                this.resetEditableComment();
                this.showEditableCommentDialog = false;
            },
            resetIssue() {
                this.current_issue.id = "";
                this.current_issue.description = "";
                this.current_issue.label = "";
            },
            resetNewIssue() {
                this.new_issue.id = "";
                this.new_issue.description = "";
                this.new_issue.label = "";
            },
            toggleIssueImage(id, el) {
                $('#' + el + id).toggle();
            },
            resetEditableIssue() {
                this.editable_issue.id = "";
                this.editable_issue.description = "";
            },
            inlineIssueEdit(issue_id, el) {
                $('#' + el + issue_id).trigger('click');
                $('#' + el + issue_id).select().focus();
            },
            resetEditableComment() {
                this.editable_comment.id = "";
                this.editable_comment.description = "";
            },
            resetComment() {
                this.current_comment.id = "";
                this.current_comment.issue_id = "";
                this.current_comment.description = "";
            },
            activate_group(group_id) {
                var current_group = this.stage.find("#" + group_id);
                /*  current_group.getChildren(function (node) {
                   console.log(node);
                 }); */
                /* var circle = this.stage.find("#" + circle_id);
                console.log(circle);
                circle.fill("#fff");
                this.stage.draw(); */
                // {

                /* if (node.getClassName() == 'Circle') {
                    if (node.getAttr('group_id') == group_id) {
                      node.stroke('#fff');
                    } else {
                      node.stroke('#f76565');
                    }
                  } */
                //});
                /*  circle.stroke('#fff');
                   console.log(circle); */
            },
            onOpenDialogNewIssue() {
                var self = this;
                this.showIssueDetails = false;
                this.imageFormData.owner_type = "issue";
                this.imageFormData.issue_id = this.current_issue.id;
                /* this.$refs.autofocus_issues.focus(); */
                Vue.nextTick(function () {
                    self.$refs.autofocus_issue_create.focus();
                });

            },
            onOpenDialogEditIssue() {
                var self = this;
                this.imageFormData.owner_type = "issue";
                this.imageFormData.issue_id = this.editable_issue.id;
                // Vue.nextTick(function () {
                //     self.$refs.autofocus_issue_edit.focus();
                // });
            },
            onOpenDialogEditComment() {
                var self = this;
                this.imageFormData.owner_type = "comment";
                this.imageFormData.comment_id = this.editable_comment.id;
                Vue.nextTick(function () {
                    self.$refs.autofocus_comment.focus();
                });
            },
            onOpenDialogSlideUpload() {
                this.imageFormData.owner_type = "proof";
                this.imageFormData.comment_id = this.editable_comment.id;
            },
            onOpenDialogNewRevision() {
                this.imageFormData.owner_type = 'proof';
                this.fullscreenLoading = false;
            },
            onOpenDialogNewCollaborators() {

            },
            onCloseDialogNewIssue() {
                this.reset_errors();
                this.imageFormData.owner_type = "";
            },
            onCloseDialogEditIssue() {
                this.imageFormData.owner_type = "";
                this.fileList = [];
            },
            onCloseDialogEditComment() {
                this.imageFormData.owner_type = "";
                this.fileList = [];
            },
            onCloseDialogSlideUpload() {
                this.imageFormData.owner_type = "";
                this.fileList = [];
            },
            onCloseDialogNewRevision() {
                this.imageFormData.owner_type = '';
                this.fileList = [];
            },
            onCloseDialogNewCollaborators() {
                this.newCollaborators = [];
            },
            activate_issue(issue_id) {
                if (this.current_proof.issues.length > 0) {
                    for (var i in this.current_proof.issues) {
                        if (this.current_proof.issues[i].id == issue_id) {
                            this.current_proof.issues[i].active_issue = true;
                        } else {
                            this.current_proof.issues[i].active_issue = false;
                        }
                    }
                }
            },
            IssueDetails(issue_id) {
                // this.current_group_id = current_group_id;
                var self = this;
                if (self.current_proof.issues.length > 0) {
                    self.current_proof.issues.forEach(function (issue_, index) {
                        if (issue_.id == issue_id) {
                            self.current_issue.id = issue_id;
                            self.issue_datils = issue_;
                            self.issue_datils.active_issue = true;
                            if (issue_.comments && issue_.comments.length > 0) {
                                issue_.comments.sort(function (a, b) {
                                    return b.id - a.id;
                                });
                            } else {
                                issue_.comments = [];
                            }
                            self.showIssueDetails = true;
                            if (issue_.unread_comments && issue_.unread_comments.length > 0) {
                                axios
                                    .delete("/api/proof/delete_issue_unread_comments/" + issue_.id)
                                    .then(response => {
                                        if (response.data.status == 1) {
                                            issue_.unread_comments = [];
                                        }
                                    })
                                    .catch(error => {
                                        // self.handle_error({});
                                    });
                            } else {
                                issue_.unread_comments = [];
                            }
                        }
                    });
                }
            },
            issuesList() {
                this.errors.comment_description = false;
                this.showIssueDetails = false;
            },
            scrollToComment(comment) {
                let self = this;
                setTimeout(function () {
                    var container = self.$el.querySelector("#comment-container");
                    var comm = self.$el.querySelector(comment);
                    container.scrollTop = comm.offsetTop;
                }, 1000);
            },
            deleteComment(issue_id, comment_id) {
                var self = this;
                if (issue_id > 0 && comment_id > 0) {
                    axios
                        .delete("/api/proof/delete_comment/" + comment_id)
                        .then(response => {
                            if (response.data.status == 1) {
                                self.fullscreenLoading = false;
                                self.current_proof.issues.forEach(function (issue_, index) {
                                    if (issue_.id == issue_id) {
                                        issue_.comments.forEach(function (comment, index_j) {
                                            if (comment.id == comment_id) {
                                                issue_.comments.splice(index_j, 1);
                                            }
                                        });
                                    }
                                });
                            } else {
                                self.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            self.handle_error({});
                        });
                }

                // swal({
                //     title: "Are you sure?",
                //     text: "You won't be able to revert this!",
                //     type: "warning",
                //     showCancelButton: true,
                //     confirmButtonColor: "#3085d6",
                //     cancelButtonColor: "#d33",
                //     confirmButtonText: "Yes, delete it!"
                // }).then(result => {
                //     if (result.value) {
                //         if (issue_id > 0 && comment_id > 0) {
                //             axios
                //                 .delete("/api/proof/delete_comment/" + comment_id)
                //                 .then(response => {
                //                     if (response.data.status == 1) {
                //                         self.fullscreenLoading = false;
                //                         self.current_proof.issues.forEach(function (issue_, index) {
                //                             if (issue_.id == issue_id) {
                //                                 issue_.comments.forEach(function (comment, index_j) {
                //                                     if (comment.id == comment_id) {
                //                                         issue_.comments.splice(index_j, 1);
                //                                     }
                //                                 });
                //                             }
                //                         });
                //                     } else {
                //                         self.handle_error(response.data.errors);
                //                     }
                //                 })
                //                 .catch(error => {
                //                     self.handle_error({});
                //                 });
                //         }
                //     }
                // });
            },
            cancelEditComment() {
                this.showCommentDialog = false;
            },
            saveCommentImage(comment_id, index) {
                var self = this;
                self.imageFormData.owner_type = "comment";
                self.imageFormData.comment_id = comment_id;
                self.$refs['save_comment_image' + comment_id][0].submit();
                self.issue_datils.comments.forEach(function (comment) {
                    if (comment_id == comment.id) {
                        comment.show_comment = true;
                        comment.show_attachment = true;
                        comment.show_attachment_del_conf = false;
                        comment.show_attachment_uploader = false;
                    }
                });
            },
            deleteIssueImage(issue_id, file_id) {
                var self = this;
                this.deletePicture(file_id);
                this.current_proof.issues.forEach(function (issue) {
                    if (issue_id == issue.id) {
                        if (self.showIssueDetails === false) {
                            issue.show_issues = true;
                            issue.show_issues_controls = false;
                            issue.show_issues_del_conf = false;
                            issue.show_issues_del_img_conf = false;
                            issue.show_issues_attachment = false;
                            issue.show_issues_attachment_uploader = false;
                        } else {
                            issue.show_issue = true;
                            issue.show_issue_controls = false;
                            issue.show_issue_del_conf = false;
                            issue.show_issue_del_img_conf = false;
                            issue.show_issue_attachment = false;
                            issue.show_issue_attachment_uploader = false;
                        }
                    }
                });
            },
            deleteCommentImage(comment_id, file_id) {
                var self = this;
                this.deletePicture(file_id);
                self.issue_datils.comments.forEach(function (comment) {
                    if (comment_id == comment.id) {
                        comment.show_comment = true;
                        comment.show_attachment = false;
                        comment.show_attachment_uploader = false;
                        comment.show_comment_del_conf = false;
                        comment.show_attachment_del_conf = false;
                    }
                });
            },
            loadInitialRevision(project_id, revision_id) {
                var self = this;
                self.fullscreenLoading = true;
                axios
                    .get("/api/projects/load/" + project_id + '/' + revision_id)
                    .then(response => {
                        if (response.data.status == 1) {
                            var project = response.data.data;

                            if (project.firstOpen && project.project_type == 'website' && self.current_rol == 'client') {
                                this.promptPluginInstall = true;
                            }

                            if (response.data.data.project_type == "video") {
                                if (response.data.data.project_video_url != "" && response.data.data.project_video_url != null) {
                                    this.$refs.isNotVideo.setAttribute("style", "visibility: hidden; position: absolute; z-index: 99");
                                    this.$refs.buttonsBarPanel.style.display = "none";
                                    this.$refs.thumbTovideo.style.display = "block";

                                    if (response.data.data.project_video_url.includes('youtube.com/embed')) {
                                        this.YoutubeVideoVue(response.data.data.project_video_url, 'embed');
                                    } else if (response.data.data.project_video_url.includes('youtu.be')) {
                                        this.YoutubeVideoVue(response.data.data.project_video_url, 'youtu');
                                    } else if (response.data.data.project_video_url.includes('youtube.com/watch')) {
                                        this.YoutubeVideoVue(response.data.data.project_video_url, 'watch');
                                    } else if (response.data.data.project_video_url.includes('dropbox.com')) {
                                        let drop_video_url = response.data.data.project_video_url;
                                        let dropbox_video = drop_video_url.replace("dropbox.com", "dl.dropboxusercontent.com");

                                        let videoTag = '<video ref="videoTagRef" controls="controls" id="VideoProofloUrlStreaming" width="100%" autoplay><source ref="videoSourceGdribeRef" type="video/mp4" src="' + dropbox_video + '"><source ref="videoSourceGdribeRef" type="video/webm" src="' + dropbox_video + '"></video>';
                                        this.$refs.divVideoTagRef.innerHTML = videoTag;
                                        /*this.$refs.videoTagRef.src = dropbox_video; */

                                    } else if (response.data.data.project_video_url.includes('drive.google.com')) {

                                        if (response.data.data.project_video_url.includes('drive.google.com/uc')) {
                                            let videoTag = '<video ref="videoTagRef" controls="controls" id="VideoProofloUrlStreaming" width="100%" autoplay><source ref="videoSourceGdribeRef" type="video/mp4" src="' + response.data.data.project_video_url + '"><source ref="videoSourceGdribeRef" type="video/webm" src="' + response.data.data.project_video_url + '"></video>';
                                            this.$refs.divVideoTagRef.innerHTML = videoTag;
                                        } else {
                                            let gdsplit = response.data.data.project_video_url.split('/d/');
                                            let gdSplitId = gdsplit[gdsplit.length - 1];
                                            let driveVideoId = gdSplitId.split('/');
                                            let driveVideo = "https://drive.google.com/uc?export=download&id=" + driveVideoId[0];
                                            /*this.$refs.videoSourceGdribeRef.src = driveVideo;*/

                                            let videoTag = '<video ref="videoTagRef" controls="controls" id="VideoProofloUrlStreaming" width="100%" autoplay><source ref="videoSourceGdribeRef" type="video/mp4" src="' + driveVideo + '"><source ref="videoSourceGdribeRef" type="video/webm" src="' + driveVideo + '"></video>';
                                            this.$refs.divVideoTagRef.innerHTML = videoTag;
                                        }

                                    } else {
                                        this.$refs.videoTagRef.src = response.data.data.project_video_url;
                                    }
                                }
                            } else {
                                this.$refs.buttonsBarPanel.style.display = "block";
                                this.$refs.proofloTakeScreenshotFromVideoRef.style.display = "none";
                                this.$refs.thumbTovideo.style.display = "none";
                            }
                            self.websiteURI = response.data.data.websiteURI;
                            self.project_video_url = response.data.data.project_video_url;
                            self.project_capture_time = response.data.data.project_capture_time;

                            self.project_status = response.data.data.project_status;
                            self.project_type = response.data.data.project_type;
                            self.exitingUsers = response.data.data.exitingUsers;
                            self.collaborators = response.data.data.collaborators;
                            self.teamMembers = response.data.data.teamMembers;
                            self.versions = response.data.data.versions;
                            self.current_version_number = response.data.data.last_revision.version;
                            self.value = 'Revision Round ' + response.data.data.last_revision.version;
                            self.current_review_id = response.data.data.last_revision.id;
                            self.last_version_number = response.data.data.last_revision.id;
                            self.revision_status = response.data.data.last_revision.status_revision;
                            self.total_issues = response.data.data.total_issues;
                            self.solved_issues = response.data.data.solved_issues;
                            self.percentage = response.data.data.percentage;

                            if (self.project_type == 'design') {
                                self.approveButton = 'APPROVE DESIGN';
                            } else if (self.project_type == 'video') {
                                self.approveButton = 'APPROVE VIDEO';
                            } else {
                                self.approveButton = 'APPROVE WEBSITE';
                            }
                            if (response.data.data.last_revision.proofs.length > 0) {
                                response.data.data.last_revision.proofs.forEach(function (element, index) {
                                    var proof = {
                                        id: element.id,
                                        revision_id: element.revision_id,
                                        project_id: response.data.data.last_revision.project_id,
                                        active_thumb: false,
                                        picture_url: "/storage/" + element.project_files.path,
                                        thumb_url: "/storage/" + element.project_files.path,
                                        canvas_data: element.canvas_data,
                                        issues: element.issues,
                                        user_id: element.project_files.user_id,
                                        status: element.status,
                                        capture_time: element.project_files.capture_time
                                    };

                                    proof.issues.forEach(function (issue) {

                                        issue.show_issues = true;
                                        issue.show_issues_controls = false;
                                        issue.show_issues_del_conf = false;
                                        issue.show_issues_del_img_conf = false;
                                        issue.show_issues_attachment = false;
                                        issue.show_issues_attachment_uploader = false;

                                        issue.show_issue = true;
                                        issue.show_issue_controls = false;
                                        issue.show_issue_del_conf = false;
                                        issue.show_issue_del_img_conf = false;
                                        issue.show_issue_attachment = false;
                                        issue.show_issue_attachment_uploader = false;

                                        issue.comments.forEach(function (comment) {
                                            comment.show_comment = true;
                                            comment.show_attachment = false;
                                            comment.show_attachment_uploader = false;
                                            comment.show_comment_del_conf = false;
                                            comment.show_attachment_del_conf = false;
                                        });
                                    });

                                    self.proofs.push(proof);
                                });
                                self.fullscreenLoading = false;
                                if (self.proofs.length > 0) {
                                    self.loadThumb(0);
                                    // self.pen_active = true;
                                    let location = window.location.href;
                                    if (location.indexOf('#') != -1) {
                                        let issue = location.substring(location.indexOf('#') + 1, location.indexOf('-'));
                                        let comment = '#comment_' + location.substring(location.indexOf('-') + 1);
                                        self.IssueDetails(issue);
                                        self.scrollToComment(comment);
                                    }
                                }
                            } else {
                                self.fullscreenLoading = false;
                            }
                        } else {
                            this.handle_error(response.data.errors);
                        }
                    })
                    .catch(error => {
                        self.sendLoading = false;
                    });
            },
            YoutubeVideoVue(video_url, type) {
                let videoId = "";
                if (type == 'embed') {
                    var embedurl = video_url.split('/');
                    videoId = embedurl[embedurl.length - 1];
                } else if (type == 'youtu') {
                    var embedurl = video_url.split('/');
                    videoId = embedurl[embedurl.length - 1];
                } else if (type == 'watch') {
                    let t = /(\?v=|\&v=|\/\d\/|\/embed\/|\/v\/|\.be\/)([a-zA-Z0-9\-\_]+)/, n = video_url,
                        o = n.match(t);
                    videoId = o[2];
                }
                let token = document.head.querySelector('meta[name="csrf-token"]');
                window.axios.defaults.headers.common = {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                };
                axios.get("https://eywbadb872.execute-api.eu-west-1.amazonaws.com/prod?video_id=" + videoId)
                    .then((response) => {
                        if (response.statusText == "OK") {
                            let desoceRes = this.decodeQueryStringVue(response.data);
                            let videoURL = this.getFinalResultOfVideoURL(desoceRes);
                            this.$refs.videoUrlFromDatabaseRef.value = videoURL;
                            /*this.$refs.videoSourceGdribeRef.src = videoURL;*/

                            let videoTag = '<video ref="videoTagRef" controls="controls" id="VideoProofloUrlStreaming" width="100%" autoplay><source ref="videoSourceGdribeRef" type="video/mp4" src="' + videoURL + '"><source ref="videoSourceGdribeRef" type="video/webm" src="' + videoURL + '"></video>';
                            this.$refs.divVideoTagRef.innerHTML = videoTag;
                        }
                    })
                    .catch((error) => {
                        dispatch({type: ERROR_FINDING_USER})
                    });
            },
            decodeQueryStringVue(e) {
                let t, n, o, i, r, a, d;
                for (i = {},
                         o = e.split("&"),
                         a = 0,
                         d = o.length; d > a; a++)
                    n = o[a],
                        t = decodeURIComponent(n.split("=")[0]),
                        r = decodeURIComponent(n.split("=")[1] || ""),
                        i[t] = r;
                return i
            },
            getFinalResultOfVideoURL(n) {
                if (n.status == "fail") {
                } else {
                    let a = this.decodeStreamMapVue(n.url_encoded_fmt_stream_map);
                    var n, o, i, r;
                    for (o in a) {
                        r = a[o];
                        if (r.quality.match("medium")) {
                            return r.url;
                        }
                    }
                }
            },
            decodeStreamMapVue(e) {
                let t, n, o, i, r, a, d, s;
                for (n = {},
                         s = e.split(","),
                         a = 0,
                         d = s.length; d > a; a++)
                    r = s[a],
                        o = this.decodeQueryStringVue(r),
                        i = o.type.split(";")[0],
                        t = o.quality.split(",")[0],
                        o.original_url = o.url,
                        o.url = "" + o.url + "&signature=" + o.sig,
                        n["" + i + " " + t] = o;
                return n
            },
            loadRevisionById() {
                var self = this;
                self.fullscreenLoading = true;
                axios
                    .get("/api/revisions/load_revision_by_id/" + this.value)
                    .then(response => {
                        if (response.data.status == 1) {
                            var revision = response.data.data.revision;
                            if (revision.proofs.length > 0) {
                                self.proofs = [];
                                self.revision_status = revision.status_revision;
                                self.current_review_id = revision.id;
                                revision.proofs.forEach(function (element, index) {
                                    var proof = {
                                        id: element.id,
                                        revision_id: element.revision_id,
                                        project_id: revision.project_id,
                                        active_thumb: false,
                                        picture_url: "/storage/" + element.project_files.path,
                                        thumb_url: "/storage/" + element.project_files.path,
                                        canvas_data: element.canvas_data,
                                        issues: element.issues,
                                        show_issues: true,
                                        capture_time: element.project_files.capture_time
                                    };
                                    self.proofs.push(proof);
                                    self.fullscreenLoading = false;

                                    proof.issues.forEach(function (issue) {
                                        issue.show_issues = true;
                                    });
                                });
                                self.total_issues = response.data.data.total_issues;
                                self.solved_issues = response.data.data.solved_issues;
                                self.percentage = response.data.data.percentage;
                                if (self.proofs.length > 0) {
                                    self.loadThumb(0);
                                }
                            } else {
                                self.fullscreenLoading = false;
                            }
                        } else {
                            this.handle_error(response.data.errors);
                        }
                    })
                    .catch(error => {
                        self.sendLoading = false;
                    });
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
            handlePreview() {
            },
            handleRemove() {
            },
            handleError(error) {
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
                        });
                        setTimeout(function () {
                            self.pdfLoader = false;
                        }, 500)
                    } else {
                        this.savedFiles.push(response.data);
                    }
                    toastr['success'](response.message, 'Success');
                } else {
                    toastr['error'](response.error, 'Error');
                }
            },
            saveIssueImage(issue_id, index) {
                var self = this;
                self.imageFormData.owner_type = "issue";
                self.imageFormData.issue_id = issue_id;
                if (index === false) {
                    self.$refs['save_issue_img' + issue_id].submit();
                } else {
                    self.$refs['save_issue_image' + issue_id][0].submit();
                }
                self.editable_issue.id = issue_id;
                var showIssueDetails = this.showIssueDetails;

                self.current_proof.issues.forEach(function (issue) {
                    if (issue_id == issue.id) {
                        if (showIssueDetails == false) {
                            issue.show_issues = true;
                            issue.show_issues_del_conf = false;
                            issue.show_issues_del_img_conf = false;
                            issue.show_issues_attachment_uploader = false;
                        } else {
                            issue.show_issue = true;
                            issue.show_issue_del_conf = false;
                            issue.show_issue_del_img_conf = false;
                            issue.show_issue_attachment_uploader = false;
                        }
                    }
                });
            },
            issueImageUploaded(response) {
                if (response.status == 1) {
                    this.savedFiles.push(response.data);
                    // toastr['success'](response.message, 'Success');

                    this.showEditableIssueDialog = false;
                    var issue_id = this.editable_issue.id;
                    var issue_image_path = "/storage/" + response.data.path;
                    var showIssueDetails = this.showIssueDetails;
                    this.current_proof.issues.forEach(function (item, index) {
                        if (item.id == issue_id) {
                            var desc = item.description;
                            item.project_files_id = response.data.id;
                            item.project_files = {
                                id: response.data.id,
                                path: response.data.path,
                                thumb_path: response.data.thumb
                            };
                            item.description = '*';
                            if (showIssueDetails == false) {
                                item.show_issues_controls = true;
                                item.show_issues_attachment = true;
                            } else {
                                item.show_issue_controls = true;
                                item.show_issue_attachment = true;
                            }
                            item.description = desc;
                        }
                    });
                } else {
                    toastr['error'](response.error, 'Error');
                }
            },

            handleUpload(file) {
                if (file.type == 'application/pdf') {
                    this.pdfLoader = true;
                }
            },

            applyZoom() {
                var self = this;
                self.background_group.scale(
                    {
                        x: self.zoom_level,
                        y: self.zoom_level
                    }
                );

                var pos_bg = this.background_group.position();
                var pos_center = {
                    x: this.initialStageWidth / 2,
                    y: this.initialStageHeight / 2
                };
                var scale = this.zoom_level - 0.1;
                var diff_scale = this.zoom_level - this.zoom_level_old;

                var diff = {
                    x: (pos_bg.x * scale - diff_scale * pos_center.x) / (scale - diff_scale) - pos_bg.x,
                    y: (pos_bg.y * scale - diff_scale * pos_center.y) / (scale - diff_scale) - pos_bg.y,
                };
                this.background_group.move(diff);
                self.zoom_level_old = self.zoom_level;

                self.background_group.getChildren(function (node) {

                    if (node.getClassName() === "Group") {
                        self.adjustScaleOfTools(node);
                    }

                    return node.getClassName() === "Group";
                });
                self.background.draw();
            },
            handleOpen(key, keyPath) {
                console.log(key, keyPath);
            },
            handleClose(key, keyPath) {
                console.log(key, keyPath);
                this.finalLinks.splice(this.finalLinks.indexOf(key), 1);
            },
            jqueryCssAdjust() {
                $('.navbar').css('display', 'none');
                $('.el-slider__bar').css('background-color', 'rgb(216, 218, 223)');
                $('.el-slider__button').css('border-color', '#fa5555');
                $('.el-slider__button').css('width', '12px');
                $('.el-slider__button').css('height', '12px');
                $('.el-slider__runway').css('height', '4px');
                $('.el-slider__bar').css('border-radius', '0px');
                $('.el-slider__runway').css('border-radius', '0px');
                $('.el-slider__button').css('background-color', 'rgb(250, 85, 85)');
                $('.el-radio-button__inner').css('border-radius', '0px');
                $('.el-radio-button__inner').css('border-color', '#dfe4ed');
                $('.el-radio-button__inner').css('color', '#fff');
                $('.el-radio-button__orig-radio:checked + .el-radio-button__inner').css('background-color', 'rgb(224, 223, 223)');
                $('.el-radio-button__orig-radio:checked + .el-radio-button__inner').css('box-shadow', 'none');
                $('.el-radio-button__orig-radio:checked + .el-radio-button__inner').css('color', 'rgb(193, 195, 194)');
                $('.konvajs-content').css('height', $('#content').height);
                $('.el-dialog__header').css('padding-top', '20px');
                $('.el-dialog__body').css('padding-top', '0px');
                $('.el-dialog__body').css('padding-bottom', '10px');
                $('.el-dialog__footer').css('padding-bottom', '10px');
                $('.el-dialog__footer').css('padding-top', '0px');
                $('.el-upload__text').text('Click or drag here to upload files');
                $('.el-upload__input').css('display', 'none');

                var self = this;
                $(window).resize(function () {
                    var container = document.getElementById('stage');

                    self.initialStageWidth = container.offsetWidth;
                    self.initialStageHeight = container.offsetHeight;

                    self.stage.width(container.offsetWidth);
                    self.stage.height(container.offsetHeight);
                });

                let isFirefox = typeof InstallTrigger !== 'undefined';
                var event = isFirefox ? 'DOMMouseScroll' : 'mousewheel';

                $('#stage').bind(event, function (event) {

                    event.stopImmediatePropagation();
                    event.preventDefault();

                    var scale = self.background_group.scaleX();
                    var d = -60 * scale;

                    if (event.originalEvent.wheelDelta > 0 || event.originalEvent.detail < 0) {
                        d = -d;
                    }

                    self.background_group.move({
                        y: d
                    });
                    self.background.draw();
                });

                $('.toggle-button').click(function (event) {
                    event.stopImmediatePropagation();

                    $(this).parent().toggleClass('collapsed');

                    if ($(this).parent().attr('id') == 'rightSidebar') {

                        $('#main').toggleClass('right-expanded');

                        if ($('#main').hasClass('right-expanded')) {
                            self.stage.width($('#stage').width() + 300);
                        } else {
                            self.stage.width($('#stage').width() - 300);
                        }
                    } else {

                        $('#main').toggleClass('left-expanded');

                        if ($('#main').hasClass('left-expanded')) {
                            self.stage.width($('#stage').width() + 200);
                        } else {
                            self.stage.width($('#stage').width() - 200);
                        }
                    }
                    self.initialStageWidth = self.stage.width();
                });

                $('.btn-backtolist').hover(function () {
                    $('.btn-backtolist i').attr('class', 'el-icon-arrow-left');
                }, function () {
                    $('.btn-backtolist i').attr('class', 'el-icon-arrow-right');
                });
            },
            alert() {
                this.isCollapse = !this.isCollapse;
            },
            goToC() {

                this.$router.push({path: '/prooflo/c2'});
            },
            open() {
                this.$alert('This is a message', 'Title', {
                    confirmButtonText: 'OK',
                    callback: action => {
                        this.$message({
                            type: 'info',
                            message: `action: ${action}`
                        });
                    }
                });
            },
            cancelSubmitCorrections() {
                this.showSubmitOptions = false;
            },
            cancelApproveCorrections() {
                this.showApproveDesign = false;
            },
            w3_open() {
                // document.getElementById("main").style.marginLeft = "15%";
                // document.getElementById("leftSidebar").style.width = "15%";
                // document.getElementById("main").style.width = "66%";
                // document.getElementById("leftSidebar").style.display = "block";
                //   $('.konvajs-content').css('width', $('#main').width());
                //    $('canvas').css('width', $('#main').width());

                /* document.getElementById("openNav").style.display = 'none'; */
            },
            w3_close() {
                document.getElementById("main").style.marginLeft = "0%";
                document.getElementById("main").style.width = document.getElementById("leftSidebar").offsetWidth;
                console.log(document.getElementById("stage").offsetWidth);
                /*   $('.konvajs-content').css('width', $('#main').width());
                  $('canvas').css('width', $('#main').width()); */
                document.getElementById("leftSidebar").style.display = "none";
                /*  document.getElementById("openNav").style.display = "inline-block"; */
            },
            moveToInProgress() {
                this.$confirm('Are you sure you want to move this project?', 'Warning', {
                    confirmButtonText: 'Yes, move',
                    cancelButtonText: 'Cancel',
                    type: 'success',
                    title: ''
                }).then(() => {
                    this.fullscreenLoading = true;
                    axios
                        .put("/api/projects/change_status/" + this.project_id)
                        .then(response => {
                            this.fullscreenLoading = false;

                            if (response.status == 200) {
                                swal({
                                    title: '',
                                    text: 'Project successfully moved to In Progress',
                                    type: 'success'
                                })
                                    .then(success => {
                                        window.location.href = "/projects";
                                    })
                            } else {
                                toastr['error'](response.error, 'Error');
                            }
                        })
                })
            },
            updateProgress(type) {
                if (type == 'increase_solved') {
                    this.solved_issues++;
                    this.percentage = Math.round((this.solved_issues / this.total_issues) * 100)
                } else if (type == 'increase_total') {
                    this.total_issues++;
                    this.percentage = Math.round((this.solved_issues / this.total_issues) * 100)
                } else if (type == 'decrease_total') {
                    this.total_issues--;
                    this.percentage = Math.round((this.solved_issues / this.total_issues) * 100)
                }
            },
            inviteCollabs() {
                var self = this;
                this.fullscreenLoading = true;
                if (this.newCollaborators.length > 0) {
                    let formData = {
                        project_id: self.project_id,
                        collaborators: self.newCollaborators
                    };
                    axios
                        .post("/api/projects/inviteCollaborators", formData)
                        .then(response => {
                            self.fullscreenLoading = false;
                            self.showNewCollaboratorDialog = false;
                            if (response.data.status == 1) {
                                toastr['success']('Your invite sent successfully', 'Success');
                                this.proofs = [];
                                this.loadInitialRevision(this.project_id, this.revision_id);
                            } else {
                                this.handle_error(response.data.errors);
                            }
                        })
                        .catch(error => {
                            self.showNewCollaboratorDialog = false;
                            self.handle_error({});
                        });
                } else {
                    toastr['error']('Please add some collaborators', 'Error');
                    this.fullscreenLoading = false;
                }
            },
            cancelInviteCollabs() {
                this.showNewCollaboratorDialog = false;
            },
            gotoIssue(group_id) {
                var self = this;

                var group = self.background.find("#" + group_id)[0];
                var bg_group = self.background_group;

                var scale = 2;

                self.zoom_level = scale;
                self.applyZoom();

                var circle;
                if (group.name() == 'bt-group') {
                    circle = group.get('.bt-circle')[0];
                } else {
                    circle = group.get('Circle')[0];
                }

                var width = (circle.x() + group.x()) * scale;
                var height = (circle.y() + group.y()) * scale;

                var lengths = self.convertLengths(width, height);

                var pos = {
                    x: bg_group.x() + lengths.width,
                    y: bg_group.y() + lengths.height
                };

                var diff = {
                    x: self.initialStageWidth / 2 - pos.x,
                    y: self.initialStageHeight / 2 - pos.y,
                };

                moveScreen();

                var i = 0;

                function moveScreen() {
                    var sec = 20;
                    if (i == (sec - 1)) return;

                    bg_group.move({
                        x: diff.x / sec,
                        y: diff.y / sec
                    });
                    self.background.draw();

                    i++;
                    setTimeout(moveScreen, 1);
                }
            },
            detectExtension() {
                var img, self = this;
                img = new Image();
                img.src = "chrome-extension://chhgohjcahedplonbibfaeefpnnknapp/images/Icon_Initial_32.png";
                img.onload = function() {
                    self.pluginInstalled = true;
                };
                img.onerror = function() {
                    self.pluginInstalled = false;
                };
            },
            installProofloPlugin() {
                this.promptPluginInstall = false;
                this.loadInitialRevision(this.project_id, this.revision_id);
                window.open('https://chrome.google.com/webstore/detail/prooflo/chhgohjcahedplonbibfaeefpnnknapp', '_blank')
            },
            takeScreenshots() {
                this.promptPluginInstall = false;
                this.loadInitialRevision(this.project_id, this.revision_id);
                window.open(this.websiteURI, '_blank')
            }
        },
        watch: {
            showSideBar() {
                if (this.showSideBar == true) {
                    this.w3_open();
                } else {
                    this.w3_close();
                }
            },
            'current_comment.description'() {
                if (this.current_comment.description != '') {
                    this.errors.comment_description = false;
                }
            },
            'editable_issue.description'() {
                if (this.editable_issue.description != '') {
                    this.errors.editable_issue_description = false;
                }
            },
            'current_issue.description'() {
                if (this.new_issue.description != '') {
                    this.errors.current_issue_description = false;
                }
            },
            'editable_comment.description'() {
                if (this.editable_comment.description != '') {
                    this.errors.editable_comment_description = false;
                }
            },
            zoom_level() {
                this.applyZoom();
            },

        },
        computed: {
            ...mapGetters(
                {current_user: 'user', projects: 'projects', current_role: 'current_role'}
            )
        },
        created() {
            this.fullscreenLoading = true;
            this.detectExtension();
            axios.get("/api/auth/getCurrentRole/" + this.project_id)
                .then(response => {
                    if (response.data.status == 1) {
                        this.current_rol = response.data.data.user_role;
                        this.current_user_id = response.data.data.user_id;
                        this.imageFormData.user_id = response.data.data.user_id;
                        this.loadInitialRevision(this.project_id, this.revision_id);
                    } else {
                        this.handle_error(response.data.errors);
                    }
                })
                .catch(error => {
                    self.sendLoading = false;
                });

            this.imageFormData.file_type = "picture";
            this.imageFormData.project_id = this.project_id;
            this.disabled_remove = true;
        },
        mounted() {
            this.$nextTick(function () {
                this.w3_open();
                var container = document.getElementById('stage');
                this.initialStageWidth = container.offsetWidth;
                this.initialStageHeight = container.offsetHeight;
                this.jqueryCssAdjust();

            });

            /*  if (this.current_proofs_state.length > 0) {
                 if (this.proofs.length > 0) {
                     if (this.proofs[0].project_id == this.current_proofs_state[0].project_id) {
                         this.proofs = this.current_proofs_state;
                     }
                 }
             } */

            //this.loadInitialRevision(this.project_id, this.revision_id);

        },
        updated() {
            this.jqueryCssAdjust();
        }

    }
</script>
<style>
    body {
        padding-top: 0px !important;
        /*overflow-y: hidden;
        overflow-x: hidden;*/
        height: 100vh;
        padding-right: 0px !important;
    }

    .el-progress-bar__outer {
        background-color: #545c64 !important;
    }

    .el-radio-button__orig-radio:checked + .el-radio-button__inner {
        background-color: #2196F3;
        border-color: rgb(18, 130, 221);
    }

    .slide-pagination {
        background-color: rgb(224, 223, 223);
        margin: unset !important;
        padding: 11px 0;
    }

    #project-actions {
        margin-left: 200px;
        margin-right: 300px;
    }

    .btn-next, .btn-prev {
        width: 30px;
        height: 31px !important;
    }

    .slide-number {
        width: 30px;
        height: 31px;
    }

    .el-pager li {
        width: 30px;
        height: 31px;
    }

    .el-pager .el-input__inner {
        width: 30px;
        padding: unset !important;
        /*border: none !important;*/
    }

    .el-pager .el-input__inner:focus {
        border-color: #409eff !important;
    }

    .btn-prev, .btn-next, .el-pager li {
        background: #fff !important;
    }

    .el-pager li:first-child, .el-pager li:nth-child(2) {
        background-color: transparent !important;
    }

    #stage-parent {
        margin: 0;
        padding: 0;
        height: calc(100vh - 123px - 56px);
        /*height: calc(100vh - 123px - 56px);*/
        width: 100%;
        overflow: auto;
    }

    #stage {
        height: 100%;
        width: 100%;
    }

    #stage-parent::-webkit-scrollbar {
        display: none;
    }

    .collabs::-webkit-scrollbar {
        display: none;
    }

    #stage-parent {
        overflow: -moz-scrollbars-none;
    }

    .slide-img-thumb {
        width: auto;
        height: 120px;
        background-size: cover;
        background-repeat: no-repeat;
        min-height: 180px;
    }

    .rollover, .files-rollover {
        width: 100%;
        height: 100%;
        z-index: 2 !important;
        position: absolute;
        display: flex;
        justify-content: center;
        align-items: center;
        background: rgba(0, 0, 0, .5);
        opacity: 0;
    }



    .proof-status {
        width: 100%;
        height: 100%;
        z-index: 1 !important;
        position: absolute;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #80b4a0;
        opacity: .8;
        color: #fff;
        font-size: 120px;
    }

    .slide-deck:hover .rollover {
        opacity: 1;
    }

    #content {
        height: 100% !important
    }

    .icon-center {
        margin-top: 40% !important;
        margin-left: -40% !important;
    }

    .top-bar-full {
        box-shadow: 1px 1px 0.1px 0px rgba(0, 0, 0, 0.2);
        margin-bottom: 0px;
    }

    .center-bar-full {
        margin-bottom: 0px;
    }

    .bg-top-bar {
        background-color: #f9f9f9;
    }

    .bg-back-btn {
        background: #fff;
        width: 64px;
        border-right: 1px solid #e6e6e6;
    }

    .bg-purple-light {
        background: #dadcdf;
    }

    .grid-content {
        border-radius: 0px;
        min-height: 60px;
        background: #545c64;
        color: #fff;
        display: flex;
        align-items: center;
    }

    .project-progress {
        background: #545c64;
        margin-top: 10px
    }

    .row-bg {
        padding: 10px 0;
        background-color: #f9fafc;
    }

    .side-menu-left, #leftSidebar {
        height: calc(100vh - 60px);
        /* padding: 10px; */
        overflow-y: auto;
        overflow-x: hidden;
        /* background-color: rgb(92, 81, 194); */
    }

    .side-menu-right, #rightSidebar {
        height: calc(100vh - 60px);
        overflow-y: auto;
        overflow-x: hidden;
        border-left: 1px solid #dadbda;
        /* background-color: #044921
 */
    }

    .central-content {
        margin-top: 20px;
    }

    .footer {
        box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.4), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        background-color: #f9f9f9;
        bottom: 0px;
        position: fixed;
        width: 100%;
        height: 50px;
        padding: 7px;
    }

    .edition-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        text-align: center;
        padding-left: 15px;
        padding-bottom: 10px;
        font-size: 22px;
        border: 1px solid #cacbcc;
        color: #626364;
        outline: none;
        background-color: #fff;
    }

    .edition-btn-active {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        text-align: center;
        padding-left: 15px;
        padding-bottom: 10px;
        font-size: 22px;
        border: none;
        color: #fff;
        outline: none;
        background-color: #fa5555;
    }

    .special-img {
        position: relative;

        float: left;

    }

    .img-circle {
        border-radius: 50%;
    }

    .current_issue_active {
        /*border-left: 10px solid #f07f7f;*/
        min-height: 100px;
        width: 100%;
        /*  background-color: #f07f7f18;*/
        margin-bottom: 0px;
        padding-bottom: 10px;
        padding-left: 10px;
        position: relative;
        padding-right: 10px;
        padding-top: 10px;
    }

    .current_issue_normal {
        /*border-left: 10px solid #cfcece;*/
        min-height: 100px;
        width: 100%;
        margin-bottom: 0px;
        padding-bottom: 10px;
        padding-top: 7px;
        position: relative;
        border-bottom: 2px solid #eee;
        border-left: 3px solid transparent;
        border-top: 3px solid transparent;
        border-right: 3px solid transparent;
        padding-left: 10px;
        padding-right: 10px;
    }

    .current_issue_solved {
        /*border-left: 10px solid #009688;*/
        min-height: 100px;
        width: 100%;
        background-color: #d5e7e0;
        margin-bottom: 0px;
        padding-bottom: 10px;
        position: relative;
        padding-top: 10px;
        padding-left: 10px !important;
        padding-right: 10px !important;
    }

    .comment-pending {
        border-radius: 10px;
        margin-left: 10px;
        margin-right: 0px;
        width: 100%;
        color: #4d4a4a;
        padding: 0 5px;
        /*     background-color: #f1f2f2;*/
        margin-bottom: 10px;
    }

    .fa-paperclip {
        cursor: pointer;
    }

    .el-icon-more {
        cursor: pointer;
    }

    .current_issue_solved button {
        background: #80B4A0 !important;
    }

    .tag_solved {
        background: #80B4A0 !important;
    }

    .comment {
        border-radius: 10px;
        margin-left: 10px;
        margin-right: 0px;
        width: 100%;
        color: #4d4a4a;
        padding: 0 5px;
        /*     background-color: #f1f2f2;*/
        margin-bottom: 10px;
    }

    a {
        cursor: pointer;
    }

    .comment-solved {
        border-radius: 10px;
        margin-left: 10px;
        margin-right: 10px;
        width: 100%;
        color: #4d4a4a;
        padding: 10px;
        background-color: #80b4a052;
        border-color: #023f1b;
        margin-bottom: 5px;
    }

    .active-thumb {
        border: 3px solid #f07f7f;
    }

    .regular-thumb {
        border: 3px solid #c7c5c5;
    }

    .active-thumb-number {
        background-color: #f07f7f;
        border-radius: 0px;
        text-align: center;
    }

    .regular-thumb-number {
        background-color: #727171;
        border-radius: 0px;
        text-align: center;
    }

    #app > div > div.el-dialog__wrapper > div {
        min-width: 300px !important;
    }

    .el-badge__content {
        font-size: 14px;
        height: 25px;
        line-height: 23px;
        text-align: center;
    }

    .issue-button-details {
        background-color: transparent;
        border-color: transparent;
        font-size: 20px;
        color: #4d4a4a;
    }

    .issue-button-edit {
        background-color: transparent;
        border-color: transparent;
        font-size: 20px;
        color: darkgreen;
    }

    .issue-button-solved {
        background-color: transparent;
        border-color: transparent;
        font-size: 20px;
        color: rgb(1, 25, 61);
    }

    .tag.tag_client {
        background-color: #f07f7f;
        border-color: rgb(250, 115, 115);
        font-size: 16px;
        font-family: Arial, Helvetica, sans-serif;
        color: #fff;
    }

    .tag.tag_freelancer {
        background-color: #565b64;
        border-color: #fff;
        font-size: 16px;
        font-family: Arial, Helvetica, sans-serif;
        color: #fff;
    }

    .tag_solved {
        background-color: #009688;
        border-color: #023f1b;
        font-size: 16px;
        font-family: Arial, Helvetica, sans-serif;
        color: #fff;
    }

    .el-button:hover,
    .el-button:focus {
        color: #80b4a0;
        border-color: transparent;
        background-color: transparent;
    }

    .el-upload-dragger {
        width: 100% !important;
        border-width: 0px !important;
        height: 130px;
    }

    .el-upload-list,
    .el-upload-list--picture {
        width: 100%;
    }

    .el-dialog__body {
        padding-bottom: 10px !important;
        padding-top: 0px;
    }

    #app > div > div:nth-child(7) > div > div.el-dialog__body {
        padding-top: 0px;
    }

    #app > div > div:nth-child(7) > div > div.el-dialog__header {
        padding-top: 0px;
    }

    .el-button--danger:hover,
    .el-button--danger:focus {
        background-color: #fb7777;
        color: #fff;
    }

    .el-radio-button__orig-radio:checked + .el-radio-button__inner {
        background-color: #2196F3;
        border-color: rgb(18, 130, 221);
    }

    .swal2-popup,
    .swal2-title {
        font-size: 20px !important;
    }

    .swal2-content {
        font-size: 16px !important;
    }

    .error_textarea {
        border: 1px solid red;
        border-radius: 4px
    }

    #app > div > div.center-bar-full.el-row > div.el-col.el-col-24.el-col-xs-24.el-col-sm-18.el-col-md-13.el-col-lg-15 > div > div.el-row.is-justify-end.el-row--flex > div.el-col.el-col-24.el-col-sm-16 > div > div > div.el-input.el-input--suffix > input {
        background-color: #f07f7f;
        border: 0px;
        color: #fff !important;
        font-weight: bold;
        text-align: center;
    }

    ::placeholder {
        /* Chrome, Firefox, Opera, Safari 10.1+ */
        color: #fff !important;
        opacity: 1;
        /* Firefox */
    }

    .new-issue textarea::placeholder {
        color: initial !important;
    }

    .el-select .el-input .el-select__caret {
        /*color: #fff !important;*/
        opacity: 1;
    }

    .stage-parent {
        width: 100%;
    }

    .w3-card {
        box-shadow: 1px 0px 7px 0px rgba(220, 218, 218, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12)
    }

    #leftSidebar {
        z-index: 300;
        width: 200px;
        overflow: visible;
        transition: .3s;
        left: 0;
    }

    #leftSidebar.collapsed {
        left: -200px;
    }

    #rightSidebar {
        z-index: 300;
        height: 93vh;
        /* top: 64px; */
        background-color: white;
        position: relative;
        overflow: visible;
        width: 300px;
        right: 0;
        transition: .3s;
    }

    #collaborators {
        z-index: 300;
        background-color: white;
        position: relative;
        overflow: visible;
        width: 300px;
        right: 0;
        transition: .3s;
        border-left: 1px solid rgb(224, 223, 223);
        height: initial;
    }

    #rightSidebar.collapsed {
        right: -300px;
    }

    #main {
        left: 200px;
        right: 300px;
        position: absolute;
        width: initial;
        transition: .3s;
    }

    .fade-in-enter-active,
    .fade-in-leave-active {
        transition: opacity .3s;
    }

    .fade-in-enter,
    .fade-in-leave-to
        /* .fade-leave-active below version 2.1.8 */

    {
        opacity: 0;
    }

    #pen-info,
    #boxtool-info {
        position: relative;
        display: flex;
        justify-content: center;
    }

    #pen-info #info {
        position: absolute;
        background: #545c64e0;
        width: 102px;
        color: white;
        font-size: 11px;
        padding: 7px 10px;
        top: 52px;
        border-radius: 5px;
        font-weight: bold;
        z-index: 999;
    }

    #info:after, #info:before {
        bottom: 100%;
        left: 50%;
        border: solid transparent;
        content: " ";
        height: 0;
        width: 0;
        position: absolute;
        pointer-events: none;
    }

    #info:before {
        border-color: rgba(194, 225, 245, 0);
        border-bottom-color: #545c64e0;
        border-width: 8px;
        margin-left: -9px;
    }

    .upload-container {
        background-size: cover;
        background-repeat: no-repeat;
        border-radius: 5px;
        margin-bottom: 5px;
        min-height: 180px;
        border: 2px dashed #d6e7e0;
        text-align: center;
        cursor: pointer;
    }

    .el-dialog__close {
        font-size: 23px !important;
    }

    .slide-dropdown {
        position: absolute;
        top: 0;
        right: 0;
    }

    @media (min-width: 768px) {
        #pen-info #info {
            left: -31px;
        }
    }

    @media (max-width: 460px) {
        #pen-info #info {
            left: -11px;
        }
    }

    @media (min-width: 1920px) {
        #pen-info #info {
            left: -23px;
        }
    }

    .comment-counter {
        background-color: rgb(250, 115, 115);
        width: 23px;
        height: 23px;
        border-radius: 30px;
        text-align: center;
        margin-top: 10px;
        position: absolute;
        top: -15px;
        z-index: 98;
        right: -5px;
        color: white;
        font-weight: 600;
    }

    .issue-resolved-overlay {
        background: rgba(0, 137, 82, 0.4) none repeat scroll 0 0;
        box-sizing: border-box;
        color: white;
        font-size: 20px;
        font-weight: 500;
        height: 100%;
        padding-top: 50px;
        position: absolute;
        text-align: center;
        text-transform: uppercase;
        top: 0;
        width: 100%;
        z-index: 987;
        cursor: pointer;
        left: 0;
        right: 0;
    }

    .issuesDetailsListing-enter-active, .issuesDetailsListing-leave-active {
        position: absolute;
        z-index: 978;
        background: #ffffff;
        top: 0;
        min-height: 700px;
        width: 100%;
        transition: transform 0.6s;
    }

    .issuesDetailsListing-enter, .issuesDetailsListing-leave-to {
        transform: translateX(100%);
    }

    .comment_textarea textarea {
        background: transparent;
        border: none;
        resize: none;
        padding-left: 5px;
        color: white;
        font-weight: 400;
        overflow: hidden;
        word-wrap: break-word;
        min-height: 30px;
        max-height: 100px;

    }

    .comment_textarea {
        position: relative;
        min-height: 46px;
        padding-top: 10px;
        width: 80%;
    }

    .right-desc div:nth-child(2) {
        background: rgba(0, 0, 0, 0) linear-gradient(#ddd, #f8f8f8) repeat scroll 0 0;
        border-radius: 4px;
        color: #333;
        min-height: 45px;
        position: relative;
        padding-bottom: 5px;
        padding-right: 5px;
    }

    .left-desc div:nth-child(2) {
        background: rgba(0, 0, 0, 0) linear-gradient(#fe7373, #fd9797) repeat scroll 0 0;
        border-radius: 4px;
        color: #fff;
        min-height: 45px;
        padding-bottom: 5px;
        padding-right: 5px;
    }

    .current_issue_solved {
        margin-top: 0 !important;
        border: none !important;
        border-top: 2px solid #fff !important;
    }

    .left-desc {
        float: right;
        position: relative;

    }

    .right-desc {
        float: left;
        position: relative;
    }

    .left-desc p {
        margin: 0;
        word-wrap: break-word;
        padding: 5px 10px;
    }

    .left-img {
        float: left;
    }

    .right-img {
        float: right;
    }

    .left-img span {
        font-size: 10px;
        font-weight: 500;
        text-align: center;
        display: inline-block;
        margin-bottom: 5px;
    }

    .right-img span {
        font-size: 10px;
        font-weight: 500;
        text-align: center;
        display: inline-block;
        margin-bottom: 5px;
    }

    .right-desc span {
        font-size: 12px;
        clear: both;
        display: inline-block;
        width: 100%;
        text-align: right;
        font-weight: 500;
    }

    .left-desc span {
        font-size: 12px;
        clear: both;
        display: inline-block;
        width: 100%;
        text-align: left;
        font-weight: 500;
    }

    .left-img .el-dropdown {
        float: none;
        margin: 0 auto;
        display: block;
    }

    .right-img .el-dropdown {
        float: none;
        margin: 0 auto;
        display: block;
    }

    .el-icon-more {
        font-size: 19px;
        color: #bbb;
        line-height: 5px;
    }

    .comment-options {
        padding: 0;
    }

    .issue_img_thumb {
        border-radius: 5px;
        float: right;
        margin-right: 0px;
        margin-top: 20px;
        width: 100%;
        height: 220px;
        object-fit: cover;
    }

    .comment_img_thumb {
        border-radius: 5px;
        float: right;
        margin-right: 0px;
        margin-top: 10px;
        width: 100%;
        height: 220px;
        object-fit: cover;
    }

    .option-del-edit {
        background: #333;
        padding: 6px;
        width: 60px;
        border-radius: 3px;
        position: absolute;
        z-index: 987;
        top: 80px;
        left: -5px;
        display: none;
    }

    .option-del-edit ul {
        margin: 0;
    }

    .right-img {
        position: relative;
    }

    .left-img {
        position: relative;
    }

    .option-del-edit a {
        color: white;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
    }

    /*   .option-del-edit::before {
          width: 0;
          height: 0;
          border-left: 10px solid transparent;
          border-right: 10px solid transparent;
          border-bottom: 10px solid #333;
          position: absolute;
          top: -7px;
          left: 13px;
          content: "";
       }*/
    button {
        transition: all 0.3s ease-in-out 0s !important;
        -moz-transition: all 0.3s ease-in-out 0s !important;
        -webkit-transition: all 0.3s ease-in-out 0s !important;
    }

    a {
        transition: all 0.3s ease-in-out 0s !important;
        -moz-transition: all 0.3s ease-in-out 0s !important;
        -webkit-transition: all 0.3s ease-in-out 0s !important;
    }

    label {
        transition: all 0.3s ease-in-out 0s !important;
        -moz-transition: all 0.3s ease-in-out 0s !important;
        -webkit-transition: all 0.3s ease-in-out 0s !important;
    }

    .current_issue_normal button:hover {
        background: rgb(250, 115, 115) !important;
        color: white !important;
    }

    .current_issue_normal .tag:hover {
        cursor: pointer;
        transition: 1s;
        transform: scale(1.2);
    }

    .unresolved-box:hover {
        background: rgb(250, 115, 115) !important;
        color: white !important;
    }

    .unresolved-box:hover span {
        color: white !important;
    }

    .unresolved-box {
        transition: all 0.3s ease-in-out 0s !important;
        -moz-transition: all 0.3s ease-in-out 0s !important;
        -webkit-transition: all 0.3s ease-in-out 0s !important;
        margin: 0 auto;
    }

    .current_issue_normal:hover {
        border: 3px solid #f07f7f;
    }

    .issue-container:hover {
        border: 3px solid #f07f7f;
    }

    .issue-controls {
        float: right;
        position: absolute;
        right: 10px;
        top: 0px;
    }

    .issue-controls-menu::before {
        width: 0;
        height: 0;
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-bottom: 10px solid #333;
        position: absolute;
        top: -7px;
        right: 13px;
        content: "";
    }

    .issue-controls-menu a {
        color: white !important;
        font-size: 13px;
    }

    .issue-controls-menu {
        background: #333 none repeat scroll 0 0;
        border: medium none;
        min-width: 75px;
        position: relative;
        text-align: center;
        top: 45px;
        left: 10px;
        width: 50px !important;
    }

    .issue-controls-menu a:hover {
        background: rgba(0, 0, 0, 0) none repeat scroll 0 0 !important;
        color: rgb(250, 115, 115) !important;
    }

    .comment-controls {
        float: left;
        width: 100%;
        text-align: center;
        margin-top: 10px;
    }

    .text-border {
        margin-top: 0px;
    }

    .comment-controls-menu::before {
        width: 0;
        height: 0;
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-bottom: 10px solid #333;
        position: absolute;
        top: -7px;
        left: 40px;
        content: "";
    }

    .comment-controls-menu a {
        color: white !important;
        font-size: 13px;
    }

    .comment-controls-menu {
        background: #333 none repeat scroll 0 0;
        border: medium none;
        min-width: 75px;
        position: absolute;
        text-align: center;
        top: 10px;
        width: 50px !important;
        left: -30px;
    }

    .comment-controls-menu a:hover {
        background: rgba(0, 0, 0, 0) none repeat scroll 0 0 !important;
        color: #FA5555 !important;
    }

    .tag {
        transition: all 0.4s ease 0s !important;

        border-radius: 45px;
        width: 45px;
        height: 45px;
        padding-top: 8px;
        text-align: center;
        font-size: 17px;
        font-weight: 600;
        background: #ccc;
        box-shadow: 0 4px 7px #ddd;
    }

    .current_issue_active button:hover {
        background: rgb(250, 115, 115) !important;
        color: white !important;
    }

    .current_issue_active .tag:hover {
        cursor: pointer;
        transition: 1s;
        transform: scale(1.2);
    }

    .current_issue_active label:hover {
        background: rgb(250, 115, 115) !important;
    }

    .issue-container {
        border-left: 3px solid transparent;
        border-top: 3px solid transparent;
        border-right: 3px solid transparent;
        border-bottom: 3px solid transparent;
        padding-right: 7px;
        padding-left: 7px;
    }

    div[contenteditable="true"] {
        cursor: pointer;
        overflow-wrap: break-word;
    }

    div[contenteditable="true"]:hover {
        border: 1px solid #22aadd !important;
    }

    div[contenteditable="true"]:focus {
        outline: none;
        overflow-x: hidden;
        border: 1px solid #22aadd !important;
        padding: 0px !important;
        margin-bottom: 3px;
    }

    div#current_issue2 {
        margin-top: 10px;
    }

    .attachment-popup {
        background: transparent;
        position: relative;
        top: 0px;
        width: 100%;
        left: 0;
        text-align: center;
        border: none;
        padding: 0 !important;
        border: 1px solid #ddd;
        border-radius: 3px;
        overflow: hidden;
    }

    .attachment-popup .el-upload-dragger {
        background: transparent;
        height: auto;
    }

    .attachment-popup div {
        background: none !important;
    }

    .delete-confirm div {
        background: transparent !important;
    }

    .delete-confirm {
        background: transparent;
        position: relative;
        top: 0px;
        width: 100%;
        left: 0;
        border: none;
        padding: 10px 0px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 3px;
    }

    .delete-confirm button {
        text-transform: uppercase;
        border-radius: 2px;
        padding: 8px 9px;
    }

    .attachment-popup button {
        border-radius: 2px;
        padding: 8px 9px;
        margin-bottom: 7px;
    }

    .delete-color {
        background: rgb(250, 115, 115);
        border: none;
    }

    .delete-confirm button span {
        font-weight: bold !important;
        text-align: center;
    }

    .delete-confirm-comment {
        background: rgb(250, 115, 115);
        position: relative;
        top: 0;
        width: 100%;
        left: 0;
        padding: 10px 0px;
        text-align: center;
        border-radius: 3px;
    }

    ul.el-upload-list {
        float: left;
        margin-bottom: 10px;
    }

    .el-upload-list__item-name {
        color: #F8F8FF;
    }

    .el-upload-list__item:hover .el-upload-list__item-name {
        color: #545c64;
    }

    .delete-confirm-comment button {
        padding: 10px 16px;
        color: #333;
        text-align: center;
        border-radius: 2px;
        text-transform: uppercase;
        width: 42%;
    }

    .delete-confirm button span {
        font-weight: bold !important;
        text-align: center;
    }

    .delete-confirm-comment div {
        padding: 0 !important;
        min-height: auto !important;
    }

    .delete-confirm-comment span {
        text-align: center;
    }

    .delete-confirm span {
        text-align: center;
    }

    .attachment-popup span {
        text-align: center;
    }

    .delete-red {
        background: rgb(250, 115, 115);
        border: none;
    }

    .yes-delete {
        background: white;
        border: rgb(250, 115, 115);
    }

    .right-desc p {
        margin: 0;
        word-wrap: break-word;
        padding: 5px 10px;
    }

    textarea.el-textarea__inner {
        resize: none;
    }

    .text-border textarea {
        border: 1px solid #ddd !important;
        border-radius: 0;
        overflow: hidden;
        word-wrap: break-word;
        min-height: 30px;
    }

    .issue-del {
        border: none;
    }

    .text-border textarea:focus {
        border: 1px solid #22aadd !important;
    }

    .current_issue_normal label span:hover {
        color: white !important;
    }

    .comment_attachm {
        padding: 0 !important;
        background: none !important;
        float: left;
        width: 100%;
    }

    .issue-container:hover .issue-controls {
        display: block;
    }

    .issue-controls {
        display: none;
    }

    .issue-container {
        border-bottom: 3px solid #eee;
    }

    .current_issue_active:hover .issue-controls {
        display: block;
    }

    .delete-confirm-comment button:hover {
        background: white;
        color: rgb(250, 115, 115);
    }

    .new-issue-attach {
        border: none;
    }

    .issue-listing {
        /*position: absolute;*/
        z-index: -1;
        height: 92vh;
        overflow-y: auto;
        width: 100%;
    }

    .listing-with-progress-bar {
        height: calc(100vh - 118px) !important;
    }

    .web-progress {
        z-index: -1;
        width: 100%;
        background: #fff;
        border-top: 2px solid #eee
    }

    .issue-thread {
        position: absolute;
        z-index: 99999999;
        height: 92vh;
        background: white;
        overflow-y: auto;
        width: 100%;
        bottom: 0;
        top: 0;
    }

    .error_textarea {
        border: 1px solid red;
        border-radius: 0px;
        margin-top: 10px;
        padding: 0;
    }

    .el-upload-list__item:first-child {
        margin-top: 0px;
    }

    .el-icon-more:hover {
        color: #999 !important;
    }

    .comment_attachm:hover::after {
        background: rgba(0, 0, 0, 0.3) none repeat scroll 0 0;
        bottom: 0;
        content: "";
        left: 0;
        position: absolute;
        right: 0;
        bottom: 0;
        height: 220px;
        visibility: visible;
        width: 100%;
        z-index: 12;
    }

    .new-issue-img:hover::after {
        background: rgba(0, 0, 0, 0.3) none repeat scroll 0 0;
        bottom: 0;
        content: "";
        left: 0;
        position: absolute;
        right: 0;
        bottom: 0;
        height: 220px;
        visibility: visible;
        width: 100%;
        z-index: 12;
    }

    .new-issue-img .download-link {
        position: absolute;
        bottom: 0;
        left: 10px;
        color: white;
        z-index: 987654;
        font-size: 33px;
    }

    .comment-space {
        margin-left: 10px;
    }

    .attachment-popup .el-upload-dragger.is-dragover::after {
        background: rgba(250, 115, 115, 0.5) !important;
        width: 100% !important;
        left: 0 !important;
        right: 0;
        position: absolute;
        border-radius: 2px;
        height: 300px;
        z-index: 12;
        content: "";
        top: 0;
        bottom: 0;
    }

    .attachment-popup .el-upload-dragger {
        overflow: visible;
    }

    .attachment-popup .el-upload {
        display: block;
    }

    .attachment-popup .is-dragover .el-icon-upload {
        color: white;
        position: relative;
        z-index: 98754;
    }

    .attachment-popup .el-upload-list__item.is-ready {
        z-index: 9;
    }

    /* @media all and (max-width: 991px) {

    #rightSidebar {
       width: 40% !important;
    }

    }


    @media all and (max-width: 567px) {

    #rightSidebar {
       width: 100% !important;
    }

    } */
    @media all and (max-width: 414px) {

        .el-dialog {
            width: 90% !important;
        }

    }

    .el-slider__button-wrapper {
        z-index: 0;
    }

    .toggle-button {
        width: 40px;
        height: 40px;
        text-align: center;
        font-size: 22px;
        border: 1px solid #cacbcc;
        color: #626364;
        outline: none;
        background-color: #fff;
        position: absolute;
    }

    .toggle-button:hover {
        border: none;
        color: #fff;
        outline: none;
        background-color: #fa5555;
    }

    #rightSidebar .toggle-button {
        left: -40px;
    }

    #leftSidebar .toggle-button {
        top: 0;
        right: -40px;
    }

    #main.right-expanded {
        right: 0;
    }

    #main.left-expanded {
        left: 0;
    }

    .user-avatar {
        width: 17%;
    }

    .comment-input {
        width: 75%;
    }

    .navbar-dashboard {
        width: 200px;
        position: absolute;
        height: initial;
    }

    .navbar-main {
        margin-left: 200px;
        margin-right: 300px;
    }

    @media (max-width: 750px) {
        #rightSidebar {
            width: 100%;
        }

        .navbar-dashboard {
            display: none;
        }

        .navbar-main {
            margin-left: 0;
            margin-right: 0;
        }
    }

    .btn-backtolist {
        float: left;
        background: rgb(250, 115, 115);
        margin-left: 0px;
        border: none;
        color: #fff;
        font-size: 18px;
        font-weight: 600;
        padding: 12px 14px;
        width: 45px;
        height: 45px;
        position: relative;
        z-index: 9;
        transition: 2s;
    }

    .btn-issue-details {
        float: left;
        background: #eee;
        margin-left: 0px;
        border: none;
        color: #333;
        font-size: 18px;
        font-weight: 600;
        padding: 12px 14px;
        height: 45px;
        width: 45px;
        position: relative;
        z-index: 9;
        padding: 0;
    }

    .btn-issue-details img {
        display: none;
        margin: auto;
    }

    .issue-container:hover .btn-issue-details img {
        display: block;
    }

    .issue-container:hover .btn-issue-details {
        background-color: rgb(250, 115, 115);
    }

    .issue-container:hover .btn-issue-details i {
        display: none;
    }

    button#snap {
        width: 48%;
        color: #fff;
        background: #F25265;
        height: 60px;
        float: left;
    }

    button#snap-video {
        width: 48%;
        color: #fff;
        background: #F25265;
        height: 60px;
        float: right;
    }

    .btn-holder {
        display: flex;
        display: -ms-flexbox;
        flex-direction: row;
        -ms-flex-direction: row;
    }

    #ProofloTakeScreenshotFromVideo {
        display: flex;
        margin-left: 15px;
        margin-top: 10px;
        padding: 0 15px;
        width: 80%;
        margin: 15px auto 0;
    }

    .btn-pink {
        background-color: #f45264;
        width: 50%;
        height: 70px;
        border-radius: 5px;
        display: flex;
        display: -ms-flexbox;
        flex-direction: row;
        -ms-flex-direction: row;
        align-items: center;
        text-transform: uppercase;
        color: #fff;
        text-decoration: none;
        padding: 10px 30px;
        font-weight: bold;
    }

    .btn-pink span {
        width: 100%;
        text-align: center;
    }

    .btn-pink:hover {
        opacity: 0.8;
    }

    .btn-pink + .btn-pink {
        margin-left: 25px;
    }

    .btn-pink {
        border: 0;
    }

    .btn-gray {
        background-color: #808080;
        width: 50%;
        height: 70px;
        border-radius: 5px;
        display: flex;
        display: -ms-flexbox;
        flex-direction: row;
        -ms-flex-direction: row;
        align-items: center;
        text-transform: uppercase;
        color: #fff;
        text-decoration: none;
        font-weight: bold;
        margin-right: 10px;
    }

    .btn-gray span {
        width: 100%;
        text-align: center;
    }

    .btn-gray:hover {
        opacity: 0.8;
    }

    .btn-gray {
        border: 0;
    }

    #resumeVideoImage img {
        margin: 0 auto;
    }

    #VideoProofloUrlStreaming {
        width: 80%;
        margin-top: 42px;
        padding: 0px 15px 0px 15px;
        margin: 42px auto 0;
        display: flex;
    }

    .captured-image {
        position: absolute;
        bottom: 0;
        font-size: 17px;
        font-weight: bold;
        background: #f07f7f;
        padding: 0 10px;
        color: #fff;
        z-index: 99;
    }

    .navtop-button {
        width: initial;
        margin-right: 20px;
    }

    .navtop-button:last-child {
        margin-right: 0;
    }

    .tool-button {
        width: initial;
    }

    .tool-button.extra {
        margin-left: 20px;
    }

    .tool-button.extra button:hover {
        border: none;
        color: #fff;
        outline: none;
        background-color: #fa5555;
    }

    .zoomtool-bar {
        width: 80px;
    }

    .web-progress-bar {
        height: 100%;
        position: absolute;
        width: 300px;
        right: 0;
    }

    @media (max-width: 750px) {
        .web-progress-bar {
            width: 100%;
            position: relative;
        }
    }

    .buttons-bar {
        background: rgb(224, 223, 223);
        padding-top: 10px;
        padding-bottom: 10px;
        z-index: 300;
    }

    .resolved-checkbox {
        position: absolute;
        top: calc(50% - 10px);
        left: calc(50% - 10px);
        z-index: 999;
    }

    .resolved-checkbox .el-checkbox__inner {
        border-radius: 50%;
        width: 25px;
        height: 25px;
    }

    .resolved-checkbox.is-checked .el-checkbox__inner {
        background-color: white;
    }

    .resolved-checkbox.is-checked .el-checkbox__inner::after {
        border-color: #80B4A0;
        height: 12px;
        width: 5px;
        top: 3px;
        left: 7px;
        border-width: 3px;
    }

    .resolved-checkbox:hover {
        background-color: transparent;
    }

    .thumbTovideo {
        margin: 10px;
    }

    .playThumbTovideoBtn {
        width: 100%;
        border: none;
        background-color: #808080;
        height: 60px;
    }

    .pauseThumbTovideoBtn {
        width: 100%;
        border: none;
        background-color: #f45264;
        height: 60px;
    }

    .playThumbTovideoBtn img, .pauseThumbTovideoBtn img {
        max-height: 58px;
    }

    #playVideoImage {
        display: none;
    }

    #pauseVideoImage img {
        margin: 0 auto;
    }

    .playThumbTovideoBtn {
        display: none;
    }

    #clickFromExt {
        visibility: hidden;
    }

    #captureWrapper {
        display: none;
    }

    body {
        background: #fff;
    }

    .video_info_link_modal {
        position: absolute;
        top: 0;
        left: 87px;
        font-style: italic;
    }

    .video_info_link_modal a {
        font-weight: bold;
    }

    .upload-container .el-card__body {
        padding: unset !important;
    }

    .computer-col, .plugin-col {
        position: relative;
        text-align: center;
        padding: 30px 0;
    }

    .files-rollover {
        top: 0;
        left: 0
    }

    .plus-circle {
        padding: 10px 15px;
        background: #80B4A0;
        color: #fff;
        font-size: 18px;
        border-radius: 50%;
        font-weight: bold;
    }

    .plugin-col {
        border-right: 1px solid #d6e7e0;
    }

    .plugin-col:hover .files-rollover {
        opacity: 1;
    }

    .computer-col:hover .files-rollover {
        opacity: 1;
    }

    .not-installed {
        background: rgb(250, 85, 85);
        color: #fff;
    }

    .installed {

    }

    .computer-col {
        border-left: 1px solid #d6e7e0;
    }

    .plugin-logo {
        height: 50px;
        width: 50px;
        border-radius: 10px;
        padding: 5px;
        margin: 0 auto;
    }

    .logo-white {
        background-color: #fff;
    }

    .logo-gray {
        background-color: rgb(238, 238, 238);
    }

    .plugin-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
</style>
