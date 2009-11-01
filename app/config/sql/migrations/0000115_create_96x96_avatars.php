<?php

class FileExtensionStripper {
	const FILE_EXTENSION_LENGTH = 4;
	
	public static function strip($filename) {
		return substr($filename, 0, -self::FILE_EXTENSION_LENGTH);
	}
}

class ImagesWithoutMediumVersionFilter extends FilterIterator {
	public function __construct(Iterator $iterator) {
		parent::__construct($iterator);
	}
	
	public function accept() {
		$filename = $this->getInnerIterator()->current();
		
		if ($this->isJPEG($filename) && $this->isLargeImage($filename)) {
			$filename = FileExtensionStripper::strip($filename);
			
			if (!file_exists($this->getInnerIterator()->getPath().DS.$filename.'-medium.jpg')) {
				return true;
			}
		}
		
		return false;
	}
	
	private function isLargeImage($filename) {
		// smaller images use a postfix ("-small" or "-medium")
		return (strpos($filename, '-') === false);
	}
	
	private function isJPEG($filename) {
		return strpos($filename, '.jpg');
	}
}

App::import('Vendor', 'ImageResizer');
$originalSize = new ImageSize(150, 150);
$mediumSize = new ImageSize(96, 96);

App::import('Component', 'Cdn');
$cdn = new CdnComponent();

$iterator = new ImagesWithoutMediumVersionFilter(new DirectoryIterator(AVATAR_DIR));

foreach ($iterator as $filename) {
	$image = imagecreatefromjpeg(AVATAR_DIR.$filename);
	$filenameWithoutExtension = FileExtensionStripper::strip($filename);
	$newFilename = $filenameWithoutExtension . '-medium.jpg';
	ImageResizer::resizeAndSaveJPEG($image, $originalSize, $mediumSize, AVATAR_DIR . $newFilename);
	
	if (Configure::read('NoseRub.use_cdn')) {
		$cdn->copyTo(AVATAR_DIR . $newFilename, 'avatars/' . $newFilename);
	}
}