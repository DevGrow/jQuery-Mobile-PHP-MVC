<?php

/**
 * Configuration file
 *
 * This file specifies all of the base values used throughout the app.
 *
 * @package    jQuery Mobile PHP MVC Micro Framework
 * @author     Monji Dolon <md@devgrow.com>
 * @copyright  2011-2012 Monji Dolon
 * @license    http://www.gnu.org/licenses/gpl.html  GNU General Public License (GPL) v3
 * @link       http://devgrow.com/jquery-mobile-php-mvc-framework/
 */

// Define global Variables.
define("APP_NAME", "jQuery Mobile MVC Framework");
define("APP_DESCRIPTION", "A simple PHP MVC framework utilizing jQuery Mobile");
define("APP_KEYWORDS", "jquery mobile, jquery mobile mvc, jquery mobile php, jquery mobile framework");
define("PASSWORD_SALT", "justjquerying");
define("CACHE_ENABLE", true);
define("BASE_DIR", dirname(dirname(__FILE__)));
define("LIB_DIR", dirname(__FILE__));
define("COOKIE_DOMAIN", "");

// Set the default controller the user is directed to (aka homepage).
define('ROUTER_DEFAULT_CONTROLLER', 'site');
define('ROUTER_DEFAULT_ACTION', 'home');

// The following controllers/actions will not be cached:
$do_not_cache = array("user","");

// Load helper functions and the model classes.
require_once(LIB_DIR."/helpers.php");
require_once(LIB_DIR."/models/cache.php");
require_once(LIB_DIR."/models/user.php");
require_once(LIB_DIR."/models/template.php");

