<?php

/**
 * Router Controller
 *
 * This controller and helper class route the user to wherever they need to go based on
 * the URL.  This was taken from Dan Sosedoff's blog, the source can be found here:
 * http://blog.sosedoff.com/2009/09/20/rails-like-php-url-router/
 *
 * @package    jQuery Mobile PHP MVC Micro Framework
 * @author     Monji Dolon <md@devgrow.com>
 * @copyright  2011-2012 Monji Dolon
 * @license    http://www.gnu.org/licenses/gpl.html  GNU General Public License (GPL) v3
 * @link       http://devgrow.com/jquery-mobile-php-mvc-framework/
 */

class Router {
    public $request_uri;
    public $routes;
    public $controller, $controller_name;
    public $action, $id;
    public $params;
    public $route_found = false;
 
    public function __construct() {
        $request = $_SERVER['REQUEST_URI'];
        $pos = strpos($request, '?');
        if ($pos) $request = substr($request, 0, $pos);
 
        $this->request_uri = $request;
        $this->routes = array();
    }
 
    public function map($rule, $target=array(), $conditions=array()) {
        $this->routes[$rule] = new Route($rule, $this->request_uri, $target, $conditions);
    }
 
    public function default_routes() {
        $this->map('/:controller');
        $this->map('/:controller/:action');
        $this->map('/:controller/:action/:id');
    }
 
    private function set_route($route) {
        $this->route_found = true;
        $params = $route->params;
        $this->controller = $params['controller']; unset($params['controller']);
        $this->action = $params['action']; unset($params['action']);
        $this->id = $params['id']; 
        $this->params = array_merge($params, $_GET);
 
        if (empty($this->controller)) $this->controller = ROUTER_DEFAULT_CONTROLLER;
        if (empty($this->action)) $this->action = ROUTER_DEFAULT_ACTION;
        if (empty($this->id)) $this->id = null;
 
        $w = explode('_', $this->controller);
        foreach($w as $k => $v) $w[$k] = ucfirst($v);
        $this->controller_name = implode('', $w);
    }
 
    public function execute() {
        foreach($this->routes as $route) {
            if ($route->is_matched) {
                $this->set_route($route);
                break;
            }
        }
    }
}
 
class Route {
    public $is_matched = false;
    public $params;
    public $url;
    private $conditions;
 
    function __construct($url, $request_uri, $target, $conditions) {
        $this->url = $url;
        $this->params = array();
        $this->conditions = $conditions;
        $p_names = array(); $p_values = array();
 
        preg_match_all('@:([\w]+)@', $url, $p_names, PREG_PATTERN_ORDER);
        $p_names = $p_names[0];
 
        $url_regex = preg_replace_callback('@:[\w]+@', array($this, 'regex_url'), $url);
        $url_regex .= '/?';
 
        if (preg_match('@^' . $url_regex . '$@', $request_uri, $p_values)) {
            array_shift($p_values);
            foreach($p_names as $index => $value) $this->params[substr($value,1)] = urldecode($p_values[$index]);
            foreach($target as $key => $value) $this->params[$key] = $value;
            $this->is_matched = true;
        }
 
        unset($p_names); unset($p_values);
    }
 
    function regex_url($matches) {
        $key = str_replace(':', '', $matches[0]);
        if (array_key_exists($key, $this->conditions)) {
            return '('.$this->conditions[$key].')';
        } 
        else {
            return '([a-zA-Z0-9_\+\-%]+)';
        }
    }
}

?>