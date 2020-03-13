<template>
    <div v-loading.fullscreen.lock="fullscreenLoading" class="container-fluid"
         style="padding-left: 0px; padding-right: 0px">
        <el-container style="margin-top: 70px;">
            <el-aside style="background-color: #545c64; width:15%; position: fixed; height: 100%;">
                <el-menu default-active="2" class="el-menu-vertical-demo" background-color="#545c64" text-color="#fff"
                         active-text-color="#ffd04b">
                    <el-menu-item index="2" @click="load('/')" :class="{'active_menu' : active_list}">
                        <i class="el-icon-tickets icon-projects"></i>
                        <span class="labels">PROJECT LIST</span>
                        <el-badge v-if="projects.length > 0" :value="projects.length"
                                  class="projects-counts"></el-badge>
                    </el-menu-item>
                    <el-menu-item v-if="is_freelancer.length > 0" index="2" @click="load('draft')"
                                  :class="{'active_menu' : active_draft}">
                        <i class="el-icon-edit-outline icon-draft"></i>
                        <span class="labels">IN DRAFT</span>
                        <el-badge v-if="on_draft.length > 0" :value="on_draft.length"
                                  class="projects-counts"></el-badge>
                    </el-menu-item>
                    <!--In progerss-->
                    <el-menu-item v-if="is_freelancer.length > 0" index="2" @click="load('progress')"
                                  :class="{'active_menu' : active_progress}">
                        <div v-if="getReviewCount(in_progress)" class="review-alert">
                            <i class="fa fa-circle"></i>
                        </div>
                        <el-badge v-if="in_progress.length > 0 && getUnreadComments(in_progress) > 0"
                                  :value="getUnreadComments(in_progress)" class="unread-comments">
                            <i class="el-icon-time icon-progress"></i>
                        </el-badge>
                        <i v-else class="el-icon-time icon-progress"></i>
                        <span class="labels">IN PROGRESS <span class="user-info">(ME)</span></span>
                        <el-badge v-if="in_progress.length > 0" :value="in_progress.length"
                                  class="projects-counts"></el-badge>
                    </el-menu-item>

                    <el-menu-item v-else-if="is_collaborator.length > 0" index="2" @click="load('revision')"
                                  :class="{'active_menu' : active_revision}">
                        <div v-if="getReviewCount(on_revision)" class="review-alert">
                            <i class="fa fa-circle"></i>
                        </div>
                        <el-badge v-if="on_revision.length > 0 && getUnreadComments(on_revision) > 0"
                                  :value="getUnreadComments(on_revision)" class="unread-comments">
                            <i class="el-icon-time icon-progress"></i>
                        </el-badge>
                        <i v-else class="el-icon-time icon-progress"></i>
                        <span class="labels">IN PROGRESS <span class="user-info">(CLIENT)</span></span>
                        <el-badge v-if="on_revision.length > 0" :value="on_revision.length"
                                  class="projects-counts"></el-badge>
                    </el-menu-item>

                    <el-menu-item v-else index="2" @click="load('revision')" :class="{'active_menu' : active_revision}">
                        <div v-if="getReviewCount(on_revision)" class="review-alert">
                            <i class="fa fa-circle"></i>
                        </div>
                        <el-badge v-if="on_revision.length > 0 && getUnreadComments(on_revision) > 0"
                                  :value="getUnreadComments(on_revision)" class="unread-comments">
                            <i class="el-icon-time icon-progress"></i>
                        </el-badge>
                        <i v-else class="el-icon-time icon-progress"></i>
                        <span class="labels">IN PROGRESS <span class="user-info">(ME)</span></span>
                        <el-badge v-if="on_revision.length > 0" :value="on_revision.length"
                                  class="projects-counts"></el-badge>
                    </el-menu-item>

                    <!--In Review-->
                    <el-menu-item v-if="is_freelancer.length > 0" index="2" @click="load('revision')"
                                  :class="{'active_menu' : active_revision}">
                        <div v-if="getReviewCount(on_revision)" class="review-alert">
                            <i class="fa fa-circle"></i>
                        </div>
                        <el-badge v-if="on_revision.length > 0 && getUnreadComments(on_revision) > 0"
                                  :value="getUnreadComments(on_revision)" class="unread-comments">
                            <i class="el-icon-edit icon-progress"></i>
                        </el-badge>
                        <i v-else class="el-icon-edit icon-revision"></i>
                        <span class="labels">IN REVIEW <span class="user-info">(CLIENT)</span></span>
                        <el-badge v-if="on_revision.length > 0" :value="on_revision.length"
                                  class="projects-counts"></el-badge>
                    </el-menu-item>

                    <el-menu-item v-else index="2" @click="load('progress')" :class="{'active_menu' : active_progress}">
                        <div v-if="getReviewCount(in_progress)" class="review-alert">
                            <i class="fa fa-circle"></i>
                        </div>
                        <el-badge v-if="in_progress.length > 0 && getUnreadComments(in_progress) > 0"
                                  :value="getUnreadComments(in_progress)" class="unread-comments">
                            <i class="el-icon-edit icon-progress"></i>
                        </el-badge>
                        <i v-else class="el-icon-edit icon-progress"></i>
                        <span class="labels">IN REVIEW <span class="user-info">(DESIGNER)</span></span>
                        <el-badge v-if="in_progress.length > 0" :value="in_progress.length"
                                  class="projects-counts"></el-badge>
                    </el-menu-item>

                    <!--on Hold-->
                    <el-menu-item index="2" @click="load('hold')" :class="{'active_menu' : active_hold}">
                        <i class="el-icon-refresh icon-hold"></i>
                        <span class="labels">ON HOLD</span>
                        <el-badge v-if="on_hold.length > 0" :value="on_hold.length"
                                  class="projects-counts"></el-badge>
                    </el-menu-item>

                    <el-menu-item index="2" @click="load('approved')" :class="{'active_menu' : active_approved}">
                        <i class="el-icon-check icon-approved"></i>
                        <span class="labels">APPROVED</span>
                        <el-badge v-if="approved.length > 0" :value="approved.length"
                                  class="projects-counts"></el-badge>
                    </el-menu-item>
                    <el-menu-item index="2" @click="load('completed')" :class="{'active_menu' : active_completed}">
                        <i class="el-icon-upload2 icon-completed"></i>
                        <span class="labels">COMPLETED</span>
                        <el-badge v-if="completed.length > 0" :value="completed.length"
                                  class="projects-counts"></el-badge>
                    </el-menu-item>
                </el-menu>
                <hr class="sidebar-divider" v-if="isFreelancer">
                <el-row v-if="ownedTeam" style="margin-top: 20px; padding: 0 20px">
                    <el-row>
                        <el-col class="team-header">
                            <span class="labels">TEAM MEMBERS</span>
                            <el-tag class="invite-btn" @click.native="showMembersDialog">Invite</el-tag>
                        </el-col>
                    </el-row>
                    <el-row type="flex" style="flex-wrap: wrap">
                        <el-col v-if="teamMembers.length > 0" v-for="member in teamMembers" :key="member.id"
                                :xs="5" :md="5" style="margin: 0 5px 5px 0" @click.native="showMemberProjects(member)">
                            <div>
                                <el-tooltip :content="member.name" placement="bottom" effect="light">
                                    <img :src="member.photo_url" class="img-circle special-img team-circle">
                                </el-tooltip>
                            </div>
                        </el-col>
                    </el-row>
                </el-row>
                <el-row v-else-if="isFreelancer">
                    <el-row>
                        <el-col class="team-header new-team">
                            <!--<span class="labels">TEAM</span>-->
                            <a href="/settings#/teams">
                                <el-tag class="create-team-btn">
                                    <el-icon class="el-icon-plus"></el-icon> CREATE TEAM
                                </el-tag>
                                <el-tag class="create-team-btn-mobile">
                                    <img src="/img/create_team.png" alt="create team">
                                </el-tag>
                            </a>
                        </el-col>
                    </el-row>
                </el-row>
                <hr class="sidebar-divider" v-if="isFreelancer">
            </el-aside>
            <el-main style="width:85%; min-height: 100vh; left: 0; margin-left: 15%;">
                <router-view></router-view>
            </el-main>
        </el-container>
        <el-dialog v-if="activePlan" :title="ownedTeam.name" :close-on-click-modal="false" :close-on-press-escape="false"
                   :show-close="true"
                   :visible.sync="showDialogInviteTeamMembers"
                   class="team-dialog"
                   @open="onOpenDialogNewTeamMembers" @close="onCloseDialogNewTeamMembers" center>
            <el-row type="flex" justify="center">
                <el-col>
                    <el-row style="text-align: center">
                        <h3>Invite Team Member</h3>
                        <i style="font-style: italic;">Please add new team Member here by email</i>
                    </el-row>

                    <!--Team members-->
                    <el-row style="margin-top: 20px" type="flex" justify="center">
                        <div>
                            <!--Exiting Members-->
                            <div v-if="teamMembers.length > 0" v-for="member in teamMembers" :key="member.id"
                                 style="margin-top: 10px">
                                <el-row type="flex" align="middle">
                                    <el-col :md="3">
                                        <img :src="member.photo_url"
                                             class="img-circle special-img team-circle">
                                    </el-col>
                                    <el-col :md="18">
                                        <span style="margin-left: 20px">{{member.email}}</span>
                                    </el-col>
                                    <el-col :md="3">
                                        <div>
                                            <el-button @click="removeTeamMember(ownedTeam.id, member.id)" type="danger" style="padding: 10px">
                                                <i class="el-icon-close" style="font-weight: bold"></i>
                                            </el-button>
                                        </div>
                                    </el-col>
                                </el-row>
                            </div>

                            <!--Pending Members-->
                            <div v-if="invitations.length > 0" v-for="invitation in invitations" :key="invitation.id"
                                 style="margin-top: 10px">
                                <el-row type="flex" align="middle">
                                    <el-col :md="3">
                                        <div class="img-circle special-img team-circle"
                                             style="float: left;">
                                            {{invitation.email | pendingFilter}}
                                        </div>
                                    </el-col>
                                    <el-col :md="18">
                                        <span style="margin-left: 20px">{{invitation.email}}</span>
                                        <span class="pending-invite">pending invite</span>
                                    </el-col>
                                    <el-col :md="3">
                                        <div>
                                            <el-button @click="removeTeamMemberInvitation(invitation)" type="danger" style="padding: 10px">
                                                <i class="el-icon-close" style="font-weight: bold"></i>
                                            </el-button>
                                        </div>
                                    </el-col>
                                </el-row>
                            </div>
                        </div>
                    </el-row>

                    <!--New members-->
                    <el-row style="text-align: center; margin-top: 30px" type="flex" justify="center">
                        <el-col :md="12">
                            <el-tag
                                    v-for="newTeamMember in newTeamMembers"
                                    :key="newTeamMember"
                                    :disable-transitions="false"
                                    :closable="true"
                                    @close="handleClose(newTeamMember)"
                                    style="margin: 5px">
                                {{newTeamMember}}
                            </el-tag>
                            <el-row type="flex" justify="center">
                                <el-input
                                        class="input-new-tag"
                                        v-if="inputVisible"
                                        v-model="inputValue"
                                        ref="saveTagInput"
                                        size="mini"
                                        @keyup.enter.native="handleInputNewMember"
                                        @blur="handleInputNewMember"
                                >
                                </el-input>
                                <el-button v-else class="button-new-tag" size="small" @click="showInput">+ New Team Member
                                </el-button>
                            </el-row>
                        </el-col>
                    </el-row>
                </el-col>
            </el-row>
            <el-row style="text-align: center">
                <el-col style="margin-top: 20px">
                    <el-button type="primary" :disabled="inviteDisabled" @click="inviteTeamNewMembers">Invite
                    </el-button>
                    <el-button @click="hideMembersDialog">Close</el-button>
                </el-col>
            </el-row>
            <el-row class="invite-info">
                <h5>
                    <span style="color: #fcbd01; font-weight: bold">{{this.exitingEmails.length + this.pendingEmails.length}} of {{activePlan.teams_members_count - 1}} member invited.</span>
                    <span>Upgrade to add more</span>
                    <el-tag class="upgrade-button">Upgrade</el-tag>
                </h5>
            </el-row>
        </el-dialog>
    </div>
