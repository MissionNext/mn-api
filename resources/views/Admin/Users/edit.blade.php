<form class="custom-form ng-pristine ng-valid" role="form" enctype="multipart/form-data" method="post"
      action="{{ route('dashboards.users.update.post',['user' => $item->id]) }}">
    @csrf
    <table class="table table-bordered">
        <tbody>
        <tr>
            <th>Username</th>
            <td>{{$item->username}}</td>
        </tr>
        <tr>
            <th>Email</th>
            <td>{{$item->email}}</td>
        </tr>
        <tr>
            <th>First Name</th>
            <td>{{$data->profileData->first_name}}</td>
        </tr>
        <tr>
            <th>Last Name</th>
            <td class="ng-binding">{{$data->profileData->last_name}}</td>
        </tr>
        <tr>
            <th>Country</th>
            <td>{{$data->profileData->country}}</td>
        </tr>
        <tr>
            <th>Registered</th>
            <td>{{$data->created_at}}</td>
        </tr>
        <tr>
            <th>Global Status</th>
            <td>
                <span style="text-transform: uppercase;">{{$status['name']}}</span>
                <hr>
                <p>
                    <button name="grandButton" value="1" type="submit"
                            class="btn btn-success btn-xs" {{ $status['value'] === 1 ? 'disabled': null }}>
                        Grant Access
                    </button>
                    <button name="denyButton" value="1" type="submit"
                            class="btn btn-danger btn-xs" {{ $status['value'] === 2 ? 'disabled': null }}>
                        Deny Access
                    </button>
                </p>
                <p class="help-block">
                    <span style="font-weight: bold;">Note:</span> User is notified by email on status changes.
                </p>
            </td>
        </tr>
        </tbody>
    </table>


    <div class="profile-data ng-scope">
        <a class="btn btn-default" href="{{ URL::route('dashboards.users.view', ['user' => $item->id]) }}">Full
            Profile</a>
        <button class="btn btn-danger">Delete this user from the system
            completely
        </button>

        <h3>Subscriptions:</h3>
        <p>No subscriptions</p>
        <table class="table table-hover">
            <thead>
            <tr>
                <th>Website</th>

                <th class="ng-hide">Level</th>
                <th>Start</th>
                <th>End</th>
                <th>Price $</th>
                <th>Paid $</th>
                <th>Days Left</th>
                <th>Status</th>
                <th>Comment</th>
                <th></th>
            </tr>
            </thead>

            <!-- ngRepeat: sub in subscriptions | orderBy:'id' -->
            <tbody class="ng-scope">
            <tr>
                <td class="ng-binding">Journey</td>
                <td class="ng-hide"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>
                    <label for="is_active_site0"><span style="font-weight: 100;">Is active on site</span></label>
                    <input ng-click="toggleModal(!sub.app.is_active, sub)" id="is_active_site0" type="checkbox"
                           ng-model="sub.app.is_active" ng-checked="sub.app.is_active"
                           class="ng-pristine ng-untouched ng-valid">
                </td>
                <td></td>
            </tr>
            <tr ng-hide="sub.status == 'closed'">
                <td></td>
                <td ng-show="showLevel" class="ng-hide">

                    <select style="width: auto !important;" class="form-control ng-pristine ng-untouched ng-valid"
                            ng-change="updateSub(sub, 'partnership')" ng-model="sub.partnership"
                            ng-options="level for level in sub.partnership_levels">
                        <option value="?" selected="selected"></option>
                        <option value="string:limited" label="limited">limited</option>
                        <option value="string:basic" label="basic">basic</option>
                        <option value="string:plus" label="plus">plus</option>
                    </select>
                </td>
                <td><input style="width: 100px !important;"
                           class="form-control ng-pristine ng-untouched ng-valid hasDatepicker"
                           ng-change="updateSub(sub, 'start_date')" ui-date="dateOptions" ui-date-format="yy-mm-dd"
                           ng-model="sub.start_date" id="dp1665044095314"></td>
                <td><input style="width: 100px !important;"
                           class="form-control ng-pristine ng-untouched ng-valid hasDatepicker"
                           ng-change="updateSub(sub, 'end_date')" ui-date="dateOptions" ui-date-format="yy-mm-dd"
                           ng-model="sub.end_date" id="dp1665044095315"></td>
                <td class="ng-binding">$0.00</td>
                <td><input type="number" min="0" ng-change="updateSub(sub, 'paid')"
                           class="form-control ng-pristine ng-untouched ng-valid ng-valid-min"
                           style="width: 80px !important;" ng-model="sub.paid"></td>
                <td class="ng-binding">285</td>
                <td class="ng-binding">active
                    <!--<select style="width: auto !important;" class="form-control" ng-change="updateSub(sub, 'status')" ng-model="sub.status" ng-options="status for status in sub.statuses" ></select>-->
                </td>
                <td><textarea ng-model="sub.comment" ng-change="updateSub(sub, 'comment')"
                              class="form-control ng-pristine ng-untouched ng-valid ng-binding"></textarea>
                </td>
                <td>
                    <button ng-click="toggleModalCancelSub(sub)" type="button" class="btn btn-danger btn-sm">Cancel <br>Subscription
                    </button>
                </td>
            </tr>

            </tbody><!-- end ngRepeat: sub in subscriptions | orderBy:'id' -->

        </table>
        <button ng-init="showHistory = false;" type="button" ng-click="showHistory = !showHistory;"
                class="btn btn-default ng-binding">Hide History
        </button>

        <div ng-show="showHistory" class="transaction-history">
            <h3>Transactions</h3><a name="transaction-history"></a>
            <p ng-hide="transactions.length">No Transactions</p>
            <table ng-show="transactions.length" class="table table-hover ng-hide">
                <thead>
                <tr>
                    <th>Created Date</th>
                    <th>Amount $</th>
                    <th>Comment</th>
                    <th>Transaction ID</th>
                </tr>
                </thead>
                <!-- ngRepeat: transaction in transactions -->
            </table>
        </div>

        <div id="myModal" class="modal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                                class="sr-only">Close</span></button>
                        <h4 class="modal-title">Modal title</h4>
                    </div>
                    <div class="modal-body">
                        <p>One fine body…</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="ng-modal ng-isolate-scope ng-hide" ng-show="show" show="modalDelete">
            <div class="ng-modal-overlay" ng-click="hideModal()"></div>
            <div class="ng-modal-dialog model-content" ng-style="dialogStyle">
                <div class="ng-modal-dialog-content" ng-transclude="">
                    <div class="modal-content ng-scope">
                        <div class="modal-header">
                            <button type="button" ng-click="closeDelete()" class="close" data-dismiss="modal"><span
                                    aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title"><strong class="ng-binding">Delete user ranoble.</strong></h4>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure want to delete user?</p>
                        </div>
                        <div class="modal-footer">
                            <button ng-click="deleteRedirect()" type="button" class="btn btn-danger">Yes</button>
                            <button type="button" ng-click="closeDelete()" class="btn btn-primary">No</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div>
            </div>
        </div>

        <div class="ng-modal ng-isolate-scope ng-hide" ng-show="show" show="modalShown" configurator="configurator">
            <div class="ng-modal-overlay" ng-click="hideModal()"></div>
            <div class="ng-modal-dialog model-content" ng-style="dialogStyle">
                <div class="ng-modal-dialog-content" ng-transclude="">
                    <div class="modal-content ng-scope">
                        <div class="modal-header">
                            <button type="button" ng-click="closeModal(isActiveOnSite)" class="close"
                                    data-dismiss="modal"><span aria-hidden="true">×</span><span
                                    class="sr-only">Close</span></button>
                            <h4 class="modal-title"><strong class="ng-binding"> user on .</strong></h4>
                        </div>
                        <div class="modal-body">
                            <p>Are you sure?</p>
                        </div>
                        <div class="modal-footer">
                            <button ng-click="activateOnSite(!isActiveOnSite.value)" type="button"
                                    class="btn btn-danger">Yes
                            </button>
                            <button type="button" ng-click="activateOnSite(isActiveOnSite.value)"
                                    class="btn btn-primary">No
                            </button>
                        </div>
                    </div><!-- /.modal-content -->
                </div>
            </div>
        </div>

        <div class="ng-modal ng-isolate-scope ng-hide" ng-show="show" show="modalCancelSub" configurator="">
            <div class="ng-modal-overlay" ng-click="hideModal()"></div>
            <div class="ng-modal-dialog model-content" ng-style="dialogStyle">
                <div class="ng-modal-dialog-content" ng-transclude="">
                    <div class="modal-content ng-scope">
                        <div class="modal-header">
                            <button type="button" ng-click="closeModalCancelSub()" class="close" data-dismiss="modal">
                                <span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                            <h4 class="modal-title"><strong>Cancel Subscription</strong></h4>
                        </div>
                        <div class="modal-body">
                            <p ng-show="cancelSubModel.is_recurrent &amp;&amp; cancelSubModel.authorize_id"
                               class="ng-hide">This user uses a monthly recurrent subscription, if you cancel one of the
                                subscriptions all other will be canceled as well.</p>
                            <p class="ng-binding">Are you sure? </p>
                        </div>
                        <div class="modal-footer">
                            <button ng-click="closeSubscription(cancelSubModel);" type="button" class="btn btn-danger">
                                Yes
                            </button>
                            <button type="button" ng-click="closeModalCancelSub()" class="btn btn-primary">No</button>
                        </div>
                    </div><!-- /.modal-content -->
                </div>
            </div>
        </div>
    </div>

</form>
