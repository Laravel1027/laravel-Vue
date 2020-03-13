<div class="modal fade" id="modal-user-details" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content" v-if="detailingUser">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">
                    @{{ detailingUser.name }}

                    {{--Impersonate User--}}
                    <button class="btn btn-default" @click="impersonate(detailingUser)" style="float: right">
                        <i class="fa fa-user-secret"></i>
                    </button>
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="row">
                    <!-- Profile Photo -->
                    <div class="col-md-3 text-center">
                        <img :src="detailingUser.photo_url" class="spark-profile-photo-xl">
                    </div>

                    <div class="col-md-9">
                        <!-- Email Address -->
                        <p>
                            <strong>Email Address:</strong> <a :href="'mailto:'+detailingUser.email">@{{ detailingUser.email}}</a>
                        </p>

                        <!-- Joined Date -->
                        <p>
                            <strong>Joined:</strong> @{{ detailingUser.created_at | datetime }}
                        </p>

                        <!-- Subscription -->
                        <p>
                            <strong>Subscription:</strong>

                            <span v-if="detailingUser.activePlan">
                                @{{ detailingUser.activePlan.name }}
                            </span>

                            <span v-else>
                                None
                            </span>
                        </p>

                        <!-- Teams -->
                        {{--<p>--}}
                            {{--<strong>Teams:</strong>--}}

                            {{--<template v-if="detailingUser.teams.length">--}}
                                {{--<p v-for="team in detailingUser.teams">--}}
                                    {{--<img :src="team.photo_url" class="spark-profile-photo" style="border: none !important; padding: unset !important;">--}}
                                    {{--@{{ team.name }}--}}
                                {{--</p>--}}
                            {{--</template>--}}

                            {{--<template v-else>--}}
                                {{--None--}}
                            {{--</template>--}}
                        {{--</p>--}}
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