// system.js
// main module
//----------------------------------

import User from "../../models/user";

import { BOOTSTRAP } from "../mutation-types.js";

/*
 * state
 *
 */

const state = {
	user: {},
	isFreelancer: '',
	isSubscribed: '',
    projects_listing_type: '',
	current_role : '',
	unread_comments: '',
	ownedTeam: {},
	teamMembers: {},
	exitingEmails: [],
	pendingEmails: [],
    invitations: [],
	membersLimit: '',
	clientEmails: [],
	clientNames: [],
	projectCompanies: [],
    activeSubscription: {},
	activePlan: {},
	ownedTeams: {},
    paymentMethods: [],
    defaultMethod: ''
};

/*
* module getters
*/

const getters = {
	user: state => state.user,
    isFreelancer: state => state.isFreelancer,
    isSubscribed: state => state.isSubscribed,
	current_role: state => state.current_role,
	projects_listing_type: state => state.projects_listing_type,
	unread_comments: state => state.unread_comments,
    ownedTeam: state => state.ownedTeam,
    exitingEmails: state => state.exitingEmails,
    teamMembers: state => state.teamMembers,
    pendingEmails: state => state.pendingEmails,
    invitations: state => state.invitations,
	membersLimit: state => state.membersLimit,
    clientEmails: state => state.clientEmails,
    clientNames: state => state.clientNames,
    projectCompanies: state => state.projectCompanies,
    activeSubscription: state => state.activeSubscription,
	activePlan: state => state.activePlan,
	ownedTeams: state => state.ownedTeams,
    paymentMethods: state => state.paymentMethods,
    defaultMethod: state => state.defaultMethod,
};

/*
* module mutations
*/

const mutations = {
  /**
   * Always called on initial page load, which occurs in DashboardLayout
   * broadcast data-ready event
   */
	[BOOTSTRAP](state, data) {
		state.user = data.user;
		state.isFreelancer = data.isFreelancer;
		state.isSubscribed = data.isSubscribed;
		state.projects_listing_type = data.user.projects_listing_type;
		state.unread_comments = data.unread_comments;
		state.ownedTeam = data.ownedTeam;
		state.teamMembers = data.exitingMembers;
		data.exitingMembers.forEach(function (user) {
			state.exitingEmails.push(user.email)
        });
	  	data.teamInvitations.forEach(function (invitation) {
			state.pendingEmails.push(invitation.email);
			state.invitations.push(invitation);
        });
      	state.membersLimit = data.currentPlan && data.currentPlan.length
			? data.currentPlan.teams_members_count
			: 0;
	},
    addInvitation(state, invitation) {
        invitation.forEach(function (invite) {
            state.pendingEmails.push(invite.email);
            state.invitations.push(invite);
        })
	},
    deleteInvitaion(state, deletedInvitation) {
		state.pendingEmails.splice(state.pendingEmails.indexOf(deletedInvitation.email), 1);
		state.invitations.forEach(function (invitation) {
			if (invitation.id == deletedInvitation.id) {
				state.invitations.splice(state.invitations.indexOf(invitation), 1)
			}
        })
	},
    refreshListingType(state, type) {
		state.projects_listing_type = type
	},
    deleteTeamMembers(state, deletedMember) {
        state.teamMembers.forEach(function (member) {
        	if (member.id == deletedMember) {
        		state.teamMembers.splice(state.teamMembers.indexOf(member), 1);
        		state.exitingEmails.splice(state.exitingEmails.indexOf(member.email), 1)
			}
        })
    },
    recentDatas(state, data) {
        state.clientEmails = data.clientEmails;
        state.clientNames = data.clientNames;
        state.projectCompanies = data.projectCompanies;
    },
	updateRecentData(state, data) {
        console.log(data);
        switch (data.field) {
            case 'company':
                state.projectCompanies.splice(state.projectCompanies.indexOf(data.item), 1);
                break;
            case 'name':
                state.clientNames.splice(state.clientNames.indexOf(data.item), 1);
                break;
        }
	},
    getActiveSubscription(state, data) {
		state.activeSubscription = data.subscription;
		state.activePlan = data.plan;
		state.ownedTeams = data.ownedTeams;
	},
    getPaymentMethods(state, data) {
        state.paymentMethods = data.paymentMethods.data;
        state.defaultMethod = data.defaultMethod;
	},
    updatePaymentMethods(state, data) {
		if (data.type == 'add') {
            state.paymentMethods.push(data.card)
        }
	}
};

/*
* module actions
*/

const actions = {
	bootstrap({ dispatch, commit }) {
		return User.bootstrap().then(data => {
			if (data.status) {
                commit(BOOTSTRAP, data.data);
                dispatch('identifyUser', data.data.user);
            } else {
				toastr['error'](data.errors, 'Error');
			}
		});
	},
	identifyUser({commit}, user) {
		// // Identify logged in user on Gist
        // convertfox.identify(user.id, {
        //     email: user.email,
        //     name: user.name
        // });
	},
    changeProjectsListingType({commit}, type) {
		return User.changeProjectsListingType(type).then(data => {
			if (data.status) {
                commit('refreshListingType', type)
            } else {
                toastr['error'](data.errors, 'Error');
			}
		})
	},
    addInvitation({commit}, members) {
		return commit('addInvitation', members)
	},
    deleteTeamMembers({commit}, deletedMember) {
		return commit('deleteTeamMembers', deletedMember);
    },
    deleteInvitation({commit}, invitation) {
		return commit('deleteInvitaion', invitation);
	},
	getRecentDatas({commit}) {
		return User.getRecentData().then(data => {
			if (data.status) {
                commit('recentDatas', data.data)
            } else {
				toastr['error'](data.errors, 'Error')
			}
		})
	},
    updateRecentData({commit}, data) {
		commit('updateRecentData', data)
	},
	getActiveSubscription({commit}) {
		return User.getActiveSubscription().then(data => {
			if (data.status) {
				commit('getActiveSubscription', data.data)
			} else {
                toastr['error'](data.errors, 'Error')
			}
		})
	},
	getPaymentMethods({state, commit}, user) {
		state.paymentMethods = [];
		return User.getPaymentMethods(user).then(data => {
            if (data.status) {
                commit('getPaymentMethods', data.data)
            } else {
                toastr['error'](data.errors, 'Error')
            }
		})
	},
	updatePaymentMethods({commit}, data) {
		commit('updatePaymentMethods', data)
	}
};

export default {
  state,
  getters,
  mutations,
  actions
};
