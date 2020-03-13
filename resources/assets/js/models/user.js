export default {
    bootstrap() {
        return axios.get('/api/bootstrap')
            .then(response => {
                return response.data;
            }).catch(error => {
                return {
                    status: 0,
                    errors: error.response.statusText
                }
            });
    },
    changeProjectsListingType(type) {
        return axios.put('/api/bootstrap/change-projects-listing-type', {type: type})
            .then(response => {
                return response.data;
            })
            .catch(error => {
                return {
                    status: 0,
                    errors: error.response.statusText
                }
            })
    },
    getRecentData() {
        return axios.get('/api/bootstrap/get-recent-datas')
            .then(response => {
                return response.data;
            }).catch(error => {
                return {
                    status: 0,
                    errors: error.response.statusText
                }
            });
    },
    getActiveSubscription() {
        return axios.get('/api/bootstrap/active-subscription')
            .then(response => {
                return response.data;
            })
            .catch(error => {
                return {
                    status: 0,
                    errors: error.response.statusText
                }
            });
    },
    getPaymentMethods(user) {
        return axios.get(`/api/payments/get-payment-methods/${user.stripe_id}`)
            .then(response => {
                return response.data;
            })
            .catch(error => {
                return {
                    status: 0,
                    errors: error.response.statusText
                }
            })
    },
}