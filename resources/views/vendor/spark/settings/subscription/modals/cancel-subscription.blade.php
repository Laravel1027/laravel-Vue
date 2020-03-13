<div class="modal fade" id="modal-cancel-subscription" tabindex="-1" role="dialog">
    <div class="modal-dialog" v-if="cancelingPlan">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" :disabled="cancelingProcess">&times;
                </button>

                <h4 class="modal-title">
                    Cancel Subscription
                </h4>
            </div>

            <div class="modal-body">
                Are you sure you want to cancel your subscription from @{{ cancelingPlan.name }} plan?
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" :disabled="cancelingProcess">No, Go Back</button>

                <button type="button" class="btn btn-danger" @click="cancelSubscription"
                        :disabled="cancelingProcess">
                    <span v-if="cancelingProcess">
                        <i class="fa fa-btn fa-spinner fa-spin"></i>Canceling
                    </span>
                    <span v-else>
                        Yes, Cancel
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>