<create-plan inline-template>
    <div>
        <div class="modal fade" id="modal-create-plan" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button " class="close" data-dismiss="modal" aria-hidden="true">&times;</button>

                        <h4 class="modal-title">
                            Create new plan
                        </h4>
                    </div>

                    <div class="modal-body">
                        <!-- Create Plan Form -->
                        <form class="form-horizontal" role="form">
                            {{--Basic Info--}}
                            <div class="form-group" :class="{'has-error': form.errors.has('name')}">
                                <label class="col-md-4 control-label">
                                    <span>Name</span>
                                </label>

                                <div class="col-md-6">
                                    <input type="text" class="form-control" v-model="form.name">

                                    <span class="help-block" v-show="form.errors.has('name')">
                                        @{{ form.errors.get('name') }}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="{'has-error': form.errors.has('description')}">
                                <label class="col-md-4 control-label">
                                    <span>Description</span>
                                </label>

                                <div class="col-md-6">
                                    <textarea class="form-control" v-model="form.description" rows="3">@{{ form.description }}</textarea>
                                    {{--<input type="text" class="form-control" v-model="form.description">--}}

                                    <span class="help-block" v-show="form.errors.has('description')">
                                        @{{ form.errors.get('description') }}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="{'has-error': form.errors.has('price')}">
                                <label class="col-md-4 control-label">
                                    <span>Price</span>
                                </label>

                                <div class="col-md-6">
                                    <input type="number" class="form-control" v-model="form.price">

                                    <span class="help-block" v-show="form.errors.has('price')">
                                        @{{ form.errors.get('price') }}
                                    </span>
                                </div>
                            </div>

                            {{--Plan Features--}}
                            <div class="form-group" :class="{'has-error': form.errors.has('teams_count')}">
                                <label class="col-md-4 control-label">
                                    <span>Teams count</span>
                                </label>

                                <div class="col-md-6">
                                    <input type="number" class="form-control" v-model="form.teams_count">

                                    <span class="help-block" v-show="form.errors.has('teams_count')">
                                        @{{ form.errors.get('teams_count') }}
                                    </span>
                                </div>
                            </div>

                            <div class="form-group" :class="{'has-error': form.errors.has('teams_members_count')}">
                                <label class="col-md-4 control-label">
                                    <span>Teams members count</span>
                                </label>

                                <div class="col-md-6">
                                    <input type="number" class="form-control" v-model="form.teams_members_count">

                                    <span class="help-block" v-show="form.errors.has('teams_members_count')">
                                        @{{ form.errors.get('teams_members_count') }}
                                    </span>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Modal Actions -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>

                        <button type="button" class="btn btn-primary" @click="create" :disabled="form.busy">
                            <span v-if="form.busy">
                                <i class="fa fa-btn fa-spinner fa-spin"></i>Creating
                            </span>

                            <span v-else>
                                Create
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</create-plan>