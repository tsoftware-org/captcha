<?php

namespace Tsoftware\Captcha;
use Illuminate\Support\ServiceProvider;

class CaptchaProvider extends ServiceProvider
{

	protected $defer = true;
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
        $this->app->bind('captcha', function($app){
	       return new Captcha;
        });
    }
}
