<?php

function __autoload($name){
	require_once LIB_DIR.'/controllers/'.strtolower($name).'.php';
}

function return_to($location){
	$location = BASE_URL.'#'.$location;
	header("Location: $location");
	exit();
}

function login_required(){
	global $user, $template;
	if(!$user->is_logged){
		$template->set_msg("You must be logged in to access this section.",false);
		User::login();
		exit();
	}
}