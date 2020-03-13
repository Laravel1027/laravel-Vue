<div class="modal fade" id="modal-new-payment" tabindex="-1" role="dialog">
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
            <div class="modal-body" style="min-height: 400px">
                <div class="newPaymentMethod">
                    <!-- Generic 500 Level Error Message / Stripe Threw Exception -->
                    <div class="alert alert-danger" v-if="cardForm.errors.has('form')">
                        We had trouble validating your card. It's possible your card provider is preventing
                        us from charging the card. Please contact your card provider or customer support.
                    </div>

                    {{--New Card Form--}}
                    <div class="card-wrapper-add-new"></div>

                    <div class="form-container-add-new active" style="margin-top: 10px">
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

            <!-- Modal Actions -->
            <div class="modal-footer" style="text-align: left !important">
                <div class="col-md-5">
                    <button type="submit" class="btn btn-primary"
                            @click.prevent="addNewPaymentMethod"
                            :disabled="form.busy">
                                    <span v-if="form.busy">
                                        <i class="fa fa-btn fa-spinner fa-spin"></i>Adding
                                    </span>
                        <span v-else>
                                Add
                            </span>
                    </button>

                    <button class="btn btn-danger"
                            @click.prevent="closeNewPaymentModal" :disabled="form.busy">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>