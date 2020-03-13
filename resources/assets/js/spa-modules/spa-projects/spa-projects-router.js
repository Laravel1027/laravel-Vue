import Vue from 'vue'
import VueRouter from 'vue-router'
import AuthService from '../../services/auth'
Vue.use(VueRouter)

import ProjectsContainer from './ProjectsContainer.vue'
import ProjectsList from './ProjectsList.vue'
import ProjectsCreate from './ProjectsCreate.vue'
import ProjectsFiles from './ProjectsFiles.vue'
// import Proofer from './Proofer.vue';
import Proofer from './Proofer.2.vue';
import ProjectUpdate from './ProjectUpdate';
import ProjectTeamMembers from './ProjectTeamMembers';
import ProjectCreativeBrief from './ProjectCreativeBrief';

const routes = [
    //GENERAL
    {
        path: '/projects', component: ProjectsContainer,
        children: [
            { path: '/', component: ProjectsList, name : 'projects_list', props: true},
            { path: 'create', component: ProjectsCreate },
            {
                path: '/projects/files/:project_id/:revision_id',
                component: ProjectsFiles,
                name: 'upload_files_with_revision',
                props: true
            },
            {
                path: '/projects/creative-brief/:project_id/:revision_id',
                component: ProjectCreativeBrief,
                name: 'project_creative_brief',
                props: true
            },
            {
                path: '/projects/team-members/:project_id',
                component: ProjectTeamMembers,
                name: 'add_team_members',
                props: true
            },
            {
                path: '/projects/files/:project_id',
                component: ProjectsFiles,
                name: 'upload_files',
                props: true
            },
            {
                path: '/projects/update/:project_id',
                component: ProjectUpdate,
                name: 'update_project',
                props: true,
            }
        ]
    },

    //PROOFER FREELANCER
    {
        path: '/proofer/:project_id/:revision_id',
        component: Proofer,
        name: 'proofer',
        props: true,
        meta: { requiresAuth: true }
    },
    
    {
        path: '/proofer/:project_id/:revision_id/:proof_id',
        component: Proofer,
        name: 'proofer',
        props: true,
        meta: { requiresAuth: true }
    },

    {
        path: '/proofer/:project_id/:revision_id/:proof_id/:issue_id',
        component: Proofer,
        name: 'proofer',
        props: true,
        meta: { requiresAuth: true }
    },

    /*  //PROOFER CLIENT
     {
         path: '/proofer_guest/:project_id/:revision_id/:project_hash',
         component: Proofer,
         name: 'proofer_guest',
         props: true,
         meta: { requiresAuth: true }
     } */
]

const router = new VueRouter({
    routes,
    mode: 'history',
    linkActiveClass: 'active'
})

/* router.beforeEach((to, from, next) => {
    // If the next route is requires user to be Logged IN
    if (to.matched.some(m => m.meta.requiresAuth)) {
        return AuthService.check().then(authenticated => {
            if (!authenticated) {
                window.location.href = '/login';
            } else {
                return next()
            }
        })
    }
    return next()
}); */

export default router