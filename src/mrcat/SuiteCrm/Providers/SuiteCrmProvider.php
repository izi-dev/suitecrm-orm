<?php

namespace MrCat\SuiteCrm\Providers;

use MrCat\SuiteCrm\Http\Api;
use Illuminate\Support\ServiceProvider;

class SuiteCrmProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('suitecrm.php'),
        ], 'suitecrm');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        Api::config([
            'base_uri' => config('suitecrm.api.base_uri'),
            'uri'      => config('suitecrm.api.uri'),
        ])->addSession(
            config('suitecrm.api.user'),
            config('suitecrm.api.password')
        );
    }
}
