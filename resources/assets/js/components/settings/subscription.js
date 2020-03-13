import {mapActions, mapGetters} from 'vuex';

Vue.component('stripe-subscription', {
    props: ['user', 'team', 'billableType'],
    mixins: [
        require('../mixins/plans'),
    ],
    data() {
        return {
            plans: [],
            loading: false,
            form: new SparkForm({
                stripe_token: '',
                plan: '',
                coupon: null,
                address: '',
                address_line_2: '',
                city: '',
                state: '',
                zip: '',
                country: 'US',
                vat_id: ''
            }),
            cardForm: new SparkForm({
                name: '',
                number: '',
                cvc: '',
                expiry: '',
                zip: ''
            }),
            selectedCard: null,
            exitingCardProcess: false,
            paymentLoading: false,
            resumingProcess: false,
            cancelingProcess: false,
            newCardForm: false,
        }
    },
    methods: {
        getPlans() {
            var self = this;
            axios.get('/plans/list')
                .then(response => {
                    if (response.data.status) {
                        self.plans = response.data.data;
                    } else {
                        toastr['error'](response.data.errors, 'Error');
                    }
                })
                .catch(error => {
                    if (error.exception) {
                        toastr['error']('Something went wrong, please try again later', 'Error');
                    } else {
                        toastr['error'](error.message, 'Error');
                    }
                });
        },
        //Init card input form
        initCardForm() {
            new card({
                form: '.form-container-add',
                container: '.card-wrapper-add',
                formatting: true,
                formSelectors: {
                    numberInput: 'input[name="number"]',
                    expiryInput: 'input[name="expiry"]',
                    cvcInput: 'input[name="cvc"]',
                    nameInput: 'input[name="name"]'
                },
                cardSelectors: {
                    cardContainer: '.jp-card-container',
                    card: '.jp-card',
                    numberDisplay: '.jp-card-number',
                    expiryDisplay: '.jp-card-expiry',
                    cvcDisplay: '.jp-card-cvc',
                    nameDisplay: '.jp-card-name'
                },
                messages: {
                    validDate: 'datyeee',
                    monthYear: 'month/year'
                },
                masks: {
                    cardNumber: false
                },
                classes: {
                    valid: 'jp-card-valid',
                    invalid: 'jp-card-invalid'
                },
                debug: false
            })
        },

        //Select subscribing plan
        selectPlan(plan) {
            this.selectedPlan = plan;
            this.form.plan = this.selectedPlan.id;
        },

        //Select payment method
        selectCard(card) {
            this.selectedCard = card.id;
        },

        //Styling exiting payment methods
        appendStripeCardsInterface() {
            (this.paymentMethods).forEach(function (stripe_card, index) {
                let expire_month, brand_class;

                if (stripe_card.brand === 'Visa') {
                    brand_class = 'visa';
                } else if (stripe_card.brand === 'American Express') {
                    brand_class = 'amex';
                } else if (stripe_card.brand === 'MasterCard') {
                    brand_class = 'mastercard';
                } else if (stripe_card.brand === 'Discover') {
                    brand_class = 'discover';
                } else if (stripe_card.brand === 'JCB') {
                    brand_class = 'jcb';
                } else if (stripe_card.brand === 'Diners Club') {
                    brand_class = 'dinersclub';
                }

                stripe_card.exp_month < 10 ? expire_month = '0' + stripe_card.exp_month : expire_month = stripe_card.exp_month;
                new card({
                    form: 'form',
                    container: '#stripe-card-' + stripe_card.id,
                    placeholders: {
                        number: '**** **** **** ' + stripe_card.last4,
                        name: stripe_card.name,
                        expiry: expire_month + '/' + stripe_card.exp_year,
                        cvc: '***'
                    }
                });
                if (brand_class) {
                    $('#stripe-card-' + stripe_card.id).find('.jp-card').addClass('jp-card-' + (brand_class).toLowerCase());
                    $('#stripe-card-' + stripe_card.id).find('.jp-card').addClass('jp-card-identified');
                }
            });
            this.paymentLoading = false;
        },

        //Show new card modal
        payWithNewCard() {
            this.newCardForm = true;
            $('#new-payment-modal').modal('show');
        },

        //Subscribe with exiting payment method
        subscribeWithExitingCard() {
            var self = this;
            var data = {
                card: this.selectedCard,
                plan: this.selectedPlan,
            };

            this.exitingCardProcess = true;
            axios.post(this.urlForSubscribeWithExitingCard, data)
                .then(response => {
                    this.exitingCardProcess = false;
                    if (response.data.status) {
                        self.selectedPlan = null;
                        self.getPaymentMethods(self.user);
                        toastr['success'](response.data.message, 'Success');
                        Bus.$emit('updateUser');
                        this.getActiveSubscription();
                    } else {
                        toastr['error'](response.data.errors, 'Error')
                    }
                });
        },

        //Create token for new payment method
        subscribeWithNewCard() {
            var exp = this.cardForm.expiry.replace(/\s/g, '').split('/');

            this.cardForm.errors.forget();
            this.form.startProcessing();

            const payload = {
                name: this.cardForm.name,
                number: this.cardForm.number,
                cvc: this.cardForm.cvc,
                exp_month: exp[0],
                exp_year: exp[1],
                address_line1: this.form.address,
                address_line2: this.form.address_line_2,
                address_city: this.form.city,
                address_state: this.form.state,
                address_zip: this.form.zip,
                address_country: this.form.country
            };

            Stripe.card.createToken(payload, (status, response) => {
                if (response.error) {
                    this.cardForm.errors.set({form: [
                            response.error.message
                        ]})
                    this.form.busy = false;
                } else {
                    this.createSubscription(response.id);
                }
            });
        },

        //Create subscription with new card
        createSubscription(token) {
            var self = this;
            this.form.stripe_token = token;

            axios.post(this.urlForSubscribeWithNewCard, this.form)
                .then(response => {
                    this.form.busy = false;
                    if (response.data.status) {
                        self.selectedPlan = null;
                        self.resetForm();
                        self.getPaymentMethods(self.user);
                        toastr['success'](response.data.message, 'Success')
                        $('#new-payment-modal').modal('hide');
                        Bus.$emit('updateUser');
                        this.getActiveSubscription();
                    } else {
                        toastr['error'](response.data.errors, 'Error')
                    }
                });
        },

        //Resume subscription dialog
        approveResumeSubscription(plan, subscription) {
            this.resumingPlan = plan;
            this.form.plan = this.resumingPlan.id;
            this.form.subscription = subscription.stripe_id;

            $('#modal-resume-subscription').modal('show');
        },

        //Cancel subscription dialog
        approveCancelSubscription(plan) {
            this.cancelingPlan = plan;
            this.form.plan = this.cancelingPlan.id;

            $('#modal-cancel-subscription').modal('show');
        },

        //Resume subscription
        resumeSubscription() {
            var self = this;
            this.resumingProcess = true;
            axios.post('/api/settings/resume-subscription', this.form)
                .then(response => {
                    this.resumingProcess = false;
                    if (response.data.status) {
                        $('#modal-resume-subscription').modal('hide');
                        toastr['success'](response.data.message, 'Success');
                        Bus.$emit('updateUser');
                        this.getActiveSubscription();
                    } else {
                        toastr['error'](response.data.errors, 'Error')
                    }
                })
                .catch(error => {
                    $('#modal-resume-subscription').modal('show');
                    this.resumingProcess= false;
                    toastr['error']('Internal server error')
                })
        },

        //Cancel subscription
        cancelSubscription() {
            var self = this;
            this.cancelingProcess = true;
            axios.post('/api/settings/cancel-subscription', this.form)
                .then(response => {
                    this.cancelingProcess = false;
                    if (response.data.status) {
                        $('#modal-cancel-subscription').modal('hide');
                        toastr['success'](response.data.message, 'Success');
                        Bus.$emit('updateUser');
                        this.getActiveSubscription();
                    } else {
                        toastr['error'](response.data.errors, 'Error')
                    }
                })
                .catch(error => {
                    $('#modal-cancel-subscription').modal('show');
                    this.cancelingProcess = false;
                    toastr['error']('Internal server error')
                })
        },

        //Reset card form
        resetForm() {
            this.form = new SparkForm({
                stripe_token: '',
                plan: '',
                coupon: null,
                address: '',
                address_line_2: '',
                city: '',
                state: '',
                zip: '',
                country: 'US',
                vat_id: ''
            });
            this.cardForm = new SparkForm({
                name: '',
                number: '',
                cvc: '',
                expiry: '',
                zip: ''
            });
        },

        ...mapActions([
            'getActiveSubscription',
            'getPaymentMethods',
            'updatePaymentMethods',
        ])
    },
    computed: {
        ...mapGetters([
            'paymentMethods',
            'defaultMethod',
            'activeSubscription',
            'activePlan',
        ]),
        urlForSubscribeWithNewCard() {
            return this.activeSubscription
                ? '/api/settings/swap-subscription-with-new-card'
                : '/api/settings/subscribe-with-new-card'
        },
        urlForSubscribeWithExitingCard() {
            return this.activeSubscription
                ? '/api/settings/swap-subscription-with-exiting-card'
                : '/api/settings/subscribe-with-exiting-card'
        }
    },
    watch: {
        paymentMethods() {
            let self = this;
            this.paymentLoading = true;
            if (this.paymentMethods.length) {
                setTimeout(function () {
                    self.appendStripeCardsInterface();
                    self.selectedCard = self.defaultMethod;
                }, 1000);
            } else {
                this.paymentLoading = false;
            }
        }
    },
    created() {
        var self = this;
        this.getPlans();
        this.$on('showPlanDetails', function (plan) {
            self.showPlanDetails(plan);
        });
    },
    mounted() {
        this.initCardForm();
    },
    filters: {
        utc_to_local: function (date) {
            if (!date) return '';
            var utc = moment.utc(date).toDate();
            return moment(utc).local().format('hh:mm A / DD.MM.YY');
        }
    }
});