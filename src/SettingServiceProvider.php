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

        $this->app->bind('setting', function($app) {
            $path = config('setting.path');
            $filename = config('setting.filename');
            return new Setting($path, $filename, $app['config']['setting::setting.fallback'] ? new LaravelFallbackInterface() : null);
        }); 
    }
}
