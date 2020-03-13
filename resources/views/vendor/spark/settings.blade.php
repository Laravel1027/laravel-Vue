@extends('spark::layouts.app')
@section('scripts') @if (Spark::billsUsingStripe())
<script src="https://js.stripe.com/v2/"></script>
@else
<script src="https://js.braintreegateway.com/v2/braintree.js"></script>
@endif
@endsection

@section('content')
<spark-settings :user="user" :teams="teams" inline-template>
    <div class="spark-screen container">
        <div class="row">
            <!-- Tabs -->
            <div class="col-md-4">
                <div class="panel panel-default panel-flush">
                    <div class="panel-heading">
                        Settings
                    </div>

                    <div class="panel-body">
                        <div class="spark-settings-tabs">
                            <ul class="nav spark-settings-stacked-tabs" role="tablist">
                                <!-- Profile Link -->
                                <li role="presentation">
                                    <a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">
                                        <i class="fa fa-fw fa-btn fa-edit"></i>Profile
                                    </a>
                                </li>

                                <!-- Teams Link -->
                                @if (Spark::usesTeams())
                                    @if(isFreelancer(Auth::user()))
                                        <li role="presentation">
                                            <a href="#{{str_plural(Spark::teamString())}}" aria-controls="teams"
                                               role="tab" data-toggle="tab">
                                                <i class="fa fa-fw fa-btn fa-users"></i>{{ ucfirst(str_plural(Spark::teamString())) }}
                                            </a>
                                        </li>
                                    @endif
                                @endif

                                <!-- Security Link -->
                                <li role="presentation">
                                    <a href="#security" aria-controls="security" role="tab" data-toggle="tab">
                                        <i class="fa fa-fw fa-btn fa-lock"></i>Security
                                    </a>
                                </li>

                                <!-- Notifications Link -->
                                <li role="presentation">
                                    <a href="#notifications" aria-controls="security" role="tab" data-toggle="tab">
                                        <i class="fa fa-fw fa-btn fa-bell"></i>Email Notifications
                                    </a>
                                </li>

                                <!-- API Link -->
                                @if (Spark::usesApi())
                                 @if(count(Auth::user()->projects) > 0)
                                  @if(Auth::user()->projects[0]->pivot->role != 'client')
                                <li role="presentation">
                                    <a href="#api" aria-controls="api" role="tab" data-toggle="tab">
                                            <i class="fa fa-fw fa-btn fa-cubes"></i>API
                                        </a>
                                </li>
                                  @endif
                                 @else
                                <li role="presentation">
                                    <a href="#api" aria-controls="api" role="tab" data-toggle="tab">
                                            <i class="fa fa-fw fa-btn fa-cubes"></i>API
                                        </a>
                                </li>
                                @endif
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Billing Tabs -->
                @if(isFreelancer(Auth::user()))
                    <div class="panel panel-default panel-flush">
                        <div class="panel-heading">
                            Billing
                        </div>

                        <div class="panel-body">
                            <div class="spark-settings-tabs">
                                <ul class="nav spark-settings-stacked-tabs" role="tablist">
                                    <!-- Subscription Link -->
                                    <li role="presentation">
                                        <a href="#subscription" aria-controls="subscription" role="tab"
                                           data-toggle="tab">
                                            <i class="fa fa-fw fa-btn fa-shopping-bag"></i>Subscription
                                        </a>
                                    </li>
                                    <!-- Payment Method Link -->
                                    <li role="presentation">
                                        <a href="#payment-method" aria-controls="payment-method" role="tab"
                                           data-toggle="tab">
                                            <i class="fa fa-fw fa-btn fa-credit-card"></i>Payment Method
                                        </a>
                                    </li>

                                    <!-- Invoices Link -->
                                    <li role="presentation">
                                        <a href="#invoices" aria-controls="invoices" role="tab" data-toggle="tab">
                                            <i class="fa fa-fw fa-btn fa-history"></i>Invoices
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Tab Panels -->
            <div class="col-md-8">
                <div class="tab-content">
                    <!-- Profile -->
                    <div role="tabpanel" class="tab-pane active" id="profile">
    @include('spark::settings.profile')
                    </div>

                    <!-- Teams -->
                    @if (Spark::usesTeams())
                    <div role="tabpanel" class="tab-pane" id="{{str_plural(Spark::teamString())}}">
    @include('spark::settings.teams')
                    </div>
                    @endif

                    <!-- Security -->
                    <div role="tabpanel" class="tab-pane" id="security">
    @include('spark::settings.security')
                    </div>

                    <!-- Notifications -->
                    <div role="tabpanel" class="tab-pane" id="notifications">
    @include('spark::settings.email-notifications')
                    </div>

                    <!-- API -->
                    @if (Spark::usesApi())
                    <div role="tabpanel" class="tab-pane" id="api">
    @include('spark::settings.api')
                    </div>
                    @endif

                    <!-- Billing Tab Panes -->
                    @if (Spark::canBillCustomers()) @if (Spark::hasPaidPlans())
                    <!-- Subscription -->
                    <div role="tabpanel" class="tab-pane" id="subscription">
                        <div v-if="user">
    @include('spark::settings.subscription')
                        </div>
                    </div>
                    @endif

                    <!-- Payment Method -->
                    <div role="tabpanel" class="tab-pane" id="payment-method">
                        <div v-if="user">
    @include('spark::settings.payment-method')
                        </div>
                    </div>

                    <!-- Invoices -->
                    <div role="tabpanel" class="tab-pane" id="invoices">
    @include('spark::settings.invoices')
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</spark-settings>
@endsection