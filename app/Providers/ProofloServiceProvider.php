<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ProofloServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'App\Contracts\IProjectService',
            'App\Services\ProjectService'
        );

        $this->app->bind(
            'App\Contracts\IFileService',
            'App\Services\FileService'
        );

        $this->app->bind(
            'App\Contracts\IProofService',
            'App\Services\ProofService'
        );

        $this->app->bind(
            'App\Contracts\IRevisionService',
            'App\Services\RevisionService'
        );

        $this->app->bind(
            'App\Contracts\IEmailNotificationSettingsService',
            'App\Services\EmailNotificationSettingsService'
        );

        $this->app->bind(
            'App\Contracts\INotificationService',
            'App\Services\NotificationService'
        );

        $this->app->bind(
            'App\Contracts\API\V1\IAPIProjectService',
            'App\Services\API\V1\APIProjectProjectService'
        );

        $this->app->bind(
            'App\Contracts\API\V1\IAPIAuthService',
            'App\Services\API\V1\APIAuthService'
        );

        $this->app->bind(
            'App\Contracts\IUserService',
            'App\Services\UserService'
        );

        $this->app->bind(
            'App\Contracts\IUnreadCommentsService',
            'App\Services\UnreadCommentsService'
        );

        $this->app->bind(
            'App\Contracts\ITeamService',
            'App\Services\TeamService'
        );

        $this->app->bind(
            'App\Contracts\IPlanService',
            'App\Services\PlanService'
        );

        $this->app->bind(
            'App\Contracts\IStripeService',
            'App\Services\StripeService'
        );

        $this->app->bind(
            'App\Contracts\IBootstrapService',
            'App\Services\BootstrapService'
        );

        $this->app->bind(
            'App\Contracts\Admin\IUserService',
            'App\Services\Admin\UserService'
        );

        $this->app->bind(
            'App\Contracts\Admin\IProjectService',
            'App\Services\Admin\ProjectService'
        );
    }
}
