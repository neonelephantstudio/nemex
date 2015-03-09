<?php

class CONFIG {
	const USER = 'nemex';
    // create your own SHA-1 hash so your password isn't readable when this file is compromised > use for instance http://www.sha1-online.com/
	const PASSWORD = '9bfd99f9e2f1c59a3f7aa00c256e1fbdbfd41ee3';

	const FILE_CREATION_MODE = 0777;
	const DATE_FORMAT = 'j. F Y';

	const PROJECTS_PATH = 'projects/';

	// Maximum width for Images. Beyond this, a scaled down version is
	// created
	const IMAGE_BIG_PATH = 'big/';
	const IMAGE_MAX_WIDTH = 800;
	const IMAGE_JPEG_QUALITY = 95;
	const IMAGE_SHARPEN = true;

	// The TIMEZONE setting is only used if there's no explicit
	// timezone set in the php.ini
	const TIMEZONE = 'US/Pacific'; 
}

// Set the timezone if we don't have one
if( @date_default_timezone_get() ) {
	date_default_timezone_set(CONFIG::TIMEZONE);
}
