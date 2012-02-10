<?php

/**
 * User Controller
 *
 * This controller contains all the actions the user may perform that deals with their
 * account.
 *
 * @package    jQuery Mobile PHP MVC Micro Framework
 * @author     Monji Dolon <md@devgrow.com>
 * @copyright  2011-2012 Monji Dolon
 * @license    http://www.gnu.org/licenses/gpl.html  GNU General Public License (GPL) v3
 * @link       http://devgrow.com/jquery-mobile-php-mvc-framework/
 */

class User {

    /**
     * Default user profile page.
     */
	function index(){
		global $template, $user;
		login_required();

		$template->assign('user_name', $user->name);
		$template->assign('user_email', $user->email);
		$template->set_title('My Profile');
		$template->render("user","profile",true);
	}

    /**
     * Login page.
     *
     * Sends the user to the homepage if they're already logged in. If they try to
     * login, validates their info and redirects them to homepage.
     */
	function login(){
		global $user, $template;
		if($user->is_logged)
			return_to('site/home');
		else{
			if($_POST['task'] == 'login'){
				$user->login($_POST['email'],$_POST['password']);
				$template->set_msg($user->msg, $user->ok);
				if($user->ok && $_POST['return_to']){
					return_to($_POST['return_to']);
				}elseif($user->ok)
					return_to('site/home');
			}
		}
		$template->set_title('Login');
		$template->render("user","login",true);
	}

    /**
     * Logout page.
     *
     * Simply logs the user out if they're logged in, then renders the login page.
     */
	function logout(){
		global $user, $template;
		if($user->is_logged()){
			$user->logout();
			$template->set_msg($user->msg, $user->ok);
		}
		return_to('user/login');
	}

    /**
     * Edit profile page.
     */
	function edit(){
		global $user, $template;
		login_required();
		if($_POST){
			if($user->update($_POST)) $template->set_msg($user->msg, $user->ok);
			$template->set_msg($user->msg, $user->ok);
		}
		$template->assign('user_email',$user->email);
		$template->assign('user_name',$user->name);
		$template->set_title('Update Information');
		$template->render("user","edit",true);
	}

    /**
     * User registration page.
     */
	function register(){
		global $user, $template;
		if($_POST){
			if($user->create($_POST)){
				$template->set_msg($user->msg, $user->ok);
				if($_POST['return_to'])
					return_to($_POST['return_to']);
				else
					return_to("site/home");
			}
			$template->set_msg($user->msg, $user->ok);
			$template->assign('email',$_POST['email']);
			$template->assign('name',$_POST['name']);
		}
		$template->set_title('Register');
		$template->render("user","register",true);
	}

}