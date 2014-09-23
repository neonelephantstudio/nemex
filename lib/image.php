<?php

class Image {
	protected $image = null;
	public $width = 0;
	public $height = 0;
	public $type = null;
	public $extension = 'img';
	public $valid = false;
	public $exif = null;
	public $exifRotated = false;
	
	
	public function __construct( $path ) {
		list( $this->width, $this->height, $this->type ) = @getImageSize( $path );

		if( $this->type == IMAGETYPE_JPEG ) {
			$this->image = imageCreateFromJPEG( $path );
			$this->extension = 'jpg';
			if( function_exists('exif_read_data') ) {
				$this->exif = exif_read_data( $path );
			}
			$this->rotateToExifOrientation();
		} 
		else if( $this->type == IMAGETYPE_PNG ) {
			$this->image = imageCreateFromPNG( $path );
			$this->extension = 'png';
		}
		else if( $this->type == IMAGETYPE_GIF ) {
			$this->image = imageCreateFromGIF( $path );
			$this->extension = 'gif';
		}
		
		if( $this->image ) {
			$this->valid = true;
		}
	}
	
	
	protected function getThumb( $thumbWidth, $thumbHeight, $sharpen = false ) {
		if( !$this->image ) { return null; }
		
		$srcX = 0;
		$srcY = 0;
		
		if( ($this->width / $this->height) > ($thumbWidth / $thumbHeight) ) {
			$zoom = ($this->width / $this->height) / ($thumbWidth / $thumbHeight);
			$srcX = ($this->width - $this->width / $zoom) / 2;
			$this->width = $this->width / $zoom;
		}
		else {
			$zoom = ($thumbWidth/$thumbHeight) / ($this->width/$this->height);
			$srcY = ($this->height - $this->height / $zoom) / 2;
			$this->height = $this->height / $zoom;
		}
		
		$thumb = imageCreateTrueColor( $thumbWidth, $thumbHeight );
		imageCopyResampled($thumb, $this->image, 0, 0, $srcX, $srcY, $thumbWidth, $thumbHeight, $this->width, $this->height);
		
		if( $sharpen && function_exists('imageconvolution') ) {
			$sharpenMatrix = array( array(-1,-1,-1), array(-1,16,-1),  array(-1,-1,-1) );
			imageConvolution( $thumb, $sharpenMatrix, 8, 0 );
		}
		
		return $thumb;
	}
	
	
	public function writeThumb( $path, $quality, $thumbWidth, $thumbHeight, $sharpen = false, $format = 'jpg' ) {
		if( !$this->image ) { return false; }
		$thumbDirName = dirname( $path );
		
		$thumb = $this->getThumb( $thumbWidth, $thumbHeight, $sharpen );
		if( $format === 'png' ) {
			imagePNG( $thumb, $path );
		}
		else {
			imageJPEG( $thumb, $path, $quality );
		}
		
		imageDestroy( $thumb );
		return true;
	}


	public function write( $path, $quality, $format = 'jpg' ) {
		if( !$this->image ) { return false; }

		if( $format === 'png' ) {
			imagePNG( $this->image, $path );
		}
		else {
			imageJPEG( $this->image, $path, $quality );
		}

		return true;
	}


	protected function rotateToExifOrientation() {
		if( !$this->exif || empty($this->exif['Orientation']) ) {
			// No Exif Orientation; nothing to do, return original.
			return;
		}

		$flip = false;
		$rotate = 0;

		switch( $this->exif['Orientation'] ) {
			case 2: $flip =  true; $rotate =   0; break;
			case 3:	$flip = false; $rotate = 180; break;
			case 4: $flip =  true; $rotate = 180; break;
			case 5: $flip =  true; $rotate = 270; break;
			case 6: $flip = false; $rotate = 270; break;
			case 7: $flip =  true; $rotate =  90; break;
			case 8: $flip = false; $rotate =  90; break;
			default: break;
		}

		if( $rotate !== 0 ) {
			$this->image = imageRotate($this->image, $rotate, 0);
			$this->width = imageSX($this->image);
			$this->height = imageSY($this->image);
			$this->exifRotated = true;
		}
		if( $flip ) {
			$mirrored = imageCreateTrueColor($this->width, $this->height);
			imageCopyResampled($mirrored, $this->image, 0, 0, $this->width, 0, $this->width, $this->height, $this->width, $this->height);
			imageDestroy($this->image);
			$this->image = $mirrored;
			$this->exifRotated = true;
		}
	}
}
