import Vue from 'vue'
import VueRouter from 'vue-router'
Vue.use(VueRouter)

// import Proofer from '../spa-projects/Proofer.vue';
import Proofer from '../spa-projects/Proofer.2.vue'

const routes = [
    //GENERAL
    //PROOFER FREELANCER
    {
        path: '/proofer/:project_id/:revision_id',
        component: Proofer,
        name: 'proofer_freelancer',
        props: true,
        meta: { requiresAuth: true }
    },

    //PROOFER CLIENT
    {
        path: '/proofer/:project_id/:revision_id/:project_hash',
        component: Proofer,
        name: 'proofer_guest',
        props: true,
    }
]

const router = new VueRouter({
    routes,
    mode: 'history',
    linkActiveClass: 'active'
})

export default router