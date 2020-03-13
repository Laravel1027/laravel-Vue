import {mapActions} from 'vuex';

Vue.component('spark-settings', {
    props: ['user', 'teams'],


    /**
     * Load mixins for the component.
     */
    mixins: [require('./../mixins/tab-state')],


    /**
     * The component's data.
     */
    data() {
        return {
            billableType: 'user',
            team: null
        };
    },
    methods: {
        ...mapActions([
            'getActiveSubscription',
            'getPaymentMethods',
        ])
    },

    created() {
        this.getActiveSubscription();
        this.getPaymentMethods(this.user);
    },

    /**
     * Prepare the component.
     */
    mounted() {
        this.usePushStateForTabs('.spark-settings-tabs');
    }
});
