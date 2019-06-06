<?php


namespace Denismitr\Bloom;


use Illuminate\Support\ServiceProvider;

class BloomServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . './../config/bloom.php' => config_path('bloom.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . './../config/bloom.php', 'bloom');

        $this->registerBloomManager();
    }

    protected function registerBloomManager()
    {
        $this->app->singleton('bloom', function($app) {
            return new BloomManager($app['config']['bloom']);
        });
    }
}