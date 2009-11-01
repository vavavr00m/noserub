<?php

App::import('Vendor', 'ImageResizer');

class ImageSizeTest extends CakeTestCase {
	public function testImageSize() {
		$width = 10;
		$height = 20;
		$imageSize = new ImageSize($width, $height);
		$this->assertEqual($width, $imageSize->getWidth());
		$this->assertEqual($height, $imageSize->getHeight());
	}
}