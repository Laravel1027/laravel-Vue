var Vue = require('vue')
var Vuex = require('vuex')

Vue.use(Vuex)

Vue.config.debug = true

// modules
import users from './modules/users.js'
import projects from './modules/projects.js'

export default new Vuex.Store({

    modules: {
        users,
        projects
    },
})

