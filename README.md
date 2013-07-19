# Captcha
An easy class to create and validate captcha images


## Usage Example
**Create a new file which can be accessed through url and add these few lines**

e.g. request_captcha.php => http://www.example.com/php_scripts/request_captcha.php
```php
use Captcha\Captcha;

spl_autoload_register();

session_start();

Captcha::init('{path to any font you want on your server}');

$captcha = new Captcha(130, 60, 5);

$captcha->generateImage();

$captcha->saveSession();
```

**Now to generate image in the view**
```php
<img src="http://www.example.com/php_scripts/request_captcha.php" />
```

**Last step: validating user input with the last generated captcha**
```php
session_start();

if(Captcha::validate( $userInput ))
{
	// User input validated successfully.
}
```