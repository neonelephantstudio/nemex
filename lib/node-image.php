<?php

require_once(NX_PATH.'lib/node.php');
require_once(NX_PATH.'lib/image.php');

class NodeImage extends Node {
	protected $extension = 'jpg';
	public $type = 'image';

	public $editable = false;

	public static function open($path) {
		return is_file($path)
			? new self($path)
			: null;
	}

	public static function createFromUpload($basePath, $uploadPath) {
		$image = new Image($uploadPath);
		if( !$image->valid ) {
			return null;
		}

		$targetName = self::getNewName($image->extension);
		$scaledTargetPath = $basePath.$targetName;
		$originalTargetPath = $scaledTargetPath;

		// Do we want to create a scaled down version of this image?
		if( $image->width > CONFIG::IMAGE_MAX_WIDTH ) {
			$scaledWidth = CONFIG::IMAGE_MAX_WIDTH;
			$scaledHeight = ($scaledWidth/$image->width) * $image->height;
			$image->writeThumb(
				$scaledTargetPath, CONFIG::IMAGE_JPEG_QUALITY, 
				$scaledWidth, $scaledHeight, 
				CONFIG::IMAGE_SHARPEN
			);
			setFileMode($scaledTargetPath);

			// We created a scaled down version, so the original has to be moved
			// in a separate big/ folder
			$originalTargetPath = $basePath.CONFIG::IMAGE_BIG_PATH.$targetName;
		}

		// If the image had an exif orientation, save the rotated version
		// and delete the original.
		if( $image->exifRotated ) {
			$image->write($originalTargetPath, CONFIG::IMAGE_JPEG_QUALITY);
			unlink($uploadPath);
		}
		// No EXIF orientation? Just move the original.
		else {
			move_uploaded_file($uploadPath, $originalTargetPath);
		}
		setFileMode($originalTargetPath);

		return self::open($scaledTargetPath);
	}

	protected function getBigPathName() {
		return dirname($this->path).'/'.CONFIG::IMAGE_BIG_PATH.basename($this->path);
	}

	public function getOriginalPath() {
		$bigPath = $this->getBigPathName();
		return file_exists($bigPath)
			? $bigPath
			: $this->path;
	}

	public function delete() {
		$bigPath = $this->getBigPathName();
		if( file_exists($bigPath) ) {
			unlink($bigPath);
		}

		parent::delete();
	}
}