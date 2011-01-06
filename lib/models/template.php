<?php

class TemplateModel {

	var $variables = array();
	var $title;
	var $msg;
	var $msg_type;

	function load($file){
		global $user, $template;
		include_once LIB_DIR."/views/$file.php";
	}
	
	function render($model = "", $action = "", $html = false){
		global $user, $template;
		$profile = $user->get_info();
		extract($this->variables);
		include_once(LIB_DIR."/views/header.php");
		$cache = new CacheModel;
		$cache->start();
		$file = './lib/views/';
		if($html) echo '<div class="'.$model.'">';
		if($model) $file .= $model;
		if($action) $file .= '/'.$action;
		$file .= '.php';
		if(file_exists($file)) include_once $file;
		if($html) echo '</div>';
		include_once(LIB_DIR."/views/footer.php");
		$cache->end();
	}
	
	function assign($name, $value){
		$this->variables[$name] = $value;
	}
	
	function set_title($title){
		$this->title = $title;
	}

	function page_title(){
		if($this->title) $str = $this->title.' - '.APP_NAME.'.com';
		else $str = APP_NAME.'.com - '.APP_KEYWORDS;
		echo $str;
	}

	function set_msg($the_msg, $type = null){
		$this->msg = $the_msg;
		$this->msg_type = $type;
	}

	function get_msg(){
		if(isset($this->msg_type)){
			if($this->msg_type) $style = 'success';
			else $style = 'error';
		}
		if($this->msg) echo "<div class='status message $style'>".$this->msg."</div>\n";
	}

}