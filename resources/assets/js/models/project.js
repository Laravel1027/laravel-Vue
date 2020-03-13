export default {
    list() {
      return axios.get('/api/projects').then(response =>  {
        return response.data;
      }).catch(error => {
          return error.response;
      });
    }
  }