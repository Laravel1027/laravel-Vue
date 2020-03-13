import Vue from 'vue';
import router from './spa-projects-router.js';
import ElementUI from 'element-ui';
import store from './../../vuex/store.js';
import linkify from 'vue-linkify';
import VueQuillEditor from 'vue-quill-editor'

// Quill Editor Styles
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'
import 'quill/dist/quill.bubble.css'

require('../../../js/bootstrap');

Vue.directive('linkified', linkify);
Vue.use(ElementUI);
Vue.use(VueQuillEditor);
Vue.config.productionTip = false;
Vue.component('projects-container', require('./ProjectsContainer.vue'));
var app_porojects = new Vue({
    router,
    store,

    created() {
        store.dispatch('bootstrap')
    }

}).$mount('#spa-projects')
