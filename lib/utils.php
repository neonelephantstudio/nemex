<?php

// shorthand escaped print function for use in templates
function p($s) { 
	echo htmlspecialchars($s);
}

function setFileMode($path) {
	$oldUmask = umask(0);
	$success = @mkdir($path, CONFIG::FILE_CREATION_MODE);
	umask($oldUmask);
}
