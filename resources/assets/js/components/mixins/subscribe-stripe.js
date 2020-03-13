module.exports = {
    props: {},
    data() {
        return {
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
            exitingPayments: [],
            exitingCardProcess: false,
            paymentLoading: false,
            cancelingProcess: false,
            newCardForm: false,
        }
    },
    methods: {
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
            // this.getPaymentMethods();
        },

        //Select payment method
        selectCard(card) {
            this.selectedCard = card.id;
        },

        //Get exiting payment methods
        getPaymentMethods() {
            let self = this;
            this.paymentLoading = true;
            axios.get(`/settings/get-payment-methods/${this.user.stripe_id}`)
                .then(response => {
                    if (response.data.status) {
                        self.exitingPayments = response.data.data.paymentMethods.data;
                        self.selectedCard = response.data.data.defaultMethod;
                        setTimeout(function () {
                            self.appendStripeCardsInterface();
                        }, 1000);
                    } else {
                        self.paymentLoading = false;
                        toastr['error'](response.data.errors, 'Error')
                    }
                })
                .catch(error => {
                    self.paymentLoading = false;
                })
        },

        //Styling exiting payment methods
        appendStripeCardsInterface() {
            (this.exitingPayments).forEach(function (stripe_card, index) {
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
            axios.post('/api/settings/subscribe-with-exiting-card', data)
                .then(response => {
                    this.exitingCardProcess = false;
                    if (response.data.status) {
                        self.selectedPlan = null;
                        self.exitingPayments = [];
                        toastr['success'](response.data.message, 'Success');
                        Bus.$emit('updateUser');
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

            axios.post('/api/settings/subscribe-with-new-card', this.form)
                .then(response => {
                    this.form.busy = false;
                    if (response.data.status) {
                        self.selectedPlan = null;
                        self.resetForm();
                        self.exitingPayments = [];
                        toastr['success'](response.data.message, 'Success')
                        $('#new-payment-modal').modal('hide');
                        Bus.$emit('updateUser');
                    } else {
                        toastr['error'](response.data.errors, 'Error')
                    }
                });
        },

        //Cancel subscription dialog
        approveCancelSubscription(plan) {
            this.cancelingPlan = plan;
            this.form.plan = this.cancelingPlan.id;

            $('#modal-cancel-subscription').modal('show');
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
                        toastr['success'](response.data.message, 'Success')
                        Bus.$emit('updateUser');
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
        }
    },
    computed: {},
    watch: {},
    created() {},
    mounted() {
        this.initCardForm();
    },
}