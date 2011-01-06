<?php

class Page {

	public static function load_page($name){
		global $template;
		$standard = array("faq", "terms", "about");
		$proper = array("Frequently Asked Questions", "Terms of Service", "About Us");
		$template->set_title(ucwords(str_replace($standard, $proper, $name)));
		$template->render("pages", $name, true);
	}
	
}
