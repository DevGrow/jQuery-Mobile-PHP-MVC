<?php

/**
 * Helper Functions
 *
 * This file contains a lot of miscellaneous functions that are used throughout the app.
 *
 * @package    jQuery Mobile PHP MVC Micro Framework
 * @author     Monji Dolon <md@devgrow.com>
 * @copyright  2011-2012 Monji Dolon
 * @license    http://www.gnu.org/licenses/gpl.html  GNU General Public License (GPL) v3
 * @link       http://devgrow.com/jquery-mobile-php-mvc-framework/
 */

/**
 * Load the controller file automatically if it is referenced in a function.
 *
 * @param   $name	The name of the controller.
 */
function __autoload($name){
	require_once LIB_DIR.'/controllers/'.strtolower($name).'.php';
}

/**
 * Redirect the user to any page on the site.
 *
 * @param   $location	URL of where you want to return the user to.
 */
function return_to($location){
	$location = '/'.$location;
	header("Location: $location");
	exit();
}

/**
 * Check to see if user is logged in and if not, redirect them to the login page.
 * If they're logged in, let them proceed.
 */
function login_required(){
	global $user, $template;
	if(!$user->is_logged){
		$template->set_msg("You must be logged in to access this section.",false);
		User::login();
		exit();
	}
}

/**
 * This function prints variables in the template, after setting them with the
 * $template->set_variable() function. Possible to extend this to support multiple
 * languages, as well as optionally returning a value (instead of echoing).
 *
 * @param   $id	The name of the variable, prints the value passed from controller.
 */
function __($id) {
	global $template;

	echo $template->variables[$id];
}

?>