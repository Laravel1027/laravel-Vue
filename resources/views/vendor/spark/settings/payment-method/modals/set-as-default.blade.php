<div class="modal fade" id="modal-set-as-default" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" :disabled="settingProcess">&times;
                </button>

                <h4 class="modal-title">
                    Set as default payment method
                </h4>
            </div>

            <div class="modal-body">
                Are you sure you want to set this payment method as default?
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" :disabled="settingProcess">No, Go Back</button>

                <button type="button" class="btn btn-danger" @click="setAsDefaultCard"
                        :disabled="settingProcess">
                    <span v-if="settingProcess">
                        <i class="fa fa-btn fa-spinner fa-spin"></i>Setting
                    </span>
                    <span v-else>
                        Yes, Set
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>