<?php

class DatabaseModel {

	var $error = array();
	var $count = 0;
	var $con = "";
	var $result;

	function connect($db){
		$this->con = mysql_connect($db['host'], $db['user'], $db['pass']) OR $this->error(1,"Can't connect to mySQL Server.");
		mysql_select_db($db['dbname'], $this->con) OR $this->error(1,"Can't select mySQL Database.");
	}

	function query($sql){
		$this->result = mysql_query($sql, $this->con) OR $this->error(1, mysql_error());
		$this->count++;
		return $this->result;
	}

	function fetch($query){
		$this->result = mysql_fetch_array($query);
		return $this->result;
	}

	function get_rows($sql){
		$this->result = mysql_fetch_array(mysql_query($sql));
		return $this->result;
	}

	function num($query){
		$this->result = mysql_num_rows($query);
		return $this->result;
	}
	
	function insert_id(){
		return mysql_insert_id($this->con);
	}

	function close(){
		mysql_close($this->con);
	}

	function error($error, $quote){
		if($error = 1) die("An error has occurred");
	}

}