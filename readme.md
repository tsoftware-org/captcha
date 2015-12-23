
##Introduction
Tsoftware\Captcha is a captcha generator in Laravel 5!
    
##Useage
First:
```C
composer require tsoftware/captcha
```



in app/config.php add
```C
  'providers' => [
    Tsoftware\Captcha\CaptchaProvider::class,
  ]

  'aliases' => [
    'Captcha' => Tsoftware\Captcha\CaptchaFacade::class,
  ]
```

for emaple in app/Http/Controllers/Auth/AuthController.php file
```C
  use Captcha;

  public function getCaptcha()
  {
    return Captcha::output('_captcha', 100, 40, 4);
  }
  
  
  protected function validator(array $data)
  {
        $validator = Validator::make($data, [
            'name' => 'required|min:5|max:20',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|confirmed|min:5',
            'captcha' => 'required',
        ]);

	    $validator->after(function($validator) use ($data){
			if (!Captcha::check($data['captcha']))
			{
				$validator->errors()->add('captcha', 'Wrong captcha code!');
			}
	    });

	    return $validator;
  }
  
```


##FeedBack

* Mail(admin@yantao.info)
* QQ: 1065317290
* Blog: [Yantao.Info](http://www.yantao.info)
* GitHub: [tsoftware-org/captcha](https://github.com/tsoftware-org/captcha)
