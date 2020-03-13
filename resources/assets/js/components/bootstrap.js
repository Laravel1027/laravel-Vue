
/*
 |--------------------------------------------------------------------------
 | Laravel Spark Components
 |--------------------------------------------------------------------------
 |
 | Here we will load the Spark components which makes up the core client
 | application. This is also a convenient spot for you to load all of
 | your components that you write while building your applications.
 */
import * as Toastr from 'toastr';
import 'toastr/build/toastr.css';
window.toastr = Toastr;
window.card = require('card');

require('./../spark-components/bootstrap');

require('./home');

require('./settings/settings');

//Email notifications component
require('./settings/email-notifications');
require('./settings/email-notifications/update-email-notifications');

//Custom Notifications component
require('./notifications/notifications');

//Custom Navbar
require('./navbar/navbar');

//Plans Management
require('./kiosk/plans');
require('./kiosk/create-plan');

//Users Management
require('./kiosk/users');

//Subscriptions
require('./settings/subscription');

//Payment Methods
require('./settings/payment-method/payment-methods-stripe');

//Teams
require('./settings/teams/create-team');
require('./settings/teams/current-teams');
require('./settings/teams/send-invitation');

require('./auth/register');