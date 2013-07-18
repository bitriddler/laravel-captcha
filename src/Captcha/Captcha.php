<?php namespace Captcha;

use Exception;

class Captcha {

	const SESSION_NAME = 'CCo13De';

	protected $width;
	protected $height;
	protected $noChars;
	protected $code;

	protected $possibleLetters = '23456789bcdfghjkmnpqrstvwxyz';
	protected $randomDots = 10;
	protected $randomLines = 30;
	protected $captchaTextColor="0x142864";
	protected $captchaNoiceColor = "0x142864"; 

	protected static $fontPath = '';

	/**
	 * Constructor.
	 *
	 * @param  int $width
	 * @param  int $height
	 * @param  int $noChars
	 * @return void
	 */
	public function __construct( $width, $height, $noChars = 5 )
	{
		$this->width   = $width;
		$this->height  = $height;
		$this->noChars = $noChars;
	}

	/**
	 * Initialize Captcha class.
	 *
	 * @param  string $fontPath
	 * @return void
	 */
	public static function init( $fontPath )
	{
		static::$fontPath = $fontPath;
	}

	/**
	 * Generate image and show it in the browser.
	 *
	 * @return void
	 */
	public function generateImage()
	{
		$this->generateCode();

		$font_size = $this->height * 0.75; 
		$image = @imagecreate($this->width, $this->height);

		/* setting the background, text and noise colours here */ 
		$background_color = imagecolorallocate($image, 255, 255, 255);

		$arr_text_color = $this->hexrgb($this->captchaTextColor); 
		$text_color = imagecolorallocate($image, $arr_text_color['red'], 
		$arr_text_color['green'], $arr_text_color['blue']);

		$arr_noice_color = $this->hexrgb($this->captchaNoiceColor); 
		$image_noise_color = imagecolorallocate($image, $arr_noice_color['red'], 
		$arr_noice_color['green'], $arr_noice_color['blue']);

		/* generating the dots randomly in background */ 
		for( $i=0; $i < $this->randomDots; $i++ ) {

			imagefilledellipse($image, mt_rand(0, $this->width),
			mt_rand(0,$this->height), 2, 3, $image_noise_color);
		}

		/* generating lines randomly in background of image */ 
		for( $i=0; $i < $this->randomLines; $i++ ) {

			imageline($image, mt_rand(0,$this->width), mt_rand(0,$this->height),
			mt_rand(0, $this->width), mt_rand(0, $this->height), $image_noise_color);
		}

		/* create a text box and add 6 letters code in it */ 
		$textbox = imagettfbbox($font_size, 0, $this->getFont(), $this->code); 
		$x = ($this->width - $textbox[4])/2;
		$y = ($this->height - $textbox[5])/2;
		imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->getFont() , $this->code);

		/* Show captcha image in the page html page */ 
		header('Content-Type: image/jpeg');// defining the image type to be shown in browser widow
		imagejpeg($image);//showing the image
		imagedestroy($image);//destroying the image instance
	}

	/**
	 * Get hex rgb.
	 * 
	 * @param  string $hexstr
	 * @return array
	 */
	private function hexrgb($hexstr) 
	{
		$int = hexdec($hexstr);

		return array(
			"red" => 0xFF & ($int >> 0x10),
			"green" => 0xFF & ($int >> 0x8),
			"blue" => 0xFF & $int
		);
	}

	/**
	 * Get font path.
	 *
	 * @return void
	 */
	private function getFont()
	{
		if(! $this->fontPath) throw new Exception('Captcha class not initialized.');

		return $this->fontPath;
	}

	/**
	 * Generate code.
	 *
	 * @return void
	 */
	private function generateCode()
	{
		$this->code = ''; 

		for($i = 0; $i < $this->noChars; $i++)
		{ 
			$this->code .= substr($this->possibleLetters, mt_rand(0, strlen($this->possibleLetters)-1), 1);
		}
	}

	/**
	 * Save code into the session.
	 *
	 * @return void
	 */
	public function saveSession()
	{
		if(! session_id()) throw new Exception('Session hasn\'t been started yet.');

		$_SESSION[static::SESSION_NAME] = $this->code;
	}

	/**
	 * Validate given string;
	 *
	 * @param  string $string
	 * @return boolean
	 */
	public static function validate( $string )
	{
		if(! session_id()) throw new Exception('Session hasn\'t been started yet.');

		$session = isset($_SESSION[static::SESSION_NAME]) ? $_SESSION[static::SESSION_NAME] : false;
		
		return $session && $session == $string;
	}
}