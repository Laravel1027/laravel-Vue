import Vue from 'vue';
import router from './spa-proofer-router.js';
import ElementUI from 'element-ui'
import store from './../../vuex/store.js'
require('../../../js/bootstrap');

Vue.use(ElementUI)
Vue.use(VueSmoothScroll)

Vue.config.productionTip = false;
Vue.component('proofer-container', require('./ProoferContainer.vue'));
var app_proofer = new Vue({
    router,
    store
}).$mount('#spa-proofer')
