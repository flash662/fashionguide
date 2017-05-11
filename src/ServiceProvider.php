<?php

namespace FashionGuide\Oauth2;

use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{
    protected $defer = true;
    
    /**
     * @return string
     */
    protected function configPath()
    {
        return __DIR__ . '/../config/fashionguide.php';
    }
    
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = $this->configPath();
        $publishPath = config_path('fashionguide.php');

        $this->publishes([$configPath => $publishPath]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'fashionguide');

        $this->registerFashionGuide();
        $this->registerTokenStorage();
    }

    /**
     * @return void
     */
    private function registerFashionGuide()
    {
        $this->app->singleton('FG', function ($app) {
            return new FashionGuide($app['config']);
        });
    }

    /**
     * @return void
     */
    private function registerTokenStorage()
    {
        $this->app->singleton('FG.tokenStorage', function ($app) {
            return new TokenStorage();
        });
    }

    /**
     * @return array
     */
    public function provides()
    {
        return ['FG'];
    }
}