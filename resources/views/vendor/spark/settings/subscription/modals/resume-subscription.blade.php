<div class="modal fade" id="modal-resume-subscription" tabindex="-1" role="dialog">
    <div class="modal-dialog" v-if="resumingPlan">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" :disabled="cancelingProcess">&times;
                </button>

                <h4 class="modal-title">
                    Resume Subscription
                </h4>
            </div>

            <div class="modal-body">
                Are you sure you want to resume your subscription to @{{ resumingPlan.name }} plan?
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" :disabled="cancelingProcess">No, Go Back</button>

                <button type="button" class="btn btn-warning" @click="resumeSubscription"
                        :disabled="resumingProcess">
                    <span v-if="resumingProcess">
                        <i class="fa fa-btn fa-spinner fa-spin"></i>Resuming
                    </span>
                    <span v-else>
                        Yes, Resume
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>