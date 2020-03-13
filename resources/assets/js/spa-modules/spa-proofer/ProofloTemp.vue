<template>
  <div v-loading.fullscreen.lock="fullscreenLoading">
    <el-row class="top-bar-full">
      <el-col :lg="1">
        <div v-if="!project_hash" class="grid-content bg-back-btn" @click="goBack" style="cursor: pointer">
          <i class="el-icon-back" style="font-size: 30px; font-weight: 900; color: #5a5555af; margin-left: 25%; margin-top: 20%"></i>
        </div>
      </el-col>
      <el-col :lg="3">
        <div style="text-align: left">
          <el-select v-model="value" placeholder="V" style="width: 80px; margin-top: 5%; margin-left: 5%;">
            <el-option v-for="item in versions" :key="item.value" :label="item.label" :value="item.value">
            </el-option>
          </el-select>
          <el-radio-group size="mini" v-model="isCollapse" style="margin-left: 5px">
            <el-radio-button :label="true">|</el-radio-button>
            <el-radio-button :label="false">|</el-radio-button>
          </el-radio-group>
        </div>
      </el-col>
      <el-col :lg="10" :offset="6">
        <div style="margin-top: 10px">
          <el-button v-if="current_rol == 'freelancer'" style="background-color: #fa5555; color: #fff; font-weight: bold; border: 0px"
            @click="sendRevision()" type="primary">Send back for revision
          </el-button>
          <el-button v-if="current_rol == 'client'" style="background-color: #fa5555; color: #fff; font-weight: bold; border: 0px"
            @click="showSubmitOptionsModal()" type="primary">Submit corrections
          </el-button>
          <el-button v-if="current_rol == 'client'" style="background-color: #e0dfdf; color: rgb(87, 86, 86); font-weight: bold; border: 0px"
            @click="sendRevision()" type="primary">Approve design
          </el-button>
        </div>
      </el-col>
      <el-col :lg="4">
        <span style="float: right; margin-top: 15px; margin-right: 15px">
          <img src="/assets/front/img/avatar.jpg" class="img-circle special-img" style="width:40px; height: 40px;">
        </span>
      </el-col>
    </el-row>
    <el-row :gutter="10" class="center-bar-full">
      <el-col :xs="24" :sm="24" :md="5" :lg="4">
        <div v-show="isCollapse" class="grid-content side-menu-left">
          <el-card :id="'proof_'+key" v-for="(proof, key, index) in proofs" :key=key :body-style="{ padding: '0px'}" @click.native="loadThumb(key)"
            :style="{'background-image' : 'url('+ proof.picture_url +')'}" style="background-size: cover; background-repeat: no-repeat; border-radius: 5px; margin-bottom: 5px; min-height: 180px;"
            :class="{'regular-thumb' : !proof.active_thumb, 'active-thumb' : proof.active_thumb}">
            <el-col :xs="4" :sm="2" :md="2" :lg="3" :class="{'regular-thumb-number' : !proof.active_thumb, 'active-thumb-number' : proof.active_thumb}">
              <span style="font-size: 16px; color: #fff">
                <b>{{key+1}}</b>
              </span>
            </el-col>
          </el-card>
        </div>
      </el-col>
      <el-col :xs="24" :sm="18" :md="13" :lg="15">
        <div id="stage-parent" class="grid-content central-content" style="max-height: 100%">
          <el-row type="flex" justify="end">
            <el-col :sm="14">
              <div style="margin-left: 20px">
                <!-- <el-radio-group v-model="decision">
                  <el-radio-button label="pending">Send back revision</el-radio-button>
                  <el-radio-button label="approved">Approve revision</el-radio-button>
                </el-radio-group> -->

                <!-- <template v-if="current_rol == 'client' && showSubmitButtons">
                  <el-select v-model="decision" placeholder="Submit decision" @change="submitDecision()">
                    <el-option :label="'Send new proof'" :value="'approved'">
                      <span style="float: left;font-size: 22px">
                        <i class="el-icon-edit"></i>
                      </span>
                      <span style="color: #8492a6; font-size: 14px; margin-left: 20px">Request new revision</span>
                    </el-option>
                    <el-option :label="'Approved'" :value="'finished'">
                      <span style="float: left; font-size: 22px">
                        <i class="el-icon-check"></i>
                      </span>
                      <span style="color: #8492a6; font-size: 14px; margin-left: 20px">Approve project</span>
                    </el-option>
                  </el-select>
                </template> -->
                <!-- <el-button @click="submitDecision()" type="danger" :disabled="!decision" icon="el-icon-arrow-up">Submit Decision</el-button> -->
              </div>
            </el-col>
            <el-col :sm="2">
              <button v-show="current_rol == 'client'" :class="{'edition-btn-active' : pen_active, 'edition-btn' : !pen_active}" @click="activate_pen()">
                <i class="el-icon-edit icon-center"></i>
              </button>
            </el-col>
            <el-col :sm="2">
              <button v-show="current_rol == 'client'" :class="{'edition-btn-active' : remove_active, 'edition-btn' : !remove_active}"
                @click="deleteIssue()" :disabled="disabled_remove">
                <i class="el-icon-delete icon-center"></i>
              </button>
            </el-col>
            <el-col :sm="1">
              <button :class="{'edition-btn-active' : zoom_out_active, 'edition-btn' : !zoom_out_active}" @mousedown="activate_zoom_in()"
                @mouseup="deactivate_zoom_in()">
                <i class="el-icon-zoom-out icon-center"></i>
              </button>
            </el-col>
            <el-col :sm="4">
              <el-slider v-model="zoom_level" :min="0.2" :max="10" :step="0.1" :height="'2px'" :show-tooltip="false" style="padding-top: 6px">
              </el-slider>
            </el-col>
            <el-col :sm="1">
              <button :class="{'edition-btn-active' : zoom_in_active, 'edition-btn' : !zoom_in_active}" @mousedown="activate_zoom_out()"
                @mouseup="deactivate_zoom_out()">
                <i class="el-icon-zoom-in icon-center"></i>
              </button>
            </el-col>
          </el-row>
          <el-row >
            <el-col>
              <div id="content">
              </div>
            </el-col>
          </el-row>
        </div>
      </el-col>
      <el-col :xs="24" :sm="6" :md="6" :lg="5">
        <div class="grid-content side-menu-right">
          <!--   <el-row justify="end">
            <el-col :xs="24" style="padding-top: 15px; padding-left: 15px">
              <span>
                <img src="/assets/front/img/avatar.jpg" class="img-circle special-img" style="width:50px; height: 50px;padding: 5px">
              </span>
              <span style="padding: 10px">
                <img src="/assets/front/img/avatar.jpg" class="img-circle special-img" style="width:50px; height: 50px;padding: 5px">
              </span>
              <span style="padding: 10px">
                <img src="/assets/front/img/avatar.jpg" class="img-circle special-img" style="width:50px; height: 50px;padding: 5px">
              </span>
            </el-col>
          </el-row> -->
          <template v-if="!showIssueDetails">
            <el-row type="flex" justify="end">
              <el-col>
                <div v-for="(_current_issue, key, index ) in current_proof.issues" :key=key :class="{'current_issue_active' : _current_issue.active_issue, 'current_issue_normal' : !_current_issue.active_issue, 'current_issue_solved' : _current_issue.status == 'solved'}">
                  <div @click="IssueDetails(_current_issue.group)" style="cursor: pointer;">
                    <div v-if="_current_issue.status == 'solved'" style="color: #044921; text-align: center; font-weight: bold; padding: 5px">This issue has been resolved</div>
                    <div v-else style="color: #da4545;; text-align: center; font-weight: bold; padding: 5px">This issue is pending</div>
                    <el-row style="padding-top: 3%; margin-bottom: 10px">
                      <el-col :xs="5" :md="5">
                        <div style="padding: 10px">
                          <img src="/assets/front/img/avatar.jpg" class="img-circle special-img" style="width: 50px;height: 50px;padding: 2px;border-style: solid;border-color: #a5a3a3;border-width: 1px;">
                        </div>
                      </el-col>
                      <el-col :xs="16" :md="16">
                        <div style="color: #626364; padding-top: 10px">
                          {{_current_issue.description}}
                        </div>
                      </el-col>
                      <el-col :xs="3" :md="3">
                        <div style="color: #626364; padding-top: 10px">
                          <div v-bind:class="{'tag' : _current_issue.status != 'solved', 'tag_solved' : _current_issue.status == 'solved'}" style="border-radius: 50px; width: 30px; height: 30px; padding-top: 6px; text-align: center;font-size:13px">{{_current_issue.label}}</div>
                        </div>
                      </el-col>
                    </el-row>
                    <el-row style="margin-bottom: 0px">
                      <el-col :xs="12" :xl="12">
                        <div v-if="_current_issue.comments" style="margin-left: 15px; margin-top: 5px">
                          <b>{{_current_issue.comments.length}}</b> comments
                        </div>
                      </el-col>
                    </el-row>
                  </div>
                  <el-row type="flex" style="margin-bottom: 0px; margin-top: 0px">
                    <el-col v-if="_current_issue.project_files_id" :xs="8" :xl="8">
                      <el-button @click="showIssueImage(_current_issue.project_files.path, _current_issue.project_files.thumb_path, _current_issue.project_files.id)"
                        type="text" class="issue-button-details" icon="el-icon-picture-outline" style="float: left;margin-left: 15px;"
                        size="mini">
                        <span style="font-size: 14px; ">Image attached</span>
                      </el-button>
                    </el-col>
                    <el-col v-else :xs="8" :xl="8">
                      <el-button v-if="current_rol == 'client' && _current_issue.status != 'solved'" @click="editIssue(_current_issue.id)" type="text"
                        class="issue-button-details" style="float: left;margin-left: 15px" size="mini">
                        <span class="fa fa-paperclip" style="font-size: 20px;"></span>
                        <span style="font-size: 14px;">Add image</span>
                      </el-button>
                    </el-col>
                    <el-col :xs="3" :xl="3" :offset="7">
                      <el-button v-if="current_rol == 'client' && _current_issue.status != 'solved'" @click="setIssueSolved(_current_issue.id, 'solved')"
                        class="issue-button-solved" icon="el-icon-check" size="mini">
                      </el-button>
                    </el-col>
                    <el-col :xs="3" :xl="3">
                      <el-button v-if="current_rol == 'client'  && _current_issue.status != 'solved'" @click="editIssue(_current_issue.id)" class="issue-button-edit"
                        icon="el-icon-edit" size="mini">
                      </el-button>
                    </el-col>
                    <el-col :xs="3" :xl="3">
                      <el-button @click="IssueDetails(_current_issue.group)" class="issue-button-details" icon="el-icon-search" size="mini">
                      </el-button>
                    </el-col>
                  </el-row>
                </div>
              </el-col>
            </el-row>
          </template>
          <template v-else>
            <el-row style="padding: 5px; margin-left: 15px">
              <div @click="issuesList()" style="float: left">
                <i class="el-icon-back icon-center" style="font-size: 20px"></i>
              </div>
            </el-row>
            <el-row type="flex" justify="end" style="margin-bottom: 0px">
              <el-col>
                <div :class="{'current_issue_active' : issue_datils.active_issue, 'current_issue_normal' : !issue_datils.active_issue, 'current_issue_solved' : issue_datils.status == 'solved'}">
                  <div v-if="issue_datils.status == 'solved'" style="color: #044921; text-align: center; font-weight: bold; padding: 5px">This issue has been resolved</div>
                  <div v-else style="color: #da4545;; text-align: center; font-weight: bold; padding: 5px">This issue is pending</div>
                  <el-row style="padding-top: 3%; margin-bottom: 0px">
                    <el-col :md="4">
                      <div style="padding: 10px">
                        <img src="/assets/front/img/avatar.jpg" class="img-circle special-img" style="width: 50px;height: 50px;padding: 2px;border-style: solid;border-color: #a5a3a3; border-width: 1px;">
                      </div>
                    </el-col>
                    <el-col :md="16">
                      <div style="color: #626364; padding-top: 10px">
                        {{issue_datils.description}}
                      </div>
                    </el-col>
                    <el-col :md="4">
                      <div style="color: #626364; padding-top: 10px">
                        <div v-bind:class="{'tag' : issue_datils.status != 'solved', 'tag_solved' : issue_datils.status == 'solved'}" style="border-radius: 50px; width: 30px; height: 30px; padding-top: 6px; text-align: center;font-size:13px">{{issue_datils.label}}</div>
                      </div>
                    </el-col>
                  </el-row>
                  <el-row type="flex" style="margin-bottom: 0px; margin-top: 15px">
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
                  </el-row>
                </div>
              </el-col>
            </el-row>
            <el-row v-if="issue_datils.status != 'solved'" style="padding: 5px; margin-bottom: 0px">
              <el-input v-model="current_comment.description" type="textarea" :autosize="{ minRows: 4, maxRows: 8}" placeholder="Type comment description..."
                v-bind:class="{'error_textarea' : errors.comment_description}">
              </el-input>
              <span v-if="errors.comment_description" style="float: right; color: rgb(211, 12, 12); font-size: 13px">{{errors_msg.comment_description}}</span>
            </el-row>
            <el-row v-if="issue_datils.status != 'solved'" type="flex" justify="end" style="padding-bottom: 0px; margin-bottom: 0px">
              <!--  <el-col :sm="4">
                <el-button style="border: 0px">
                  <i class="fa fa-paperclip fa-2x" style="color: #8f9091; font-size: 22px"></i>
                </el-button>
              </el-col> -->
              <el-col :sm="4">
                <el-button @click="addComment(issue_datils.id)" style="border: 0px">
                  <i class="fa fa-share" style="color: #8f9091; font-size: 22px"></i>
                </el-button>
              </el-col>
            </el-row>
            <el-row style="padding: 5px; margin-bottom: 0px; height: 400px; overflow-y: scroll">
              <div v-for="(comment, key, index ) in issue_datils.comments" :key=key>
                <div class="comment">
                  <el-row style="margin-bottom: 0px">
                    <el-col :md="20">
                      <div>
                        {{comment.description}}
                      </div>
                    </el-col>
                    <el-col :md="4">
                      <div style="padding: 10px">
                        <img src="/assets/front/img/avatar.jpg" class="img-circle special-img" style="width:50px; height: 50px;padding: 5px">
                      </div>
                    </el-col>
                  </el-row>
                  <el-row type="flex" style="margin-bottom: 0px; margin-left: 0px">
                    <el-col v-if="comment.project_files_id" :xs="8" :xl="8">
                      <el-button @click="showIssueImage(comment.project_files.path, comment.project_files.thumb_path, comment.project_files.id)"
                        type="text" class="issue-button-details" icon="el-icon-picture-outline" style="float: left" size="mini">
                        <span style="font-size: 14px;">Image attached</span>
                      </el-button>
                    </el-col>
                    <el-col v-else :xs="8" :xl="8">
                      <el-button v-if="issue_datils.status != 'solved'" @click="editComment(comment.issue_id, comment.id)" type="text" class="issue-button-details"
                        style="float: left" size="mini">
                        <span class="fa fa-paperclip" style="font-size: 20px;"></span>
                        <span style="font-size: 14px;">Add image</span>
                      </el-button>
                    </el-col>
                    <el-col :xs="3" :xl="3" :offset="10">
                      <el-button v-if="issue_datils.status != 'solved'" @click="editComment(comment.issue_id, comment.id)" class="issue-button-details"
                        icon="el-icon-edit" size="mini">
                      </el-button>
                    </el-col>
                    <el-col :xs="3" :xl="3">
                      <el-button v-if="issue_datils.status != 'solved'" @click="deleteComment(issue_datils.id, comment.id)" class="issue-button-edit"
                        icon="el-icon-delete" size="mini">
                      </el-button>
                    </el-col>
                  </el-row>
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
          </template>
        </div>
      </el-col>
    </el-row>
    <el-row class="top-bar-full footer">
      <el-col :xl="4" :offset="20">
        <!--  <div class="grid-content bg-top-bar"> -->
        <div class="block">
          <!--  <el-slider v-model="zoom_level" show-input :min="0.1" :max="10" :step="0.1">
          </el-slider> -->
        </div>
        <!-- </div> -->
      </el-col>
    </el-row>

    <el-dialog title="New issue" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" :visible.sync="showIssueDialog"
      @open="onOpenDialogNewIssue" @close="onCloseDialogNewIssue" width="30%" center>
      <el-input type="textarea" :rows="5" placeholder="Type issue description..." v-model="new_issue.description" v-bind:class="{'error_textarea' : errors.issue_description}">
      </el-input>
      <span v-if="errors.issue_description" style="float: right; color: rgb(211, 12, 12); font-size: 13px">{{errors_msg.issue_description}}</span>
      <span slot="footer" class="dialog-footer">
        <el-button @click="cancelIssue()">Cancel</el-button>
        <el-button type="primary" @click="addIssue()">Save</el-button>
      </span>
    </el-dialog>

    <el-dialog title="Edit issue" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" :visible.sync="showEditableIssueDialog"
      @open="onOpenDialogEditIssue" @close="onCloseDialogEditIssue" width="30%" center>
      <el-input type="textarea" :rows="5" v-model="editable_issue.description" v-bind:class="{'error_textarea' : errors.editable_issue_description}">
      </el-input>
      <span v-if="errors.editable_issue_description" style="float: right; color: rgb(211, 12, 12); font-size: 13px">{{errors_msg.editable_issue_description}}</span>
      </el-input>
      <span slot="footer" class="dialog-footer">
        <el-button @click="cancelEditableIssue()">Cancel</el-button>
        <el-button type="primary" @click="updateIssue()">Save</el-button>
      </span>
      <el-row v-if="!editable_issue.project_files_id">
        <el-col style="text-align: center">
          <div class="uploader" style="width: 100%; margin-top: 15px">
            <el-upload ref="upload" :data="imageFormData" :headers="headers" action="/api/files/upload" :on-preview="handlePreview" :on-error="handleError"
              :on-remove="deleteFile" :on-success="handleSuccess" :auto-upload="true" :file-list="fileList" name="photos" list-type="picture">
              <i class="el-icon-upload"></i>
              <div class="el-upload__text">Drop file here or
                <em>click to upload</em>
              </div>
            </el-upload>
          </div>
        </el-col>
      </el-row>
    </el-dialog>

    <el-dialog title="Edit comment" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false" :visible.sync="showEditableCommentDialog"
      @open="onOpenDialogEditComment" @close="onCloseDialogEditComment" width="30%" center>
      <el-input type="textarea" :rows="5" v-model="editable_comment.description" v-bind:class="{'error_textarea' : errors.editable_comment_description}">
      </el-input>
      <span v-if="errors.editable_comment_description" style="float: right; color: rgb(211, 12, 12); font-size: 13px">{{errors_msg.editable_comment_description}}</span>
      </el-input>
      </el-input>
      <span slot="footer" class="dialog-footer">
        <el-button @click="cancelEditableComment()">Cancel</el-button>
        <el-button type="primary" @click="updateComment()">Save</el-button>
      </span>
      <el-row v-if="!editable_comment.project_files_id">
        <el-col style="text-align: center">
          <div class="uploader" style="width: 100%">
            <el-upload ref="upload" drag :data="imageFormData" :headers="headers" action="/api/files/upload" :on-preview="handlePreview"
              :on-error="handleError" :on-remove="deleteFile" :on-success="handleSuccess" :auto-upload="true" :file-list="fileList"
              name="photos" list-type="picture">
              <i class="el-icon-upload"></i>
              <div class="el-upload__text">Drop file here or
                <em>click to upload</em>
              </div>
            </el-upload>
          </div>
        </el-col>
      </el-row>
    </el-dialog>

    <el-dialog :close-on-click-modal="true" :close-on-press-escape="true" :show-close="true" :visible.sync="showIssueImageFlag"
      width="60%" center>
      <el-row>
        <el-col :xs="4">
          <div style="padding: 15px">
            <span v-if="current_rol == 'client'  && current_issue.status != 'solved'" @click="deletePicture(image_id)" class="fa fa-trash"
              style="font-size: 24px; cursor: pointer"></span>
          </div>
        </el-col>
        <el-col :xs="20">
          <img :src="image_preview" style="width: 100%; height: auto">
        </el-col>
      </el-row>
    </el-dialog>
    <el-dialog :close-on-click-modal="true" :close-on-press-escape="true" :show-close="true" :visible.sync="showSubmitOptions"
      width="40%" center>
      <el-row style="text-align: center">
        <el-col :lg="24">
          <span style="font-size: 28px">Bob, please make your decition below.</span>
        </el-col>
      </el-row>
      <el-row style="text-align: center">
        <el-col :lg="24">
          <span style="font-size: 18px">Are these your corrections
            <span style="color: #24a158">for revision #"1"?</span>
          </span>
        </el-col>
      </el-row>
      <el-row style="text-align: center">
        <el-col :lg="12">
          <el-button style="background-color: #fa5555; color: #fff; font-weight: bold; border: 0px" @click="sendRevision()" type="primary">Yes, submit corrections
          </el-button>
        </el-col>
        <el-col :lg="12">
          <el-button style="background-color: rgb(224, 223, 223); color: rgb(87, 86, 86); font-weight: bold; border: 0px" @click=""
            type="primary">Cancel
          </el-button>
        </el-col>
      </el-row>
    </el-dialog>
  </div>