</template>

<script>
    import {mapActions, mapGetters} from "vuex";

    export default {
        data() {
            return {
                active_list: true,
                active_progress: false,
                active_revision: false,
                active_draft: false,
                active_hold: false,
                active_approved: false,
                active_completed: false,
                showDialogInviteTeamMembers: false,
                newTeamMembers: [],
                inputVisible: false,
                inputValue: '',
                fullscreenLoading: false,
                inviteDisabled: false,
            };
        },
        methods: {
            load(status) {
                this.$router.push({path: '/projects'});
                switch (status) {
                    case "/":
                        this.active_list = true;
                        this.active_progress = false;
                        this.active_revision = false;
                        this.active_draft = false;
                        this.active_approved = false;
                        this.active_completed = false;
                        this.$store.commit("set_show_project_by_status", 0);
                        this.$store.commit("set_current_project_listing", 'all');
                        break;
                    case "progress":
                        this.active_list = false;
                        this.active_progress = true;
                        this.active_revision = false;
                        this.active_draft = false;
                        this.active_approved = false;
                        this.active_completed = false;
                        this.$store.commit("set_show_project_by_status", 1);
                        this.$store.commit("set_current_project_listing", 'progress');

                        break;
                    case "revision":
                        this.active_list = false;
                        this.active_progress = false;
                        this.active_revision = true;
                        this.active_draft = false;
                        this.active_approved = false;
                        this.active_completed = false;
                        this.$store.commit("set_show_project_by_status", 2);
                        this.$store.commit("set_current_project_listing", 'revision');
                        break;
                    case "approved":
                        this.active_list = false;
                        this.active_progress = false;
                        this.active_revision = false;
                        this.active_draft = false;
                        this.active_approved = true;
                        this.active_completed = false;
                        this.$store.commit("set_show_project_by_status", 3);
                        this.$store.commit("set_current_project_listing", 'approved');
                        break;
                    case "completed":
                        this.active_list = false;
                        this.active_progress = false;
                        this.active_revision = false;
                        this.active_draft = false;
                        this.active_approved = false;
                        this.active_completed = true;
                        this.$store.commit("set_show_project_by_status", 4);
                        this.$store.commit("set_current_project_listing", 'completed');
                        break;
                    case "draft":
                        this.active_list = false;
                        this.active_progress = false;
                        this.active_revision = false;
                        this.active_draft = true;
                        this.active_approved = false;
                        this.active_completed = false;
                        this.$store.commit("set_show_project_by_status", 5);
                        this.$store.commit("set_current_project_listing", 'draft');
                        break;
                    case "hold":
                        this.active_list = false;
                        this.active_progress = false;
                        this.active_revision = false;
                        this.active_draft = false;
                        this.active_hold = true;
                        this.active_approved = false;
                        this.active_completed = false;
                        this.$store.commit("set_show_project_by_status", 6);
                        this.$store.commit("set_current_project_listing", 'hold');
                        break;
                }
            },
            getUnreadComments(projects) {
                let unread_comments = 0;
                projects.forEach(function (project) {
                    unread_comments += project.unreadComments;
                });

                return unread_comments;
            },
            getReviewCount(projects) {
                if (!projects.length) return;

                let count = 0;
                projects.forEach(project => {
                    project.active_revision.proofs.forEach(proof => {
                        proof.issues.forEach(issue => {
                            if (this.isFreelancer) {
                                if (issue.status == 'todo') count ++;
                            }
                            else {
                                if (issue.status == 'review') count ++;
                            }
                        })
                    });
                });
                return count;
            },
            showMembersDialog() {
                this.showDialogInviteTeamMembers = true;
            },
            hideMembersDialog() {
                this.showDialogInviteTeamMembers = false;
            },
            onOpenDialogNewTeamMembers() {

            },
            onCloseDialogNewTeamMembers() {
                this.newTeamMembers = [];
            },
            showInput() {
                this.inputVisible = true;
                this.inviteDisabled = true;
                this.$nextTick(_ => {
                    this.$refs.saveTagInput.$refs.input.focus();
                });
            },
            handleClose(key, keyPath) {
                this.newTeamMembers.splice(this.newTeamMembers.indexOf(key), 1);
            },
            handleInputNewMember() {
                let inputValue = this.inputValue;
                if (inputValue) {
                    if (this.exitingEmails.length + this.pendingEmails.length + this.newTeamMembers.length < this.activePlan.teams_members_count - 1) {
                        if (inputValue == this.user.email) {
                            toastr['error']('You are the owner of this team', 'Error');
                        } else if (this.newTeamMembers.indexOf(inputValue) == -1 && this.exitingEmails.indexOf(inputValue) == -1 && this.pendingEmails.indexOf(inputValue) == -1) {
                            if (this.isEmail(inputValue)) {
                                this.newTeamMembers.push(inputValue);
                            } else {
                                toastr['error']('Invalid email', 'Error');
                            }
                        } else {
                            toastr['error']('This email is already exist', 'Error');
                        }
                    } else {
                        toastr['error']("Your current plan doesn't allow you to invite more than 5 members", 'Error');
                    }
                }

                this.inputVisible = false;
                this.inviteDisabled = false;
                this.inputValue = '';
            },
            isEmail(string) {
                var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(String(string).toLowerCase())
            },
            inviteTeamNewMembers() {
                var self = this;
                this.fullscreenLoading = true;
                if (this.newTeamMembers.length > 0) {
                    let formData = {
                        team_id: self.ownedTeam.id,
                        emails: self.newTeamMembers
                    };
                    axios.post('api/teams/invite-members', formData)
                        .then(resposne => {
                            this.fullscreenLoading = false;
                            if (resposne.data.status == 1) {
                                self.newTeamMembers = [];
                                toastr['success'](resposne.data.message, 'Success');
                                this.addInvitation(resposne.data.data);
                            } else {
                                toastr['error'](resposne.data.errors, 'Error');
                            }
                        })
                        .catch(error => {
                            this.fullscreenLoading = false;
                            toastr['error']('Something went wrong, please try again later', 'Error');
                        })
                } else {
                    toastr['error']('Please add member email', 'Error');
                    this.fullscreenLoading = false;
                }
            },
            removeTeamMemberInvitation(invitation) {
                this.$confirm('Are you sure you want to remove this invitation?', 'Warning', {
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel',
                    type: 'success',
                    title: ''
                }).then(() => {
                    this.fullscreenLoading = true;
                    axios.delete(`/settings/invitations/${invitation.id}`)
                        .then(response => {
                            this.fullscreenLoading = false;
                            if (response.status) {
                                toastr['success']('Invitation removed successfully', 'Success');
                                this.deleteInvitation(invitation);
                            } else {
                                toastr['error']('Error removing member, try again later', 'Error');
                            }
                        })
                        .catch(error => {
                            this.fullscreenLoading = false;
                            toastr['error']('Something went wrong, try again later', 'Error');
                        })
                })
            },
            removeTeamMember(teamId, memberId) {
                this.$confirm('Are you sure you want to remove this member from team?', 'Warning', {
                    confirmButtonText: 'Confirm',
                    cancelButtonText: 'Cancel',
                    type: 'success',
                    title: ''
                }).then(() => {
                    this.fullscreenLoading = true;
                    axios.delete(`/settings/${Spark.pluralTeamString}/${teamId}/members/${memberId}`)
                        .then(response => {
                            this.fullscreenLoading = false;
                            if (response.status) {
                                toastr['success']('Member removed successfully', 'Success');
                                this.deleteTeamMembers(memberId);
                            } else {
                                toastr['error']('Error removing member, try again later', 'Error');
                            }
                        })
                        .catch(error => {
                            this.fullscreenLoading = false;
                            toastr['error']('Something went wrong, try again later', 'Error');
                        })
                })
            },
            showMemberProjects(member) {
                this.active_list = false;
                this.active_progress = false;
                this.active_revision = false;
                this.active_draft = false;
                this.active_approved = false;
                this.active_completed = false;
                this.$store.commit("set_show_project_by_status", member);
                this.$store.commit("set_current_project_listing", 'member');
                this.loadMemberProjects(member);
            },
            ...mapActions([
                'getActiveSubscription',
                'loadProjects',
                'loadMemberProjects',
                'bootstrap',
                'addInvitation',
                'deleteInvitation',
                'deleteTeamMembers',
            ])
            /*  ...mapActions([
               'bootstrap',
             ]) */
        },
        computed: {
            ...mapGetters([
                "user",
                "isFreelancer",
                "activeSubscription",
                "activePlan",
                "project_listing_type",
                "unread_comments",
                "ownedTeam",
                "teamMembers",
                "exitingEmails",
                "pendingEmails",
                "invitations",
                "membersLimit",
                "is_freelancer",
                "is_collaborator",
                "projects",
                "in_progress",
                "on_revision",
                "approved",
                'completed',
                'on_draft',
                'on_hold',
                'member_projects',
                'current_member',
            ])
        },
        filters: {
            pendingFilter(text) {
                if (!text) return '';
                text = text.toString();
                return text.charAt(0).toUpperCase().substring(0, 1);
            }
        },
        mounted() {
            this.$nextTick(() => {
                /* this.bootstrap() */
            })
        },
        created() {
            this.loadProjects();
            this.getActiveSubscription();
        },
        beforeRouteUpdate: function (to, from, next) {
            this.loadProjects();
            next();
        }
    };
