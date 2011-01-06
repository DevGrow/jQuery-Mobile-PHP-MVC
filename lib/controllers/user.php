<?php

class User {

	function index(){
		global $template;
		login_required();
		$template->set_title('My Profile');
		$template->render("user","profile",true);
	}

	function login(){
		global $user, $template;
		if($user->is_logged)
			return_to('home');
		else{
			if($_POST['task'] == 'login'){
				$user->login($_POST['email'],$_POST['password']);
				$template->set_msg($user->msg, $user->ok);
				if($user->ok && $_POST['return_to']){
					return_to($_POST['return_to']);
				}elseif($user->ok)
					return_to('home');
			}
		}
		$template->set_title('Login');
		$template->render("user","login",true);
	}

	function logout(){
		global $user, $template;
		if($user->is_logged()){
			$user->logout();
			$template->set_msg($user->msg, $user->ok);
		}
		$template->set_title('Login');
		$template->render("user","login",true);
	}

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

	function register(){
		global $user, $template;
		if($_POST){
			if($user->create($_POST)){
				$template->set_msg($user->msg, $user->ok);
				if($_POST['return_to'])
					return_to($_POST['return_to']);
				else
					return_to("home");
			}
			$template->set_msg($user->msg, $user->ok);
			$template->assign('email',$_POST['email']);
			$template->assign('name',$_POST['name']);
		}
		$template->set_title('Register');
		$template->render("user","register",true);
	}

}