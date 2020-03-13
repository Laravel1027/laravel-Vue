<div class="panel panel-default">
    <div class="panel-heading">
        Subscribe
    </div>

    <div class="panel-body table-responsive">
        {{--Subscription Notice--}}
        <template v-if="activeSubscription">
            <div class="p-b-lg" v-if="activeSubscription.ends_at">
                Your subscription ends at <b>@{{ activeSubscription.ends_at | utc_to_local}}</b>
            </div>
        </template>
        <template v-else>
            <div class="p-b-lg" >
                You are not subscribed to a plan. Choose from the plans below to get started.
            </div>
        </template>

        {{--Plan Error Message--}}
        <div class="alert alert-danger" v-if="form.errors.has('plan')">
            @{{ form.errors.get('plan') }}
        </div>

        {{--All Plans--}}
        <div class="col-md-12" style="position: relative">
            <div class="loader" v-if="loading"
                 style="position:absolute; left: 0; height: 100%; width: 100%; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,.9);">
                <i class="fa fa-btn fa-spinner fa-spin" style="font-size: 50px"></i>
            </div>
            <table class="table table-borderless m-b-none">
                <thead></thead>
                <tbody>
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

                        <!-- Plan Select Button -->
                        <td class="text-right">
                            <template v-if="activePlan && activePlan.id == plan.id">
                                <button class="btn btn-success"
                                        disabled>
                                    <i class="fa fa-pause" v-if="activeSubscription.ends_at"></i>
                                    <i class="fa fa-check" v-else></i>
                                </button>

                                <button v-if="activeSubscription.ends_at"
                                        class="btn btn-warning btn-plan"
                                        @click="approveResumeSubscription(plan, activeSubscription)">
                                    Resume
                                </button>
                                <button v-else class="btn btn-danger btn-plan"
                                        @click="approveCancelSubscription(plan)">
                                    Cancel
                                </button>
                            </template>
                            <template v-else-if="selectedPlan !== plan">
                                <button class="btn btn-primary-outline btn-plan"
                                        @click="selectPlan(plan)"
                                        :disabled="form.busy">
                                    Select
                                </button>
                            </template>
                            <button class="btn btn-primary btn-plan"
                                    v-if="selectedPlan === plan"
                                    disabled>
                                <i class="fa fa-btn fa-check"></i>Selected
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{--Plan Details Modal--}}
@include('spark::settings.subscription.modals.plan-details')

{{--Resume Subscription Modal--}}
@include('spark::settings.subscription.modals.resume-subscription')

{{--Cancel Subscription modal--}}
@include('spark::settings.subscription.modals.cancel-subscription')