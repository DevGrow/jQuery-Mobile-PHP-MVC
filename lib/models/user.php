<?php

class UserModel {

	var $user_id;
	var $name;
	var $email;
	var $password;
	var $ok;
	var $msg;
	var $is_logged;

	function __construct(){
		global $db;
		
		$this->user_id = 0;
		$this->email = "Guest";
		$this->name = "Guest";
		$this->ok = false;
		
		if(!$this->check_session()) $this->check_cookie();
		
		return $this->ok;
	}
	
	function check_session(){
		if(!empty($_SESSION['auth_email']) && !empty($_SESSION['auth_secret']))
			return $this->check($_SESSION['auth_email'], $_SESSION['auth_secret']);
		else
			return false;
	}

	function check_cookie(){
		if(!empty($_COOKIE['auth_email']) && !empty($_COOKIE['auth_secret']))
			return $this->check($_COOKIE['auth_email'], $_COOKIE['auth_secret']);
		else
			return false;
	}
	
	function create($info,$login = true){
		global $db;
		$name = mysql_real_escape_string($info['name']);
		$email = mysql_real_escape_string($info['email']);
		$password = md5(mysql_real_escape_string($info['password']) . PASSWORD_SALT);
		$status = $info['status'] ? mysql_real_escape_string($info['status']) : 1;
		$created_ip = $_SERVER['REMOTE_ADDR'];
		$this->ok = false;
		
		if(!$info['name'] || !$info['email'] || !$info['password'] || !$info['password2']){
			$this->msg = "Error! All fields are required.";
			return false;
		}elseif($info['password'] != $info['password2']){
			$this->msg = "Error! Passwords do not match.";
			return false;
		}elseif(!$this->validEmail($info['email'])){
			$this->msg = "Error! Please enter a valid e-mail address.";
			return false;
		}
		
		$db->query("SELECT user_id, password FROM users WHERE email = '".mysql_real_escape_string($email)."'");
		if(mysql_num_rows($db->result) == 1){
			$this->msg = "Error! E-mail address is already in use.";
		}else{
			$query = $db->query("INSERT INTO users (name,email,password,status,created_ip) VALUES ('$name','$email','$password','$status','$created_ip')");
			if($query){
				$this->msg = "User successfully added.";
				$this->ok = true;
				if($login) $this->login($info['email'],$info['password']);
				return true;
			}else{
				$this->msg = "There was a problem, please try again.";
			}
		}
		return false;
	}
	
	function update($info){
		global $db;

		$this->ok = false;
		
		$name = mysql_real_escape_string($info['name']);
		$email = mysql_real_escape_string($info['email']);
		$password = md5(mysql_real_escape_string($info['password']) . PASSWORD_SALT);
		
		if($info['password'] != $info['password2']){
			$this->msg = "Error! Passwords do not match.";
			return false;
		}elseif(!$this->validEmail($info['email'])){
			$this->msg = "Error! Please enter a valid e-mail address.";
			return false;
		}
		$sql = "name='$name', email='$email'";
		if($info['password']){
			$sql .= ", password='$password'";
		}
		$query = "UPDATE users SET ".$sql." WHERE user_id = '".$this->user_id."'";
		$query = $db->query($query);
		if($query){
			$this->msg = "Info successfully updated.";
			$this->ok = true;
			$_SESSION['auth_email'] = $email;
			if($info['password']) $_SESSION['auth_secret'] = $password;
			setcookie("auth_email", $email, time()+60*60*24*30, "/", COOKIE_DOMAIN);
			if($info['password']) setcookie("auth_secret", $password, time()+60*60*24*30, "/", COOKIE_DOMAIN);
			$this->name = $name;
			$this->email = $email;
			return true;
		}else{
			$this->msg = "There was a problem, please try again.";
		}
		return false;
	}

	function login($email, $password){
		global $db;
		$sql = $db->query("SELECT user_id, password, name FROM users WHERE email = '".mysql_real_escape_string($email)."'");
		$this->ok = false;
		if(!$email || !$password){
			$this->msg = "Error! Both E-mail and Password are required to login.";
		}
		$results = $db->fetch($sql);
		if($db->num($sql) == 1)
		{
			$db_password = $results['password'];
			$name = $results['name'];
			if(md5($password . PASSWORD_SALT) == $db_password)
			{
				$_SESSION['auth_email'] = $email;
				$_SESSION['auth_secret'] = md5($password . PASSWORD_SALT);
				setcookie("auth_email", $email, time()+60*60*24*30, "/", COOKIE_DOMAIN);
				setcookie("auth_secret", md5($password . PASSWORD_SALT), time()+60*60*24*30, "/", COOKIE_DOMAIN);
				$this->user_id = $results['user_id'];
				$this->name = $name;
				$this->email = $email;
				$this->ok = true;
				$this->msg = "Login Successful!";
				$this->is_logged = true;
				return true;
			}else{
				$this->msg = "Error! Password is incorrect.";
			}
		}else{
			$this->msg = "Error! User does not exist.";
		}
		return false;
	}
	
