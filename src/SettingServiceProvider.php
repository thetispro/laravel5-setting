<?php namespace Thetispro\Setting;

use Illuminate\Support\ServiceProvider;
use Thetispro\Setting\interfaces\LaravelFallbackInterface;

class SettingServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->publishes([
            __DIR__ . '/config/setting.php' => config_path('setting.php'),
        ]);

        $this->app->bind(['setting' => 'Thetispro\Setting\Setting'],
                function($app) {
            return new Setting(
                    config('setting.path'), config('setting.filename'),
                    config('setting.fallback') ? new LaravelFallbackInterface() : null);
        }); 
    }
}
