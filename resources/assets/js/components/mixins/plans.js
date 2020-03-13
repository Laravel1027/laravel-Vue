module.exports = {
    data() {
        return {
            selectedPlan: null,
            detailingPlan: null,
            cancelingPlan: null,
            resumingPlan: null,
        }
    },
    methods: {
        showPlanDetails(plan) {
            this.detailingPlan = plan;
            $('#modal-plan-details').modal('show');
        },
    },
    computed: {},
    watch: {},
    created() {

    },
    mounted() {

    },
    filters: {
        currency(value) {
            return '$' + parseFloat(value/100).toFixed(2);
        }
    },
}