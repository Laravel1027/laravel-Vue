import Ls from './ls'
export default {
/*     login(loginData){
        return axios.post('/api/auth/login', loginData).then(response =>  {
            Ls.set('auth.token',response.data.token)
            toastr['success']('Logged In!', 'Success')
        }).catch(error => {
            if (error.response.status == 401) {
                toastr['error']('Invalid Credentials', 'Error')
            } else {
                console.log('Error', error.message)
            }
        });

    },
 */
    loginByUserId(loginData){
        return axios.post('/api/auth/login_by_user_id', loginData).then(response =>  {
            toastr['success']('Logged In!', 'Success');
        }).catch(error => {
            if (error.response.status == 401) {
                toastr['error']('Invalid Credentials', 'Error')
            } else {
                // Something happened in setting up the request that triggered an Error
                console.log('Error', error.message)
            }
        });
    },

/*     logout(){
        return axios.get('/api/auth/logout').then(response =>  {
            Ls.remove('auth.token')
            toastr['success']('Logged out!', 'Success')
        }).catch(error => {
            console.log('Error', error.message);
        });
    },
 */
    check(){
        return axios.get('/api/auth/check').then(response =>  {
            return !!response.data.authenticated
        }).catch(error => {
            console.log('Error', error.message)
        });
    },

    getCurrentRol(project_id){
        return axios.get('/api/projects/get_user_rol/' + project_id).then(response =>  {
            Ls.set('auth.rol', response.data.data);
            return response.data.data;
        }).catch(error => {
            console.log('Error', response.data.error)
        });
    },

}