<?php

namespace App;

use App\Mail\ResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Mail;
use Laravel\Cashier\Billable;
use Laravel\Spark\User as SparkUser;
use Laravel\Spark\CanJoinTeams;

class User extends SparkUser
{
    use CanJoinTeams;
    use Billable;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'verified',
        'activation_code',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'authy_id',
        'country_code',
        'phone',
        'card_brand',
        'card_last_four',
        'card_country',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_zip',
        'billing_country',
        'extra_billing_information',
        'activation_code',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'uses_two_factor_auth' => 'boolean',
    ];

    public function projects()
    {
        return $this->belongsToMany(Project::class)->withPivot('role')->withTimestamps();
    }

    /**
     * Get user email notifications settings
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function emailNotifications() {
        return $this->hasOne(EmailNotificationSettings::class);
    }

    /**
     * Get the token that belongs to user.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne|\Laravel\Spark\Token
     */
    public function token()
    {
        return $this->hasOne(APIToken::class);
    }

    public function unreadComments()
    {
        return $this->hasMany(UnreadComment::class, 'user_id');
    }

    /**
     * Get subscribed plan details
     * @return mixed
     */
    public function currentPlan()
    {
        if ($this->current_billing_plan) {
            return Plan::find($this->current_billing_plan);
        }
        return null;
    }

    public function sendPasswordResetNotification($token)
    {
        $email = $this->getEmailForPasswordReset();

        Mail::to($email)->send(new ResetPassword($token));
    }
}