</template>

<script>
/*   import Ls from "../../services/ls"; */
  import Konva from "konva";
/*   import Auth from '../../services/auth' */
  mixins: [Konva];
  export default {
    props: ["project_id", "revision_id", "project_hash"],
    data() {
      return {
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

        versions: [],
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
        posStart: 0,
        posNow: 0,
        headers: {},
        imageFormData: {
          project_id: "",
          file_type: "",
          owner_type: ""
        },
        fileList: [],
        image_preview: "",
        image_thumb: "",
        image_id: "",
        showIssueImageFlag: false,
        disabled_remove: false,
        decision: '',
        current_rol: '',
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
        isCollapse: true
      };
    },
    methods: {
      initializeSatge(img_url, image) {
        var self = this;
        self.group_counter = 1;
        this.showIssueDetails = false;

        if (self.current_proof.canvas_data) {
          self.stage = Konva.Node.create(self.current_proof.canvas_data, "content");
          self.background = self.stage.getChildren()[0];
          self.background.clearBeforeDraw(true);

          self.background_group = self.background.getChildren()[0];
          self.background_group.draggable(true);
          this.resetPosition();

          var imageObject = new Image();
          imageObject.onload = function () {
            self.background_image = new Konva.Image({
              x: 0,
              y: 0,
              image: imageObject,
              draggable: false
            });

            self.background_image.on("click tap", function () {
              self.drawCircle();
            });

            self.background_group.add(self.background_image);
            self.background_image.moveToBottom();
            self.background.draw();
          }

          imageObject.src = img_url;

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
            container: "content",
            width: self.initialStageWidth,
            height: self.initialStageWidth
          });

          self.background = new Konva.Layer({
            clearBeforeDraw: true
          });

          self.background_group = new Konva.Group({
            id: 'background_group',
            name: 'background_group',
            draggable: true,
            x: 0,
            y: 0
          });

          var imageObject = new Image();
          imageObject.onload = function () {
            self.background_image = new Konva.Image({
              x: 0,
              y: 0,
              image: imageObject
            });

            self.background_group.on("dragend", function (event) {
              self.draggedDistance = event;
              console.log(self.draggedDistance);
            });

            self.background_group.on("click tap", function (event) {
              self.drawCircle();
            });

            self.stage.on('contentContextmenu', function (e) {

            });

            self.background_group.add(self.background_image);
            self.background.add(self.background_group);
            self.background_image.moveToTop();
            self.stage.add(self.background);
            /* self.saveCanvasState(); */
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
        var self = this;
        if (this.pen_active) {
          var group = new Konva.Group({
            id: "group_" + self.group_counter,
            label: self.group_counter,
            counter: self.group_counter,
            x: self.stage.getPointerPosition().x,
            y: self.stage.getPointerPosition().y,
            draggable: true
          });

          var circle = new Konva.Circle({
            id: "circle_" + group.getAttr("id"),
            group_id: group.getAttr("id"),
            counter: group.getAttr("counter"),
            radius: 20,
            fill: "#f07f7f",
            stroke: "#f76565",
            strokeWidth: 2
          });

          var text = new Konva.Text({
            id: "text_" + group.getAttr("id"),
            group_id: group.getAttr("id"),
            counter: group.getAttr("counter"),
            text: self.group_counter,
            fontSize: 15,
            fontFamily: "Arial",
            fill: "white",
            offsetX: 7,
            offsetY: 8
          });

          this.current_group_id = group.getAttr("id");
          this.current_group_label = group.getAttr("label");
          this.resetZomm();
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
            self.activate_remove();
          });

          this.background_group.add(group);
          this.background.add(this.background_group);
          this.stage.add(this.background);
          this.showIssueDialog = true;
        }
      },

      loadThumb(index) {
        var self = this;
        this.showSubmitButtons = true;
        self.current_proof = self.proofs[index];
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
        self.initializeSatge(img_url);
      },
      saveCanvasState() {
        this.resetZomm();
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

      deleteIssue() {
        var self = this;
        self.showIssueDialog = false;
        swal({
          title: "Are you sure?",
          text: "You won't be able to revert this!",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then(result => {
          if (result.value) {
            axios
              .delete("/api/proof/delete_issue/" + self.current_issue.id)
              .then(response => {
                if (response.data.status == 1) {
                  self.current_proof.issues.forEach(function (item, index) {
                    if (item.id == self.current_issue.id) {
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
        });
      },
      deleteIssueById(issue_id) { },
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
      addIssue() {
        this.reset_errors();
        if (this.new_issue.description != '') {
          var self = this;
          var formData = {
            proof_id: this.current_proof.id,
            description: this.new_issue.description,
            group: this.current_group_id,
            label: this.current_group_label
          };
          if (this.new_issue.id > 0) {
            formData.id = this.new_issue.id;
          }

          this.fullscreenLoading = true;
          axios
            .post("/api/proof/create_issue", formData)
            .then(response => {
              if (response.data.status == 1) {

                var issue = {
                  id: response.data.data.id,
                  proof_id: response.data.data.proof_id,
                  comments: response.data.data.comments,
                  status: response.data.data.status,
                  user_id: response.data.data.user_id,
                  description: response.data.data.description,
                  group: this.current_group_id,
                  label: this.current_group_label,
                  active_issue: true
                };
                this.fullscreenLoading = false;
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
                this.showIssueDialog = false;

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
      updateIssue() {
        this.reset_errors();
        if (this.editable_issue.description != '') {
          var self = this;
          var formData = {
            id: this.editable_issue.id,
            description: this.editable_issue.description
          };
          this.fullscreenLoading = true;
          axios
            .post("/api/proof/create_issue", formData)
            .then(response => {
              if (response.data.status == 1) {
                var issue = {
                  id: response.data.data.id,
                  proof_id: response.data.data.proof_id,
                  comments: response.data.data.comments,
                  user_id: response.data.data.user_id,
                  description: response.data.data.description,
                  project_files_id: response.data.data.project_files_id,
                  project_files: response.data.data.project_files,
                  group: response.data.data.group,
                  label: response.data.data.label,
                  active_issue: true
                };
                self.fullscreenLoading = false;
                self.current_proof.issues.forEach(function (item, index) {
                  if (item.id == self.editable_issue.id) {
                    issue.comments = item.comments;
                    self.current_proof.issues.splice(index, 1, issue);
                  }
                });
                self.activate_issue(this.current_issue.id);
                self.issuesList();
                self.resetEditableIssue();
                self.showEditableIssueDialog = false;
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
          if (this.editable_issue.description == '') {
            this.errors.editable_issue_description = true;
            this.errors_msg.editable_issue_description = 'Issue description is required';
          }
        }

      },

      updateComment() {
        this.reset_errors();
        if (this.editable_comment.description != '') {
          var self = this;
          var formData = {
            id: this.editable_comment.id,
            issue_id: this.editable_comment.issue_id,
            description: this.editable_comment.description
          };
          this.fullscreenLoading = true;
          axios
            .post("/api/proof/add_comment", formData)
            .then(response => {
              if (response.data.status == 1) {
                var comment = {
                  id: response.data.data.id,
                  issue_id: response.data.data.issue_id,
                  project_files_id: response.data.data.project_files_id,
                  user_id: response.data.data.user_id,
                  description: response.data.data.description,
                  project_file_id: response.data.data.project_file_id,
                  project_files: response.data.data.project_files
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
            issue_id: issue_id,
            description: self.current_comment.description
          };
          self.fullscreenLoading = true;
          axios
            .post("/api/proof/add_comment", formData)
            .then(response => {
              if (response.data.status == 1) {
                var comment = {
                  id: response.data.data.id,
                  issue_id: response.data.data.issue_id,
                  user_id: response.data.data.user_id,
                  description: response.data.data.description
                };
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
      showIssueImage(path, thumb, id) {
        this.image_preview = "/storage/" + path;
        this.image_thumb = thumb;
        this.image_id = id;
        this.showIssueImageFlag = true;
      },
      showSubmitOptionsModal() {
        this.showSubmitOptions = true;
      },
      activate_pen() {
        this.pen_active = !this.pen_active;
        this.zoom_in_active = false;
        this.zoom_out_active = false;
        this.remove_active = false;
        this.disabled_remove = true;
      },
      deactivate_zoom_in() {
        this.zoom_out_active = false;
      },
      deactivate_zoom_out() {
        this.zoom_in_active = false;
      },
      activate_zoom_in() {
        this.zoom_level = this.zoom_level - 0.1;
        this.zoom_out_active = true;
        /*this.pen_active = false;
        this.zoom_out_active = false;
        this.remove_active = false;
        this.disabled_remove = true; */
      },
      activate_zoom_out() {
        this.zoom_level = this.zoom_level + 0.1;
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

        this.$router.push("/");
      },
      submitDecision() {
        var self = this;
        var _decision = this.decision;
        this.decision = '';

        if (_decision != '') {
          if (_decision == 'approved') {
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
              var formData = {
                project_id: self.project_id,
                revision_id: self.current_proof.revision_id,
                decision: _decision
              }
              self.fullscreenLoading = true;
              axios
                .post("/api/projects/submit_decision", formData)
                .then(response => {

                  self.fullscreenLoading = false
                  if (response.data.status == 1) {
                    /* this.showSendBackButton = false;
                    this.showSubmitButtons = false; */
                    swal(
                      '',
                      'The decision has been sent by email successfully',
                      'success'
                    )
                  } else {
                    this.handle_error(response.data.errors);
                  }
                })
                .catch(error => {
                  self.handle_error({});
                });
            }
          });
        }
      },
      sendRevision() {
        this.fullscreenLoading = true;
        axios
          .get("/api/projects/sendBackRevision/" + this.project_id + '/' + this.current_rol)
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
      setIssueSolved(issue_id, status) {
        var msg_text = 'You are about to set this issue as resolved.'
        swal({
          title: "Set issue as resolved",
          text: msg_text,
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes"
        }).then(result => {
          if (result.value) {
            self.fullscreenLoading = true;
            axios
              .get("/api/proof/change_issue_status/" + issue_id + '/' + status)
              .then(response => {
                if (response.data.status == 1) {
                  self.fullscreenLoading = false;
                  if (this.current_proof.issues.length > 0) {
                    this.current_proof.issues.forEach(function (item, index) {
                      if (item.id == issue_id) {
                        item.status = status;
                      }
                    });
                  }

                } else {
                  self.handle_error(response.data.errors);
                }
              })
              .catch(error => {
                self.handle_error({});
              });
          }
        });
      },
      cancelIssue() {
        var group = this.background.find("#" + this.current_group_id);
        group.remove();
        this.group_counter--;
        this.background.draw();
        this.saveCanvasState();
        this.resetNewIssue();
        this.showIssueDialog = false;
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
      resetEditableIssue() {
        this.editable_issue.id = "";
        this.editable_issue.description = "";
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
        this.showIssueDetails = false;
        this.imageFormData.owner_type = "issue";
        this.imageFormData.issue_id = this.current_issue.id;
      },
      onOpenDialogEditIssue() {
        this.imageFormData.owner_type = "issue";
        this.imageFormData.issue_id = this.editable_issue.id;
      },
      onOpenDialogEditComment() {
        this.imageFormData.owner_type = "comment";
        this.imageFormData.comment_id = this.editable_comment.id;
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
      IssueDetails(current_group_id) {
        this.current_group_id = current_group_id;
        if (this.current_proof.issues.length > 0) {
          for (var i in this.current_proof.issues) {
            if (this.current_proof.issues[i].group == current_group_id) {
              this.current_issue.id = this.current_proof.issues[i].id;
              this.issue_datils = this.current_proof.issues[i];
              this.issue_datils.active_issue = true;
              if (this.current_proof.issues[i].comments) {
                if (this.current_proof.issues[i].comments.length > 0) {
                  this.current_proof.issues[i].comments.sort(function (a, b) {
                    return b.id - a.id;
                  });
                }
              } else {
                this.current_proof.issues[i].comments = [];
              }
              this.showIssueDetails = true;
            } else {
              this.current_proof.issues[i].active_issue = false;
            }
          }
        }
      },
      issuesList() {
        this.errors.comment_description = false;
        this.showIssueDetails = false;
      },
      deleteComment(issue_id, comment_id) {
        var self = this;
        swal({
          title: "Are you sure?",
          text: "You won't be able to revert this!",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, delete it!"
        }).then(result => {
          if (result.value) {
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
          }
        });
      },

      cancelEditComment() {
        this.showCommentDialog = false;
      },
      loadInitialRevision(project_id, revision_id) {
        var self = this;
        self.fullscreenLoading = true;
        axios
          .get("/api/projects/load/" + project_id + '/' + revision_id)
          .then(response => {
            if (response.data.status == 1) {
              self.versions = response.data.data.versions;
              self.current_review_id = response.data.data.last_revision.id;
              response.data.data.last_revision.proofs.forEach(function (element, index) {
                var proof = {
                  id: element.id,
                  revision_id: element.revision_id,
                  project_id: response.data.data.last_revision.project_id,
                  active_thumb: false,
                  picture_url: "/storage/" + element.project_files.path,
                  thumb_url: "/storage/" + element.project_files.path,
                  canvas_data: element.canvas_data,
                  issues: element.issues
                };
                self.proofs.push(proof);
                self.fullscreenLoading = false;
              });
              setTimeout(function () {
                if (self.proofs.length > 0) {
                  self.loadThumb(0);
                }
              }, 600);
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
          position: "top-center",
          type: "error",
          title: "",
          text: text,
          showConfirmButton: true
        });
      },
      handlePreview() { },
      handleRemove() { },
      handleError(error) {
      },
      handleSuccess(response) {
        toastr["success"](response.message, "Success");
      },
      applyZoom() {
        var self = this;
        self.background_group.scale(
          {
            x: self.zoom_level,
            y: self.zoom_level
          }
        );
        self.background.draw();
      },
      handleOpen(key, keyPath) {
        console.log(key, keyPath);
      },
      handleClose(key, keyPath) {
        console.log(key, keyPath);
      },
      jqueryCssAdjust() {
        $('.el-slider__bar').css('background-color', '#dfe4ed');
        $('.el-slider__button').css('border-color', '#fa5555');
        $('.el-slider__bar').css('border-radius', '0px');
        $('.el-slider__runway').css('border-radius', '0px');
        $('.el-slider__button').css('background-color', 'rgb(250, 85, 85)');
        $('.el-radio-button__inner').css('border-radius', '0px');
        $('.el-radio-button__inner').css('border-color', '#dfe4ed');
        $('.el-radio-button__inner').css('color', '#fff');
        $('.el-radio-button__orig-radio:checked + .el-radio-button__inner').css('background-color', 'rgb(193, 195, 194)');
        $('.el-radio-button__orig-radio:checked + .el-radio-button__inner').css('box-shadow', 'none');
        $('.el-radio-button__orig-radio:checked + .el-radio-button__inner').css('color', 'rgb(193, 195, 194)');
        $('.konvajs-content').css('height', $('#content').height);
      }
    },
    computed: {
      current_proofs_state() {
        return this.$store.state.projects.current_proofs_state;
      }
    },
    components: {},
    watch: {
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
      }
    },
    created() {
      this.imageFormData.file_type = "picture";
      this.imageFormData.project_id = this.project_id;
      this.disabled_remove = true;
    },
    mounted() {
      var self = this;
      if (this.$route.name == 'editor_guest') {
        var loginData = {
          project_hash: this.project_hash
        }
        Auth.loginByUserId(loginData).then(() => {
          Auth.getCurrentRol(this.project_id).then((response) => {
            this.current_rol = response;
            this.loadInitialRevision(this.project_id, this.revision_id);
          })
        })
      }
      if (this.$route.name == 'editor') {
        Auth.getCurrentRol(this.project_id).then((response) => {
          this.current_rol = response;
          this.loadInitialRevision(this.project_id, this.revision_id);
        })
      }
      if (this.current_proofs_state.length > 0) {
        if (this.proofs.length > 0) {
          if (this.proofs[0].project_id == this.current_proofs_state[0].project_id) {
            this.proofs = this.current_proofs_state;
          }
        }
      }

      this.$nextTick(function () {
        var container = document.querySelector("#stage-parent");
        this.initialStageWidth = container.offsetWidth;
        this.initialStageHeight = container.offsetHeight;
        this.jqueryCssAdjust();
      })

      const AUTH_TOKEN = Ls.get("auth.token");
      this.headers = {
        Authorization: `Bearer ${AUTH_TOKEN}`,
        Accept: "application/json"
      };
    },
    updated() {
      this.jqueryCssAdjust();
    }
  };
</script>

<style scoped>
  body,
  html {
    max-height: 100% !important;
  }

  #content {
    height: 100% !important
  }

  .icon-center {
    margin-top: 40% !important;
    margin-left: -40% !important;
  }

  .el-row {
    margin-bottom: 20px;
    &:last-child {
      margin-bottom: 0;
    }
  }

  .top-bar-full {
    box-shadow: 1px 1px 0.1px 0px rgba(0, 0, 0, 0.2);
    margin-bottom: 0px;
  }

  .center-bar-full {
    margin-bottom: 0px;
  }

  .el-col {
    border-radius: 4px;
  }

  .bg-top-bar {
    background-color: #f9f9f9;
  }

  .bg-back-btn {
    background: #f3f1f1;
    width: 64px;
    border-right: 1px solid #e6e6e6;
  }

  .bg-purple-light {
    background: #dadcdf;
  }

  .grid-content {
    border-radius: 0px;
    min-height: 60px;
  }

  .row-bg {
    padding: 10px 0;
    background-color: #f9fafc;
  }

  .side-menu-left {
    padding: 10px;
    overflow-y: scroll;
    overflow-x: hidden;
    /* background-color: rgb(92, 81, 194); */
  }

  .side-menu-right {
    height: 100vh;
    overflow-y: scroll;
    overflow-x: hidden;
    border-left: 1px solid #dadbda;
    /* background-color: #044921
 */  }

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
    width: 50px;
    height: 50px;
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
    width: 50px;
    height: 50px;
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
    top: -5px;
    float: left;
    left: -5px;
  }

  .img-circle {
    border-radius: 50%;
  }

  .current_issue_active {
    border-left: 10px solid #f07f7f;
    min-height: 100px;
    width: 100%;
    background-color: #f07f7f18;
    margin-bottom: 15px;
    padding-bottom: 10px;
  }

  .current_issue_normal {
    border-left: 10px solid #cfcece;
    min-height: 100px;
    width: 100%;
    background-color: #b4afaf2c;
    margin-bottom: 15px;
    padding-bottom: 10px;
  }

  .current_issue_solved {
    border-left: 10px solid #0a7738;
    min-height: 100px;
    width: 100%;
    background-color: #26d35131;
    margin-bottom: 15px;
    padding-bottom: 10px;
  }

  .comment {
    border-radius: 10px;
    margin-left: 10px;
    margin-right: 10px;
    width: 100%;
    color: #4d4a4a;
    padding: 10px;
    background-color: #aca7a72c;
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

  #app>div>div.el-dialog__wrapper>div {
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

  .tag {
    background-color: #f07f7f;
    border-color: #fa5555;
    font-size: 16px;
    font-family: Arial, Helvetica, sans-serif;
    color: #fff;
  }

  .tag_solved {
    background-color: darkgreen;
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
  }

  .el-upload-list,
  .el-upload-list--picture {
    width: 100%;
  }

  .el-dialog__body {
    padding-bottom: 10px !important;
    padding-top: 0px;
  }

  #app>div>div:nth-child(7)>div>div.el-dialog__body {
    padding-top: 0px;
  }

  #app>div>div:nth-child(7)>div>div.el-dialog__header {
    padding-top: 0px;
  }

  .el-button--danger:hover,
  .el-button--danger:focus {
    background-color: #fb7777;
    color: #fff;
  }

  .el-radio-button__orig-radio:checked+.el-radio-button__inner {
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

  #app>div>div.center-bar-full.el-row>div.el-col.el-col-24.el-col-xs-24.el-col-sm-18.el-col-md-13.el-col-lg-15>div>div.el-row.is-justify-end.el-row--flex>div.el-col.el-col-24.el-col-sm-16>div>div>div.el-input.el-input--suffix>input {
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

  .el-select .el-input .el-select__caret {
    color: #fff !important;
    opacity: 1;
  }

  .stage-parent {
    width: 100%;
  }

  .el-menu-vertical-demo:not(.el-menu--collapse) {}
</style>