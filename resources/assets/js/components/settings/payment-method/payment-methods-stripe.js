import {mapActions, mapGetters} from 'vuex';

Vue.component('payment-methods-stripe', {
    props: ['user', 'team', 'billableType'],
    data() {
        return {
            form: new SparkForm({
                stripe_token: '',
                address: '',
                address_line_2: '',
                city: '',
                state: '',
                zip: '',
                country: 'US'
            }),
            cardForm: new SparkForm({
                name: '',
                number: '',
                cvc: '',
                month: '',
                year: ''
            }),
            card: null,
            paymentLoading: true,
            newPaymentMethodModal: false,
            removeProcess: false,
            settingProcess: false,
            stripe_default_card: null
        };
    },
    mounted() {
        let self = this;
        Stripe.setPublishableKey(Spark.stripeKey);
        this.initCardForm();
        setTimeout(function () {
            self.paymentLoading = false;
        }, 2000)
        // this.getPaymentMethods();
    },
    created() {
    },
    methods: {
        initCardForm() {
            new card({
                form: '.form-container-add-new',
                container: '.card-wrapper-add-new',
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
        newPaymentMethod() {
            this.newPaymentMethodModal = true;
            $('#modal-new-payment').modal('show');
        },
        closeNewPaymentModal() {
            this.resetForm();
            this.newPaymentMethodModal = false;
            $('#modal-new-payment').modal('hide');
        },
        addNewPaymentMethod() {
            this.cardForm.errors.forget();
            this.form.startProcessing();

            const payload = {
                name: this.cardForm.name,
                number: this.cardForm.number,
                cvc: this.cardForm.cvc,
                address_line1: this.form.address,
                address_line2: this.form.address_line_2,
                address_city: this.form.city,
                address_state: this.form.state,
                address_zip: this.form.zip,
                address_country: this.form.country
            };

            if (this.cardForm.expiry) {
                let exp = this.cardForm.expiry.replace(/\s/g, '').split('/');
                payload.exp_month = exp[0];
                payload.exp_year = exp[1];
            }

            Stripe.card.createToken(payload, (status, response) => {
                if (response.error) {
                    this.cardForm.errors.set({
                        form: [
                            response.error.message
                        ]
                    });
                    this.form.busy = false;
                } else {
                    this.createPaymentMethod(response.id);
                }
            });
        },
        createPaymentMethod(token) {
            let self = this;
            this.form.stripe_token = token;
            axios.post('/api/settings/add-payment-method', this.form)
                .then(response => {
                    this.form.busy = false;
                    if (response.data.status) {
                        toastr['success'](response.data.message, 'Success');
                        self.resetForm();
                        $('#modal-new-payment').modal('hide');
                        self.paymentLoading = true;
                        self.updatePaymentMethods({card: response.data.data, type: 'add'});
                    } else {
                        toastr['error'](response.data.errors, 'Error')
                    }
                })
                .catch(error => {
                    self.form.busy = false;
                    toastr['error']('Internal server error')
                })
        },
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
        appendStripeCardsInterface() {
            $.each(this.paymentMethods, function (index, stripe_card) {
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
                    container: '#card-' + stripe_card.id,
                    placeholders: {
                        number: '**** **** **** ' + stripe_card.last4,
                        name: stripe_card.name,
                        expiry: expire_month + '/' + stripe_card.exp_year,
                        cvc: '***'
                    }
                });
                if (brand_class) {
                    $('#card-' + stripe_card.id).find('.jp-card').addClass('jp-card-' + (brand_class).toLowerCase());
                    $('#card-' + stripe_card.id).find('.jp-card').addClass('jp-card-identified');
                }
            });
            this.paymentLoading = false;
        },
        destroyCardConfirm(card) {
            this.card = card;
            $('#modal-remove-payment').modal('show')
        },
        cancelDestroyingCard() {
            this.card = null;
            $('#modal-remove-payment').modal('hide')
        },
        removePaymentMethod() {
            let self = this;
            this.removeProcess = true;
            axios.post('/api/settings/remove-payment-method', this.card)
                .then(response => {
                    self.removeProcess = false;
                    if (response.data.status) {
                        toastr['success'](response.data.message, 'Success');
                        $('#modal-remove-payment').modal('hide');
                        self.paymentLoading = true;
                        self.getPaymentMethods(self.user);
                    } else {
                        toastr['error'](response.data.errors, 'Error')
                    }
                })
                .catch(error => {
                    self.removeProcess = false;
                    toastr['error']('Internal server error')
                })
        },
        setAsDefaultCardConfirm(card) {
            this.card = card;
            $('#modal-set-as-default').modal('show');
        },
        setAsDefaultCard() {
            let self = this;
            this.settingProcess = true;
            axios.post('/api/settings/set-as-default-payment', this.card)
                .then(response => {
                    self.settingProcess = false;
                    if (response.data.status) {
                        toastr['success'](response.data.message, 'Success');
                        $('#modal-set-as-default').modal('hide');
                        self.paymentLoading = true;
                        self.getPaymentMethods(self.user);
                    } else {
                        toastr['error'](response.data.errors, 'Error')
                    }
                })
                .catch(error => {
                    self.settingProcess = false;
                    toastr['error']('Internal server error')
                })
        },

        ...mapActions([
            'getPaymentMethods',
            'updatePaymentMethods',
        ])
    },
    computed: {
        ...mapGetters([
            'paymentMethods',
            'defaultMethod'
        ])
    },
    watch: {
        paymentMethods() {
            let self = this;
            this.paymentLoading = true;
            if (this.paymentMethods.length) {
                setTimeout(function () {
                    self.appendStripeCardsInterface();
                }, 1000);
            } else {
                this.paymentLoading = false;
            }
        }
    }
})
