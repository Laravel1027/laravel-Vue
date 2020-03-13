<?php

namespace App\Providers;

use Laravel\Spark\Spark;
use Laravel\Spark\Providers\AppServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SparkServiceProvider extends ServiceProvider
{
    /**
     * Your application and company details.
     *
     * @var array
     */
    protected $details = [
        'vendor' => 'Your Company',
        'product' => 'Your Product',
        'street' => 'PO Box 111',
        'location' => 'Your Town, NY 12345',
        'phone' => '555-555-5555',
    ];

    /**
     * The address where customer support e-mails should be sent.
     *
     * @var string
     */
    protected $sendSupportEmailsTo = null;

    /**
     * All of the application developer e-mail addresses.
     *
     * @var array
     */
    protected $developers = [
        'jaap84@gmail.com',
        'shanejohnlong@gmail.com',
		'yoandro@gmail.com',
        'viddamusic@gmail.com'
    ];

    /**
     * Indicates if the application will expose an API.
     *
     * @var bool
     */
    protected $usesApi = true;

    /**
     * Finish configuring Spark for the application.
     *
     * @return void
     */
    public function booted()
    {
        Spark::useStripe()->noCardUpFront()->trialDays(14);

        Spark::freePlan()
            ->features([
                'First', 'Second', 'Third'
            ])
            ->maxTeams(1)
            ->maxTeamMembers(6);

        Spark::plan('Basic', 'provider-id-1')
            ->price(10)
            ->features([
                'First', 'Second', 'Third'
            ]);

        Spark::swap('UserRepository@current', function () {
            if (Auth::check()) {
                return Auth::user()->load('emailNotifications', 'projects');
            }
        });

        Spark::createUsersWith(function ($request) {
            $user = Spark::user();

            $data = $request->all();
            $user->forceFill([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'last_read_announcements_at' => Carbon::now(),
                'trial_ends_at' => Carbon::now()->addDays(Spark::trialDays()),
                'activation_code' => str_random(50)
            ])->save();

            $user->emailNotifications()->create();

            return $user;
        });
    }
}
