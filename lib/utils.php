<?php

// shorthand escaped print function for use in templates
function p($s) { 
	echo htmlspecialchars($s);
}

function setFileMode($path) {
	$oldUmask = umask(0);
	$success = @chmod($path, CONFIG::FILE_CREATION_MODE);
	umask($oldUmask);
}

// glob returns 'false' for empty directories on some 
// PHP versions; fix it to always return an empty array.
function saneGlob($path, $mode = null) {
	$files = glob($path, $mode);
	return empty($files) ? array() : $files;
}
