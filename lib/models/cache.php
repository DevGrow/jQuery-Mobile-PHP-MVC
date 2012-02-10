<?php

/**
 * Cache Model
 *
 * This class contains all of the functions used for caching the templates to static files,
 * in a bid to minimize DB requests and dynamic calls as much as possible.
 *
 * NOTE: This probably isn't super effective for some types of sites, however it seems to
 * work decently for simple applications like this. It may be worth changing to something
 * more robust later on.
 *
 * @package    jQuery Mobile PHP MVC Micro Framework
 * @author     Monji Dolon <md@devgrow.com>
 * @copyright  2011-2012 Monji Dolon
 * @license    http://www.gnu.org/licenses/gpl.html  GNU General Public License (GPL) v3
 * @link       http://devgrow.com/jquery-mobile-php-mvc-framework/
 */

class CacheModel {

    var $cacheDir = "./cache";
    var $cacheTime = 21600; // 6 hours = 6*60*60
    var $caching = false;
    var $cacheFile;
    var $cacheFileName;

    /**
     * Create an md5 hash of the currently requested URL, set the filename based on the hash.
     */
    function __construct(){
        // Hash the requested URI.
        $this->cacheFile = md5($_SERVER['REQUEST_URI']);

        // Set the filename using the hash.
        $this->cacheFileName = $this->cacheDir.'/'.$this->cacheFile.'.cache';

        // If the cache directory doesn't exist, create it and set correct permissions.
        if(!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755);
        }
    }
    
    /**
     * Starts the cache object; must call this function at the beginning of the content/page
     * you are trying to cache, then call the end function at the (duh) end of it.
     */
    function start(){
        global $do_not_cache;

        // Get the current URI and identify the current request.
        $location = explode('/',$_SERVER['REQUEST_URI']);

        // If this page isn't in the "Do not cache" list, and caching is enabled, either
        // start the cache process if the previous cache is older than cacheTime or doesn't exist,
        // or else just render the existing cache file.
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
    
    /**
     * Starts the cache object; must call this function at the beginning of the content/page
     * you are trying to cache, then call the end function at the (duh) end of it.
     */
    function end(){
        if($this->caching){
            file_put_contents($this->cacheFileName,ob_get_contents());
            ob_end_flush();
        }
    }
    
    /**
     * This function deletes the cache file for the current URI.
     */
    function purge(){
        if(file_exists($this->cacheFile) && is_writable($this->cacheDir)) unlink($this->cacheFile);
    }
    
    /**
     * This function deletes all of the cache files in the cache directory.
     */
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
?>