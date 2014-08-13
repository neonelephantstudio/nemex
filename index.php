<?php

define( 'NX_PATH', realpath('./').'/' );

require_once(NX_PATH.'config.php');
require_once(NX_PATH.'lib/session.php');
require_once(NX_PATH.'lib/utils.php');
require_once(NX_PATH.'lib/project.php');

header( 'Content-type: text/html; Charset=UTF-8' );
$session = new Session('nemex', NX_PATH, CONFIG::USER, CONFIG::PASSWORD);

// Attempting to login?
if( !empty($_POST['username']) && !empty($_POST['password']) ) {
	if( $session->login($_POST['username'], $_POST['password']) ) {
		header('location: ./');
		exit();
	}
}

// Not authed for this nemex? Show login form
if( !$session->isAuthed() ) {
	include( NX_PATH.'media/templates/login.html.php');
}

// Show project or project list
else {
	if( !empty($_GET) ) {
		$projectName = key($_GET);
		$project = Project::open($projectName);
		if( $project ) {
			$nodes = $project->getNodes();
			include( NX_PATH.'media/templates/project.html.php');
		}
		else {
			header( "HTTP/1.1 404 Not Found" );
			echo 'No Such Project: '.htmlspecialchars($projectName);
		}
	}
	else {
		$projects = Project::getProjectList();
		include( NX_PATH.'media/templates/project-list.html.php');
	}
}
