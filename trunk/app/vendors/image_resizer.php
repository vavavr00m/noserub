<?php

class ImageResizer {
	const BEST_QUALITY = 100;
	
	public static function resizeAndSaveJPEG($picture, ImageSize $originalSize, ImageSize $newSize, $filename) {
    	if ($originalSize != $newSize) {
            $resampled = imagecreatetruecolor($newSize->getWidth(), $newSize->getHeight());
            imagecopyresampled($resampled, $picture, 0, 0, 0, 0, imagesx($resampled), imagesy($resampled), $originalSize->getWidth(), $originalSize->getHeight());
            imagejpeg($resampled, $filename, self::BEST_QUALITY); 
        } else {
            imagejpeg($picture, $filename, self::BEST_QUALITY);
        }
	}
}

class ImageSize {
	private $width = null;
	private $height = null;
	
	public function __construct($width, $height) {
		$this->width = $width;
		$this->height = $height;
	}
	
	public function getHeight() {
		return $this->height;
	}
	
	public function getWidth() {
		return $this->width;
	}
}