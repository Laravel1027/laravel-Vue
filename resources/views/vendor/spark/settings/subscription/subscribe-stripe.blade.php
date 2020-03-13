<stripe-subscription :user="user" :team="team"
                     :plans="plans" :billable-type="billableType"
                     inline-template>
    <div>
        <!-- Common Subscribe Form Contents -->
        @include('spark::settings.subscription.subscribe-common')

        <div class="panel panel-default" v-show="selectedPlan">
            <!-- Payment Method Heading -->
            <div class="panel-heading">
                Select Payment Method
            </div>

            <div class="panel-body">
                <div class="exitingPayments" style="position: relative">
                    {{--Create button--}}
                    <div class="col-md-12">
                        <button v-if="paymentMethods.length" :disabled="exitingCardProcess" type="submit" class="btn btn-primary"
                                @click.prevent="payWithNewCard" style="margin-right: 8px">
                            Pay with new card
                        </button>
                    </div>

                    {{--Loader--}}
                    <div class="loader" v-if="paymentLoading"
                         style="position:absolute; left: 0; height: 100%; width: 100%; display: flex; align-items: center; justify-content: center; background: rgb(255,255,255); z-index: 1000">
                        <i class="fa fa-btn fa-spinner fa-spin" style="font-size: 50px"></i>
                    </div>

                    {{--Exiting Payment Methods--}}
                    <template v-if="paymentMethods.length">
                        <div class="row">
                            <div class="col-md-6" style="margin-top: 10px" v-for="stripe_card in paymentMethods">
                                <div class="text-center" @click="selectCard(stripe_card)">
                                    <label :id="'stripe-card-' + stripe_card.id" class='card-wrapper'
                                           :style="selectedCard === stripe_card.id ? 'opacity: 1' : 'opacity: 0.5'" style="margin-bottom: 10px">
                                    </label>
                                </div>
                            </div>
                        </div>
                        {{--Subscribe Button--}}
                        <div class="form-group">
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary"
                                        @click.prevent="subscribeWithExitingCard"
                                        :disabled="exitingCardProcess">
                                        <span v-if="exitingCardProcess">
                                            <i class="fa fa-btn fa-spinner fa-spin"></i>Subscribing
                                        </span>
                                    <span v-else>
                                            Subscribe
                                        </span>
                                </button>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div style="text-align: center">
                            <p>
                                <i class="fa fa-shopping-bag" style="font-size: 50px; color: #a4aaae;"></i>
                            </p>
                            <h4>No Payment Methods</h4>
                            <button type="submit" class="btn btn-primary"
                                    @click.prevent="payWithNewCard">
                                Pay with new card
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{--New Payment Modal--}}
        <div class="modal fade" id="new-payment-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" :disabled="form.busy">&times;
                        </button>
                        <h4 class="modal-title">
                            New Payment Method
                        </h4>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body" style="min-height: 400px;">
                        <div class="newPaymentMethod">
                            <!-- Generic 500 Level Error Message / Stripe Threw Exception -->
                            <div class="alert alert-danger" v-if="cardForm.errors.has('form')">
                                We had trouble validating your card. It's possible your card provider is preventing
                                us from charging the card. Please contact your card provider or customer support.
                            </div>

                            {{--New Card Form--}}
                            <div id="new-card-form">
                                <div class="card-wrapper-add"></div>

                                <div class="form-container-add active" style="margin-top: 10px">
                                    <form action="">
                                        <div class="form-row">
                                            {{--Card Number--}}
                                            <div class="form-group col-md-6">
                                                <label for="card_number">Card Number</label>
                                                <input placeholder="Card Number" id="card_number"
                                                       class="form-control"
                                                       type="tel"
                                                       style="border: 1px solid silver" name="number"
                                                       v-model="cardForm.number">
                                            </div>

                                            {{--Full name--}}
                                            <div class="form-group col-md-6">
                                                <label for="card_name">Cardholder's Name</label>
                                                <input placeholder="Cardholder's Name" id="card_name"
                                                       class="form-control"
                                                       type="text"
                                                       style="border: 1px solid silver" name="name"
                                                       v-model="cardForm.name">
                                            </div>

                                            {{--Exp Date--}}
                                            <div class="form-group col-md-4">
                                                <label for="card_expiry">Expiration Date</label>
                                                <input placeholder="MM/YYYY" type="tel" id="card_expiry"
                                                       class="form-control"
                                                       style="border: 1px solid silver" name="expiry"
                                                       v-model="cardForm.expiry">
                                            </div>

                                            {{--ZIP Code--}}
                                            <div class="form-group col-md-4">
                                                <label for="zip_code">ZIP Code</label>
                                                <input placeholder="ZIP Code" type="number" id="zip_code"
                                                       class="form-control"
                                                       style="border: 1px solid silver" name="zip"
                                                       v-model="form.zip">
                                            </div>

                                            {{--CVC--}}
                                            <div class="form-group col-md-4">
                                                <label for="card_cvc">CVC</label>
                                                <input placeholder="CVC" type="number" id="card_cvc"
                                                       class="form-control"
                                                       style="border: 1px solid silver" name="cvc"
                                                       v-model="cardForm.cvc">
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer" style="text-align: left !important">
                        <div class="col-md-5">
                            {{--Subscribe Button--}}
                            <div class="form-group">
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary"
                                            @click.prevent="subscribeWithNewCard"
                                            :disabled="form.busy">
                                        <span v-if="form.busy">
                                            <i class="fa fa-btn fa-spinner fa-spin"></i>Subscribing
                                        </span>
                                        <span v-else>
                                            Subscribe
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</stripe-subscription>
