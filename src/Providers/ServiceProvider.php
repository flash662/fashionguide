<?php

namespace FashionGuide\Oauth2\Providers;

use FashionGuide\Oauth2\FashionGuide;
use FashionGuide\Oauth2\TokenStorage;
use Illuminate\Support\ServiceProvider as Provider;

class ServiceProvider extends Provider
{
    protected $defer = true;
    
    /**
     * @var string
     */
    protected $groupName = 'fashionguide';

    /**
     * @return string
     */
    protected function configPath()
    {
        return __DIR__ . '/../../config/fashionguide.php';
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

        $this->publishes([$configPath => $publishPath], $this->groupName);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), $this->groupName);

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
}
