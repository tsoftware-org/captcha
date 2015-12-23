<?php

namespace Tsoftware\Captcha;

use Illuminate\Support\Facades\Session;

class Captcha
{
	private $captchaCharList;
	private $tmpImage;
	private $image;
	private $width;
	private $height;
	private $codeLength;
	private $textColor;
	private $sessionName;


	/**
	 * @param string $sessionName
	 * @param int $width
	 * @param int $height
	 * @param int $codeLength
	 * @return \Symfony\Component\HttpFoundation\Response|\Illuminate\Contracts\Routing\ResponseFactory
	 */
	public function output($sessionName = '_captcha', $width = 110, $height = 40, $codeLength = 4)
	{
		$this->sessionName = $sessionName;
		$this->width = $width;
		$this->height = $height;
		$this->codeLength = $codeLength;
		$this->genImage();
		return response('')->header('Content-Type', 'image/gif');
	}


	public function check($captcha = null, $sessionName = '_captcha')
	{
		return Session::pull($sessionName) === strtoupper($captcha);
	}


	private function genImage()
	{
		$this->createCanvas();
		$this->genCaptchaCharList();
		$this->drawWords();
		$this->drawLines();
		$this->twistChar();
		$this->drawDisturb();
		$this->genStream();
	}

	private function createCanvas()
	{
		$this->tmpImage = imagecreatetruecolor($this->width, $this->height);
		$bgColor = imagecolorallocate($this->tmpImage, 255, 255, 255);
		@imagefill($this->tmpImage, 0, 0, $bgColor);
	}


	private function genCaptchaCharList()
	{
		$charList = '3456789abcdefghijkmnpqrstuvwxyABCDEFGHJKMNPQRSTUVWXY';
		for ($i = 0; $i < $this->codeLength; $i++){
			$this->captchaCharList.= $charList[rand(0, strlen($charList) - 1)];
		}
		Session::put($this->sessionName, strtoupper($this->captchaCharList));
	}


	private function drawWords()
	{
		$font = dirname(__DIR__).'/fonts/Bohemian.ttf';
		$colorArray = array(
			array(28,78,180),
			array(22,162,35),
			array(209,30,1),
			array(219,71,47),
			array(211,43,4),
		);
		$colorIndex = array_rand($colorArray);
		$this->textColor = imagecolorallocate($this->tmpImage, $colorArray[$colorIndex][0], $colorArray[$colorIndex][1], $colorArray[$colorIndex][2]);

		$charPosArray = array();
		$posX = 10;

		for ($i = 0; $i < strlen($this->captchaCharList); $i++){
			$angleRand = rand(-25, 25);
			$fontSize = rand($this->width / $this->codeLength - 5, $this->width / $this->codeLength + 10);
			$posY = rand($fontSize + 5, $this->height - 5);

			$charPosArray[] = array($posX, $posY, $angleRand, $fontSize);
			$posX += $fontSize - 5;
		}

		for ($i = 0; $i < count($charPosArray); $i++){
			$tmpChar = substr($this->captchaCharList, $i, 1);
			imagettftext($this->tmpImage, (double)$charPosArray[$i][3], (double)$charPosArray[$i][2], (int)$charPosArray[$i][0], (int)$charPosArray[$i][1], $this->textColor, $font, $tmpChar);
		}
	}

	private function drawLines()
	{
		$lineCnt = mt_rand(4, 8);

		for ($i = 0; $i < $lineCnt; $i++){
			imagesetthickness($this->tmpImage, rand(1, 3));
			imagearc($this->tmpImage, mt_rand(-10, $this->width), mt_rand(-10, $this->height), mt_rand(30,300), mt_rand(20,200), mt_rand(80, 360), mt_rand(0, 300), $this->textColor);
		}
	}

	private function twistChar()
	{
		$this->image = imagecreatetruecolor ($this->width, $this->height);
		$bgColor = imagecolorallocate($this->tmpImage, 255, 255, 255);
		@imagefill($this->image, 0, 0, $bgColor);

		for ( $SrcX = 0; $SrcX < $this->width; $SrcX++) {
			for ($SrcY = 0; $SrcY < $this->height; $SrcY++) {
				$pointRgb = imagecolorat($this->tmpImage, $SrcX , $SrcY);
				//keep y same;
				$DescY = $SrcY;
				//change x like sin
				$DescX = $SrcX + sin($SrcY / $this->height * 2 * M_PI - M_PI * rand(0.1, 0.8)) * 4;
				imagesetpixel($this->image, (int)$DescX, (int)$DescY, $pointRgb);
			}
		}
	}


	private function drawDisturb()
	{
		$count = 160;
		for($i = 0; $i < $count; $i++){
			imagesetpixel($this->image, mt_rand() % $this->width , mt_rand() % $this->height , $this->textColor);
		}

	}


	private function genStream()
	{
		if (imagetypes() & IMG_GIF)
		{
			ob_clean();
			header('Content-type: image/gif');
			imagegif($this->image);
		}
	}


	public function __destruct()
	{
		if ($this->tmpImage)
		{
			imagedestroy($this->tmpImage);
		}

		if ($this->image)
		{
			imagedestroy($this->image);
		}
	}

}