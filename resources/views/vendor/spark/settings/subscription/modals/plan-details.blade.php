<div class="modal fade" id="modal-plan-details" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm">
        <div class="modal-content" v-if="detailingPlan">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">
                    <b>@{{ detailingPlan.name }}</b> plan features
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="row">
                    <label class="col-md-6 control-label">
                        <span>Price:</span>
                    </label>

                    <div class="col-md-6">
                        <b>@{{ detailingPlan.price | currency }} / Monthly</b>
                    </div>
                </div>

                <div class="row">
                    <label class="col-md-6 control-label">
                        <span>Teams Count:</span>
                    </label>

                    <div class="col-md-6">
                        <b>@{{ detailingPlan.teams_count }}</b>
                    </div>
                </div>

                <div class="row">
                    <label class="col-md-6 control-label">
                        <span>Members Count:</span>
                    </label>

                    <div class="col-md-6">
                        <b>@{{ detailingPlan.teams_members_count }}</b>
                    </div>
                </div>
            </div>

            <!-- Modal Actions -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>