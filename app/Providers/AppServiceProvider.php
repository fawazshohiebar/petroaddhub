<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Statamic\Statamic;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('http');
        }

        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('azure', \SocialiteProviders\Azure\Provider::class);
        });

        // allow gate to viewApiDocs
        \Illuminate\Support\Facades\Gate::define('viewApiDocs', function ($user = null) {
            // check if request has header x-api-key and if it is equal to the value in .env file
            // if(Auth::check()) {
            //     return true;
            // }
            // if (in_array($user->email ?? '', [
            //     'ali.awwad@modon.com',
            //     'ali.awwad@adnec.ae',
            // ])) {
            //     return true;
            // }
            return true;
        });

        Statamic::vite('app', [
            'resources/js/cp.js',
            'resources/css/cp.css',
        ]);
    }
}
