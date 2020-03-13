<kiosk-users inline-template>
    <div class="panel panel-default">
        <div class="panel-heading">
            Users
        </div>
        <div class="panel-body">
            {{--Search--}}
            <div class="form-group m-b-none">
                <div class="col-md-12">
                    <input type="text" id="kiosk-users-search" class="form-control"
                           name="search"
                           placeholder="Search By Name Or E-Mail Address..."
                           v-model="searchString"
                           style="margin-bottom: 60px !important;">
                </div>
            </div>

            {{--All Users List--}}
            <div class="col-md-12" style="position: relative">
                <div class="loader" v-if="loader"
                     style="position:absolute; left: 0; height: 100%; width: 100%; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,.9);">
                    <i class="fa fa-btn fa-spinner fa-spin" style="font-size: 50px"></i>
                </div>
                <table class="table table-borderless m-b-none">
                    <thead v-if="users.length">
                    <tr style="text-align: center">
                        <th scope="col">Thumbnail</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <template v-if="users.length">
                        <tr v-for="user in users">
                            {{--Thumb--}}
                            <td>
                                <img :src="user.photo_url" class="spark-profile-photo"
                                     style="border: none !important; padding: unset !important;">
                            </td>

                            {{--Name--}}
                            <td>
                                <div class="btn-table-align">
                                    <span>
                                        <strong>@{{ user.name }}</strong>
                                    </span>
                                </div>
                            </td>

                            {{--Email--}}
                            <td>
                                <div class="btn-table-align">
                                    <span>
                                        <strong>@{{ user.email }}</strong>
                                    </span>
                                </div>
                            </td>


                            {{--User Management Buttons--}}
                            <td>
                                <button class="btn btn-default"
                                        @click="showUserDetails(user)">
                                    <i class="fa fa-search"></i>
                                </button>

                                <button class="btn btn-danger"
                                        @click="approveUserDeleting(user)">
                                    <i class="fa fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template v-else>
                        <tr>
                            <div style="text-align: center">
                                <p>
                                    <i class="fa fa-users" style="font-size: 50px; color: #a4aaae;"></i>
                                </p>
                                <h4>No Users</h4>
                            </div>
                        </tr>
                    </template>
                    </tbody>
                </table>
            </div>
        </div>

        {{--Modals--}}
        <div>
            {{--Delete User Modal--}}
            <div class="modal fade" id="modal-delete-user" tabindex="-1" role="dialog">
                <div class="modal-dialog" v-if="deletingUser">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button " class="close" data-dismiss="modal" aria-hidden="true">&times;
                            </button>

                            <h4 class="modal-title">
                                Delete user (@{{ deletingUser.name }})
                            </h4>
                        </div>

                        <div class="modal-body">
                            <p>Are you sure you want to delete this user?</p>
                            <small><b>Note: This will also delete all project data assigned to this user.</b></small>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">No, Go Back</button>

                            <button type="button" class="btn btn-danger" @click="deleteUser"
                                    :disabled="deletingUserProcess">
                                <span v-if="deletingUserProcess">
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

            {{--User Details Modal--}}
            @include('spark::kiosk.modals.user-details')
        </div>
    </div>
</kiosk-users>