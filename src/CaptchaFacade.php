<?php

namespace Tsoftware\Captcha;
use Illuminate\Support\Facades\Facade;

class CaptchaFacade extends Facade
{
	/**
	 * Get the registered name of the captcha component binding from the application container .
	 *
	 * @return string
	 */
	protected static function getFacadeAccessor()
	{
		return 'captcha';
	}
}