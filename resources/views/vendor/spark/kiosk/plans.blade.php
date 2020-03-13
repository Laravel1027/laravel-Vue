<spark-kiosk-plans inline-template>
    <div class="panel panel-default">
        <div class="panel-heading">
            Plans
        </div>
        <div class="panel-body">
            {{--Create button--}}
            <div class="col-md-12" style="text-align: right;">
                <button v-if="plans.length" type="submit" class="btn btn-primary"
                        @click.prevent="create" style="margin-right: 8px">
                    <i class="fa fa-plus"></i> New
                </button>
            </div>

            {{--All Plans--}}
            <div class="col-md-12" style="position: relative">
                <div class="loader" v-if="loading" style="position:absolute; left: 0; height: 100%; width: 100%; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,.9);">
                    <i class="fa fa-btn fa-spinner fa-spin" style="font-size: 50px"></i>
                </div>
                <table class="table table-borderless m-b-none">
                    <thead></thead>
                    <tbody>
                    <template v-if="plans.length">
                        <tr v-for="plan in plans">
                            {{--Plan Name--}}
                            <td>
                                <div class="btn-table-align">
                                <span style="cursor: pointer;">
                                    <strong>@{{ plan.name }}</strong>
                                </span>
                                </div>
                            </td>

                            {{--Plan Features Button--}}
                            <td>
                                <button class="btn btn-default m-l-sm" @click="showPlanDetails(plan)">
                                    <i class="fa fa-btn fa-star-o"></i>Plan Features
                                </button>
                            </td>

                            {{--Plan Price--}}
                            <td>
                                <div class="btn-table-align">
                                    @{{ plan.price | currency }} / Monthly
                                </div>
                            </td>

                            {{--Plan Management Buttons--}}
                            <td class="text-right">
                                <button class="btn btn-danger"
                                        @click="approvePlanDeleting(plan)">
                                    <i class="fa fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr>
                            <div style="text-align: center">
                                <p>
                                    <i class="fa fa-shopping-bag" style="font-size: 50px; color: #a4aaae;"></i>
                                </p>
                                <h4>No Plans</h4>
                                <button type="submit" class="btn btn-primary"
                                        @click.prevent="create">
                                    <i class="fa fa-plus"></i> Create Plan
                                </button>
                            </div>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{--Modals--}}
        <div>
            {{--Delete Plan Modal--}}
            <div class="modal fade" id="modal-delete-plan" tabindex="-1" role="dialog">
                <div class="modal-dialog" v-if="deletingPlan">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button " class="close" data-dismiss="modal" aria-hidden="true">&times;
                            </button>

                            <h4 class="modal-title">
                                Delete plan (@{{ deletingPlan.name }})
                            </h4>
                        </div>

                        <div class="modal-body">
                            Are you sure you want to delete this plan?
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">No, Go Back</button>

                            <button type="button" class="btn btn-danger" @click="deletePlan"
                                    :disabled="deletingPlanProcess">
                                <span v-if="deletingPlanProcess">
                                    <i class="fa fa-btn fa-spinner fa-spin"></i>Deleting
                                </span>
                                <span v-else>
                                    Yes, Delete
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{--Plan Details Modal--}}
            @include('spark::kiosk.modals.plan-details')

            {{--Plan Management Modal--}}
            @include('spark::kiosk.modals.create-plan')
        </div>
    </div>
</spark-kiosk-plans>