	function check($email, $secret){
		global $db;
		$sql = $db->query("SELECT user_id, password, name FROM users WHERE email = '".mysql_real_escape_string($email)."'");
		$results = $db->fetch($sql);
		if($db->num($sql) == 1)
		{
			$db_password = $results['password'];
			$name = $results['name'];
			if($db_password == $secret) {
				$this->user_id = $results['user_id'];
				$this->email = $email;
				$this->name = $name;
				$this->ok = true;
				$this->is_logged = true;
				return true;
			}
		}			
		return false;
	}

	function is_logged(){
		if($this->check($_SESSION['auth_email'], $_SESSION['auth_secret'])) return true;
		else return false;
	}

	function is_admin(){
		if($this->is_logged() && $this->get_info('admin') == 1) return true;
		else return false;
	}
	
	function get_info($field = "*", $email = null){
		global $db;
		if(!$email) $email = $this->email;
		$sql = $db->query("SELECT $field FROM users WHERE email = '$email'");
		$info = $db->fetch($sql);
		if($field == "*") return $info;
		else return $info[$field];
	}
	
	function logout(){
		$this->user_id = 0;
		$this->email = "Guest";
		$this->name = "Guest";
		$this->ok = true;
		$this->msg = "You have been logged out!";
		$this->is_logged = false;
		
		$_SESSION['auth_email'] = "";
		$_SESSION['auth_secret'] = "";
		setcookie("auth_email", "", time() - 3600, "/", COOKIE_DOMAIN);
		setcookie("auth_secret", "", time() - 3600, "/", COOKIE_DOMAIN);
	}
	
	// Courtesy LinuxJournal.com : http://www.linuxjournal.com/article/9585?page=0,3
	function validEmail($email){
		$isValid = true;
		$atIndex = strrpos($email, "@");
		if (is_bool($atIndex) && !$atIndex){
			$isValid = false;
		}
		else{
			$domain = substr($email, $atIndex+1);
			$local = substr($email, 0, $atIndex);
			$localLen = strlen($local);
			$domainLen = strlen($domain);
			if ($localLen < 1 || $localLen > 64){
				$isValid = false;
			}else if ($domainLen < 1 || $domainLen > 255){
				$isValid = false;
			}else if ($local[0] == '.' || $local[$localLen-1] == '.'){
				$isValid = false;
			}else if (preg_match('/\\.\\./', $local)){
				$isValid = false;
			}else if (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)){
				$isValid = false;
			}else if (preg_match('/\\.\\./', $domain)){
				$isValid = false;
			}else if(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))){
				if (!preg_match('/^"(\\\\"|[^"])+"$/', str_replace("\\\\","",$local))){
					$isValid = false;
				}
			}
			if ($isValid && !(checkdnsrr($domain,"MX") ||  checkdnsrr($domain,"A"))){
				$isValid = false;
			}
		}
		return $isValid;
	}
	
	function user_table($all = false, $start = 1, $limit = 20, $sort_by = "user_id DESC"){
		global $user, $db;
		$query = "SELECT * FROM users";
		if(!$all) $query .= " WHERE user_id = '".$user->user_id."'";
		$this->total = $db->num($db->query($query));
		$query .= " ORDER BY $sort_by LIMIT $start, $limit";
		if($this->total >= 1){
			$result = $db->query($query);
			$str .= '<ul class="order-list"><li class="title"><span class="c2">Name</span><span class="c3">Email</span><span class="c2">Joined On</span><span class="c1">Status</span></li>';
			while($row = $db->fetch($result)){
				$str .= '<li><span class="c2">'.$row['name'].'</span><span class="c3">'.$row['email'].'</span><span class="c2">'.date("M j Y",strtotime($row['created_on'])).'</span><span class="c1">';
				if($all) $str .= '<a href="admin/users/edit/'.$row['user_id'].'">'.$this->status($row['status']).'</a>';
				else $str .= $this->status($row['status']);
				$str .= '</span></li>';
			}
			$str .= '</ul>';
		}else
			$str = "No users found.";
		return $str;
	}
	
	function status($value){
		switch($value){
			case 0:
				return 'Inactive';
				break;
			case 1:
				return 'Active';
				break;
			case 2:
				return 'Banned';
				break;
			default:
				return 'Inactive';
				break;
		}
	}
	
	function user_info($user_id){
		global $user, $db;
		$query = "SELECT * FROM users WHERE user_id = '".$user_id."'"; 
		$result = $db->query($query);
		$info = $db->fetch($result);
		return $info;
	}
	
	function user_update($info, $user_id){
		global $db;
		$query = "UPDATE users SET status='".$info['status']."', name='".$info['name']."', email='".$info['email']."', admin='".$info['admin']."'";
		if($info['password']){
			$password = md5(mysql_real_escape_string($info['password']) . PASSWORD_SALT);
			$query .= ", password='".$password."'";
		}
		$query .=  " WHERE user_id = '".$user_id."'";
		if($db->query($query)) return true;
		else return false;
	}

}