</script>
<style>
    .unread-comments .el-badge__content {
        background-color: rgb(250, 115, 115) !important;
        color: #fff !important;
        border-radius: 10px;
        display: inline-block;
        font-size: 12px;
        height: 20px;
        line-height: 18px;
        padding: 0 6px;
        text-align: center;
        white-space: nowrap;
        border: 1px solid rgb(250, 115, 115);
        z-index: 1;
    }

    .unread-comments .el-badge__content.is-fixed {
        top: 20px;
        left: -10px;
        right: unset !important;
        transform: unset;
        width: 20px;
        top: 10px;
        display: flex;
        justify-content: center;
    }

    .review-alert {
        position: absolute;
        left: 8px;
        font-size: 10px;
        top: 2px;
        left: 38px;
        font-size: 8px;
    }

    .el-menu-vertical-demo li.el-menu-item {
        padding-left: 15px !important;
    }

    .el-menu {
        border-right: 0px !important;
    }

    .with-navbar {
        padding-top: 0px !important;
    }

    .navbar {
        border-bottom: 0px !important;
    }

    .el-menu-vertical-demo .el-badge__content {
        background-color: #fff;
        color: black;
        font-weight: bold;
    }

    .active_menu {
        background-color: #444a50 !important;
    }

    .el-menu-item:focus,
    .el-menu-item:hover {
        background-color: #444a50 !important;
    }

    .labels {
        color: #fff;
        font-size: 12px;
        font-weight: bold;
        position: relative
    }

    .projects-counts {
        float: right;
        padding-top: 5px;
        height: 30px;
    }

    .user-info {
        position: absolute;
        top: -4px;
        left: 0px;
        color: #a5aaaf;
        font-weight: normal;
    }

    @-moz-document url-prefix() {
        .projects-counts {
            height: 46px;
            padding-top: 5px;
            margin-left: -21px;
        }

        .projects-counts .el-badge__content {
            line-height: 16px;
        }
    }

    .team-header {
        display: flex;
        justify-content: space-around;
        align-items: center;
        margin-bottom: 10px
    }

    .new-team {
        margin: unset !important;
    }

    .invite-btn, .create-team-btn {
        margin-left: 15px;
        border-radius: 25px;
        background: #817f7f;
        color: #fff;
        font-weight: bold;
        padding: 0 15px
    }

    .invite-btn:hover, .create-team-btn:hover {
        cursor: pointer;
    }

    .sidebar-divider {
        border: 1px solid rgb(111, 119, 128);
    }

    .create-team-btn {
        margin: unset !important;
    }

    .team-circle {
        font-size: 21px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        margin: 0 5px;
        background: #fff;
    }

    .pending-invite {
        margin-left: 10px;
        font-size: 12px;
        font-style: italic;
        color: #F2A731;
        font-weight: bold
    }

    .invite-info {
        background: rgb(255, 253, 222);
        text-align: center;
        margin-top: 20px;
        padding: 10px 0;
    }

    .upgrade-button {
        background: rgb(252, 189, 1);
        color: rgb(255, 255, 255);
        padding: 0 30px;
        font-size: 13px;
        margin-left: 35px;
    }

    .upgrade-button:hover {
        cursor: pointer;
        background: rgb(255, 216, 99)
    }

    .team-dialog .el-dialog__body {
        padding: unset !important;
    }

    .create-team-btn-mobile {
        display: none;
        background-color: transparent;
        border: none;
    }
    .create-team-btn-mobile img {
        width: 30px;
    }

    @media (max-width: 750px) {
        .team-dialog .el-dialog {
            width: 90%;
        }
        .team-header.new-team .create-team-btn {
            display: none;
        }
        .team-header.new-team .create-team-btn-mobile {
            display: block;
        }
    }
</style>