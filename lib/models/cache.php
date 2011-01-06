<?php

/* For more information on this cache class, check out this post: http://devgrow.com/simple-cache-class/ */

class CacheModel {

	var $cacheDir = "./cache";
	var $cacheTime = 21600; // 6 hours = 6*60*60
	var $caching = false;
	var $cacheFile;
	var $cacheFileName;

	function __construct(){
		$this->cacheFile = md5($_SERVER['REQUEST_URI']);
		$this->cacheFileName = $this->cacheDir.'/'.$this->cacheFile.'.cache';
		if(!is_dir($this->cacheDir)) mkdir($this->cacheDir, 0755);
	}
	
	function start(){
		global $do_not_cache;
		$uri = str_replace(BASE_URL,"","http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
		$location = explode('/',$uri);
		if(!in_array($location[0],$do_not_cache) && CACHE_ENABLE){
			if(file_exists($this->cacheFileName) && (time() - filemtime($this->cacheFileName)) < $this->cacheTime){
				$this->caching = false;
				echo file_get_contents($this->cacheFileName);
				exit();
			}else{
				$this->caching = true;
				ob_start();
			}
		}
	}
	
	function end(){
		if($this->caching){
			file_put_contents($this->cacheFileName,ob_get_contents());
			ob_end_flush();
		}
	}
	
	function purge(){
		if(file_exists($this->cacheFile) && is_writable($this->cacheDir)) unlink($this->cacheFile);
	}
	
	function purge_all(){
		if(!$dirhandle = @opendir($this->cacheDir)) return;
		while(false != ($filename = readdir($dirhandle))){
			if(substr($filename,-4) == '.cache') {
				$filename = $this->cacheDir. "/". $filename;
				unlink($filename);
			}
		}
	}

}