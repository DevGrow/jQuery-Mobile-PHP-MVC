<?php

/**
 * Template Model
 *
 * This class contains all of the functions used for rendering the HTML templates from
 * the various view files.
 *
 * @package    jQuery Mobile PHP MVC Micro Framework
 * @author     Monji Dolon <md@devgrow.com>
 * @copyright  2011-2012 Monji Dolon
 * @license    http://www.gnu.org/licenses/gpl.html  GNU General Public License (GPL) v3
 * @link       http://devgrow.com/jquery-mobile-php-mvc-framework/
 */

class TemplateModel {

    var $variables = array();
    var $title;
    var $msg;
    var $msg_type;

    /**
     * Simple function used to load template views.  If only a view/model is specified,
     * load only the file from the base template directory.  If an action is specified,
     * the file/model name is expected to also be the name of the folder in which the
     * actual view file is being held.
     *
     * @param   $file       The name of the view/model file
     * @param   $action     When specified, name of the file ($file used as dir name).
     *                      This param is optional.
     */
    function load($file, $action = null){
        global $template, $user;

        // If an action is specified, include the specific action.
        $file = LIB_DIR . "/views/" . $file;
        if($action) {
            $file .= '/'.$action;   
        }
        $file .= ".php";

        // Load the view file only if it exists.
        if(file_exists($file)) { 
            include_once $file;
        }
    }
    
    /**
     * Renders default template views, based on the model and action supplied (including
     * header and footer views).
     *
     * @param   $model              The name of the model file
     * @param   $action             When specified, name of the file ($file used as dir name)
     * @param   $html               When specified, name of the file ($file used as dir name)
     * @param   $caching_enabled    (optional): When specified, name of the file ($file used as dir name)
     */
    function render($model, $action = null, $html = false, $caching_enabled = true){

        $this->load("header");

        // Start caching everything rendered.  We start this after the
        // header, since the header may contain user session information
        // that shouldn't be cached.
        if($caching_enabled) {
            $cache = new CacheModel;
            $cache->start();
        }

        // Add a container DIV to the view HTML about to be inclued.
        if($html) {
            echo '<div class="'.$model.'">';
        }

        // Load this specific view
        $this->load($model, $action);

        // Close the container DIV.
        if($html) echo '</div>';

        $this->load("footer");
        
        // Stop caching.
        if($caching_enabled) {
            $cache->end();
        }

    }

     /**
     * Used to assign variables that can be used in the template files.
     *
     * @param   $name       Name of the variable to be assigned
     * @param   $value      String or Array object
     */
    function assign($name, $value){
        $this->variables[$name] = $value;
    }
    
    /**
     * Used to assign the page title of the rendered HTML file.
     *
     * @param   $title      Title of the rendered HTML file (<title></title>)
     */
    function set_title($title){
        $this->title = $title;
    }

     /**
     * This function prints the page title that has been set in the controller,
     * should only be used in the header view.
     */
    function page_title(){
        if($this->title) $str = $this->title.' - '.APP_NAME.'.com';
        else $str = APP_NAME.'.com - '.APP_KEYWORDS;
        echo $str;
    }

     /**
     * Set any status or error messages to be passed into the view files.
     *
     * @param   $the_msg    The message to be displayed in the status box.
     * @param   $type       Type of message, either 'success' or 'error' -
     *                      passed into the DIV object as a class (used for styling).
     */
    function set_msg($the_msg, $type = null){
        $this->msg = $the_msg;
        $this->msg_type = $type;
    }

     /**
     * Displays the status or error message in the template.
     */
    function get_msg(){
        if($this->msg_type) {
            $style = "success";
        } else {
            $style = "error";
        }
        if($this->msg) {
            echo "<div class='status message " . $style . "'>".$this->msg."</div>\n";
        }
    }

}
?>