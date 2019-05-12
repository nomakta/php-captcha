<?php 

class captcha {


    public function __constructor() {
        (!extension_loaded('gd')) ?? die("PHP gd extension doesn't seem to be installed");
    }

    private function generateChallenge($length = 6) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function createCaptcha($backgroundImage = "/captcha/background.png", $fontFile = "/captcha/verdana.ttf", $backgroundSizeX = 2000, $backgroundSizeY = 350, $sizeX = 200, $sizeY = 50)  {
        $secret = $this->generateChallenge();
        $_SESSION['captcha'] = $secret;
        $length = strlen($secret);
        $fontSize = rand(14,24);
        $angle = rand(-5, 5);

        $backgroundOffsetX = rand(0, $backgroundSizeX - $sizeX - 1);
        $backgroundOffsetY = rand(0, $backgroundSizeY - $sizeY - 1);
        $textX = rand(0, (int)($sizeX - 0.9 * $length * $fontSize));
        $textY = rand((int)(1.25 * $fontSize), (int)($sizeY - 0.2 * $fontSize));

        if(function_exists('imagecreatetruecolor')) {
            $dst_im = imagecreatetruecolor($sizeX, $sizeY);
            $resizeResult = imagecopyresampled($dst_im, imagecreatefrompng(dirname(__DIR__).$backgroundImage), 0, 0, $backgroundOffsetX, $backgroundOffsetY, $sizeX, $sizeY, $sizeX, $sizeY);

        }else{
            $dst_im = imagecreate( $sizeX, $sizeY );
            $resizeResult = imagecopyresized($dst_im, imagecreatefrompng(dirname(__DIR__).$backgroundImage), 0, 0, $backgroundOffsetX, $backgroundOffsetY, $sizeX, $sizeY, $sizeX, $sizeY);
        }

        $color = imagecolorallocate($dst_im, rand(0, 127), rand(0, 127), rand(0, 127));
        imagettftext($dst_im, $fontSize, -$angle, $textX, $textY, $color, dirname(__DIR__).$fontFile, $secret);
	    header("Content-Type: image/png");
        imagepng($dst_im);
        imagedestroy($src_im);
        imagedestroy($dst_im);
    }

}

