<?php

class Home {

	public static function index(){
		global $template;
		$template->render("home");
	}
	
	public static function error(){
		global $template;
		$template->set_title('Error');
		$template->render("error");
	}
	
}