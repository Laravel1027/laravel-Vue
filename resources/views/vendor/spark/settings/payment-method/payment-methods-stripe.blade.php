<payment-methods-stripe :user="user" :team="team" :billable-type="billableType" inline-template>
    <div>
        <div class="panel panel-default">
            <!-- Update Payment Method Heading -->
            <div class="panel-heading">
                Payment Methods
            </div>

            <div class="panel-body">
                <div class="paymentMethods" style="position: relative">
                    {{--Create button--}}
                    <div class="col-md-12" style="text-align: right;">
                        <button v-if="paymentMethods.length" type="submit" class="btn btn-primary"
                                @click.prevent="newPaymentMethod" style="margin-right: 8px">
                            <i class="fa fa-plus"></i> Add new
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
                                <div class="text-center" style="position: relative;">
                                    <label :id="'card-' + stripe_card.id" class='card-wrapper'
                                           :style="defaultMethod === stripe_card.id ? 'opacity: 1' : 'opacity: 0.5'" style="margin-bottom: 10px"></label>
                                    <br>
                                    <div v-if="defaultMethod !== stripe_card.id" style="position: absolute; top: -15px; left: 15px; !important; z-index: 999;">
                                        <button class="btn btn-danger"
                                                @click="destroyCardConfirm(stripe_card)"
                                                v-if="defaultMethod !== stripe_card.id" style="opacity: 1 !important;">
                                            <i class="fa fa-times"></i>
                                        </button>
                                        <button class="btn btn-primary"
                                                @click="setAsDefaultCardConfirm(stripe_card)">
                                            SET AS DEFAULT
                                        </button>
                                    </div>
                                    <span class="btn btn-success"
                                            v-if="defaultMethod === stripe_card.id"
                                            style="position: absolute; top: -15px; left: 15px; opacity: 1 !important; z-index: 999;">
                                        <i class="fa fa-check"></i> DEFAULT
                                    </span>
                                </div>
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
                                    @click.prevent="newPaymentMethod">
                                <i class="fa fa-plus"></i> Add Payment method
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{--New Payment Modal--}}
        @include('spark::settings.payment-method.modals.new-payment-method')

        {{--Remove Payment Modal--}}
        @include('spark::settings.payment-method.modals.remove-payment-method')

        {{--Set As Default Modal--}}
        @include('spark::settings.payment-method.modals.set-as-default')
    </div>
</payment-methods-stripe>
