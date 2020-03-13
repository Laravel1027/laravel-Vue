Vue.component('spark-custom-navbar', {
    props: [
        'user', 'teams', 'currentTeam',
        'hasUnreadNotifications', 'hasUnreadAnnouncements'
    ],

    data() {
        return {
            dropdown: 'dropdown',
            show: 'false'
        }
    },
    methods: {
        /**
         * Show the user's notifications.
         */
        showNotifications() {
            Bus.$emit('showNotifications');
        },


        /**
         * Show the customer support e-mail form.
         */
        showSupportForm() {
            Bus.$emit('showSupportForm');
        },

        toggleDropdown() {
            if (this.dropdown == 'dropdown') {
                this.dropdown = 'dropdown open';
            } else {
                this.dropdown = 'dropdown';
            }
            this.show = !this.show;
        }
    }
});
