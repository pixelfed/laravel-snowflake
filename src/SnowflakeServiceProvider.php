<?php

namespace Pixelfed\Snowflake;

use Illuminate\Support\ServiceProvider;
use Pixelfed\Snowflake\Snowflake;

class SnowflakeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
     public function boot()
     {
         $this->publishes([
             __DIR__ . '/../config/snowflake.php' => config_path('snowflake.php'),
         ]);
     }

     public function register()
     {
         $this->app->singleton(Snowflake::class, function () {
             return new Snowflake();
         });
     }
}
