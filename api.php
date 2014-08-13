<?php
define( 'NX_PATH', realpath('./').'/' );

require_once(NX_PATH.'config.php');
require_once(NX_PATH.'lib/utils.php');
require_once(NX_PATH.'lib/session.php');
require_once(NX_PATH.'lib/ajax-controller.php');

header( 'Content-type: text/plain; Charset=UTF-8' );

$session = new Session('nemex', NX_PATH, CONFIG::USER, CONFIG::PASSWORD);
if( !$session->isAuthed() ) {
	header( "HTTP/1.1 403 Forbidden" );
	echo '{"error": "forbidden", "code": 403}';
	exit();
}

$controller = new AjaxController($session);

// Downloads may be initiated via GET; everything else is handled through the
// POST action parameter
$action = 'invalid';
if( !empty($_GET['downloadProject']) ) { $action = 'downloadProject'; }
else if( !empty($_GET['downloadNode']) ) { $action = 'downloadNode'; }
else if( !empty($_POST['action']) ) { $action = $_POST['action']; }

$func = array($controller, $action);
if( !is_callable($func) ) {
	header( "HTTP/1.1 404 Not Found" );
	echo '{"error": "not found", "code": 404}';
	exit();
}
call_user_func($func);

echo json_encode($controller->response);
