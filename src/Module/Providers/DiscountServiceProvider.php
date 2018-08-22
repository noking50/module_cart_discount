<?php

namespace Noking50\Modules\Cart\Discount\Providers;

use Illuminate\Support\ServiceProvider;
use Noking50\Modules\Cart\Discount\DiscountManager;

class DiscountServiceProvider extends ServiceProvider {

    public function boot() {
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'module_cart_discount');
//        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
//        $this->loadViewsFrom(__DIR__.'/../views', 'module_banner_carousel');
        $this->publishes([
            __DIR__ . '/../../config/module_cart_discount.php' => config_path('module_cart_discount.php'),
            __DIR__ . '/../../lang' => resource_path('lang/vendor/module_cart_discount'),
//            __DIR__.'/../views' => base_path('resources/views/vendor/module_banner_carousel'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->mergeConfigFrom(
                __DIR__ . '/../../config/module_cart_discount.php', 'module_cart_discount'
        );
        $this->app->singleton('module_cart_discount', function ($app) {
            return new DiscountManager();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return ['module_cart_discount'];
    }

}
