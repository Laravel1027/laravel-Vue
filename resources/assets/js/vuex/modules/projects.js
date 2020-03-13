// system.js
// main module
//----------------------------------

import Project from "../../models/project";

import {PROJECTS_LOAD} from "../mutation-types.js";

/*
 * state
 *
 */

const state = {
    projects: {},
    in_progress: {},
    on_revision: {},
    on_draft: {},
    on_hold: {},
    approved: {},
    completed: {},
    member_projects: {},
    current_member: '',
    current_proofs_state: '',
    show_project_by_status: 0,
    current_project_listing: 'all',
    is_freelancer: {},
    is_collaborator: {},
};

/*
* module getters
*/

const getters = {
    projects: state => state.projects,
    current_proofs_state: state => state.current_proofs_state,
    in_progress: state => state.in_progress,
    on_revision: state => state.on_revision,
    approved: state => state.approved,
    completed: state => state.completed,
    on_draft: state => state.on_draft,
    on_hold: state => state.on_hold,
    member_projects: state => state.member_projects,
    current_member: state => state.current_member,
    listing: state => state.current_project_listing,
    is_freelancer: state => state.is_freelancer,
    is_collaborator: state => state.is_collaborator
};

/*
* module mutations
*/

const mutations = {
    /**
     * Always called on initial page load, which occurs in DashboardLayout
     * broadcast data-ready event
     */
    [PROJECTS_LOAD](state, data) {
        if (data) {
            state.projects = data
            state.in_progress = data.filter(project => (project.status == 'progress' || (project.type == 'website' && project.status == 'revision') && project.role == 'freelancer'))
            state.on_revision = data.filter(project => (project.status == 'revision' && project.type == 'design' || project.role != 'freelancer' && (project.status == 'draft' || (project.type == 'website' && project.status == 'revision'))))
            state.on_draft = data.filter(project => project.status == 'draft' && project.role == 'freelancer');
            state.on_hold = data.filter(project => project.status == 'hold');
            state.approved = data.filter(project => project.status == 'approved')
            state.completed = data.filter(project => project.status == 'completed');
            state.is_freelancer = data.filter(project => project.role == 'freelancer');
            state.is_collaborator = data.filter(project => project.role == 'collaborator');
        }
    },

    SAVE_CURRENT_PROOF_DATA(state, data) {
        state.current_proofs_state = data;
    },

    RESET_CURRENT_PROOF_DATA(state) {
        state.current_proofs_state = [];
    },

    SAVE_CURRENT_PROJECT_NAME(state, data) {
        state.current_project_name = data;
    },

    SAVE_PROGRESS_PROJECT(state, data) {
        state.in_progress_projects = data;
    },

    SAVE_REVISION_PROJECTS(state, data) {
        state.on_revison_projects = data;
    },

    SAVE_APPROVED_PROJECTS(state, data) {
        state.approved_projects = data;
    },

    SAVE_COMPLETED_PROJECTS(state, data) {
        state.completed_projects = data;
    },

    SAVE_DRAFT_PROJECTS(state, data) {
        state.on_draft_projects = data;
    },
    set_show_project_by_status(state, data) {
        state.show_project_by_status = data;
    },

    set_current_project_listing(state, data) {
        state.current_project_listing = data;
    },
    showMemberProjects(state, member) {
        state.current_member = member.name;
        state.member_projects = state.projects.filter(project => project.users.indexOf(member.id) >= 0);
    }

};

/*
* module actions
*/

const actions = {

    loadProjects({dispatch, commit}) {
        return Project.list().then(projects => {
            commit(PROJECTS_LOAD, Object.values(projects));
        });
    },
    loadMemberProjects({commit}, member) {
        commit('showMemberProjects', member);
    }
};

export default {
    state,
    getters,
    mutations,
    actions
};
