<div class="modal fade" id="modal-remove-payment" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" :disabled="removeProcess">&times;
                </button>

                <h4 class="modal-title">
                    Remove Payment method
                </h4>
            </div>

            <div class="modal-body">
                Are you sure you want to remove this payment method?
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" :disabled="removeProcess" @click="cancelDestroyingCard">No, Go Back</button>

                <button type="button" class="btn btn-danger" @click="removePaymentMethod"
                        :disabled="removeProcess">
                    <span v-if="removeProcess">
                        <i class="fa fa-btn fa-spinner fa-spin"></i>Removing
                    </span>
                    <span v-else>
                        Yes, Remove
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>