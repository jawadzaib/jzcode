<?php
namespace Core;

use Core\Router;
use Core\LoadModules;

class Router {
	private static $instance;
	public static function getInstance() {
		if (empty(self::$instance)) {
            self::$instance = new Router();
            self::$instance->routes = [];
        }
        return self::$instance;
	}
	public static function route($path, $controller_name, $action_name, $module_name = null, $layout = null) {
		$instance = self::getInstance();
		$key = str_replace('/', '_', $path);
		$instance->routes[$key] = array(
			'path' => $path,
			'controller' => $controller_name,
			'action' => $action_name,
			'module' => $module_name,
			'layout' => $layout
		);
	}
	public static function action($controller, $action, $module_name = null) {
		$instance = self::getInstance();
		$url = PROOT;
		if($instance->routes) {
			foreach ($instance->routes as $route) {
				if($module_name && $module_name == $route['module'] && $controller == $route['controller'] && $action == $route['action']) {
					$url .= $route['path'];
					break;
				}
			}
		}
		return $url;
	}
	private static function getModuleRoutes($module_name) {
		$instance = self::getInstance();
		$routes = [];
		if($instance->routes) {
			foreach ($instance->routes as $route) {
				if(isset($route['module']) && $route['module'] == $module_name) {
					$routes[] = $route;
				}
			}
		}
		return $routes;
	}
	private static function fetchRoute($url) {
		if(file_exists(ROOT.DS.'config'.DS.'routes.php')) {
			require_once (ROOT.DS.'config'.DS.'routes.php');
		}
		$instance = self::getInstance();
		$routes = $instance->routes;
		
		$url_string = trim(implode('_', $url), '_');

		$route = null;
		if(isset($routes[$url_string])) {
			$route = $routes[$url_string];
			$route['params'] = [];
		} else {
			if(!empty($routes)) {
				$url = explode('_', $url_string);
				foreach ($routes as $key => $c_route) {
					$path = explode('_', trim($key, '_'));
					$matched = true;
					$route_params = [];
					foreach ($path as $index => $value) {
						if(!isset($url[$index])) {
							$matched = false;
							break;
						}
						if($url[$index] !== $value) {
							if(substr($value, 0, 1) === ':') {
								// this is URL parameter
								if($url[$index] != '') {
									$route_params[substr($value, 1, strlen($value))] = $url[$index];
								}
							} else {
								$matched = false;
								break;
							}
						}

					}
					if($matched && $index == count($url)-1) {
						// route matched
						$route = $c_route;
						$route['params'] = $route_params;
						break;
					}
				}
			}
		}
		return $route;
	}
	public static function load($url) {
		$route = self::fetchRoute($url);
		$module = null;
		// controller
		$controller = (isset($url[0]) && $url[0] != '') ? ucwords($url[0]) : ucwords(DEFAULT_CONTROLLER);
		
		$controller_name = $controller;
		$controller .= 'Controller';
		$controller = 'App\Controllers\\'.$controller;
		array_shift($url);

		$indexParams = $url;
		// action 
		$action = (isset($url[0]) && $url[0] != '') ? $url[0].'Action' : 'indexAction';
		array_shift($url);
		$queryParams = $url;
		$dispatch = null;
		

		if($route != null) {
			if(isset($route['controller']) && $route['controller'] != '') {
				$controller = $controller_name = trim($route['controller']);
				$controller .= 'Controller';
				
			}
			if(isset($route['action']) && $route['action'] != '') {
				$action = trim($route['action']);
			} else {
				$action = 'index';
			}
			$action .= 'Action';
			if(isset($route['params'])) {
				$queryParams = $route['params'];
			}
			if(isset($route['module']) && $route['module'] != '') {
				$controller_path = LoadModules::path($route['module']).'controllers'.DS.$controller.'.php';
				if(file_exists($controller_path)) {
					$module = LoadModules::getModule($route['module']);
					$controller = $module->namespace.DS.'Controllers'.DS.$controller;
				}
			} else {
				$controller = 'App\Controllers\\'.$controller;
			}
		}

		if(class_exists($controller)) {
			if(isset($route['module'])) {
				$module = LoadModules::getModule($route['module']);
				$dispatch = new $controller($controller_name, $action, $module);
				if($module) {
					$dispatch->view->module = $module;
				}
				if(isset($route['layout']) && $route['layout'] != '') {
					$dispatch->view->setLayout($route['layout']);
				}
			} else {
				$dispatch = new $controller($controller_name, $action);
			}
			if(method_exists($dispatch, 'isController') && $dispatch->isController()) {
				if(method_exists($controller, $action)) {
					call_user_func_array([$dispatch, $action], $queryParams);
				} else {
					die( 'That method does not exist in the controller \"'.$controller_name.'\"');
				}
			} 
		} else if (strtolower($controller_name) == '_layouts') {
			$controller = 'Core\Layouts';
			$dispatch = new $controller();	
			if(method_exists($controller, 'index')) {
				call_user_func_array([$dispatch, 'index'], $indexParams);
			}
		} else if (strtolower($controller_name) == '_errors') {
			$controller = 'Core\Errors';
			$dispatch = new $controller();	
			if(method_exists($controller, 'index')) {
				call_user_func_array([$dispatch, 'index'], $indexParams);
			}
		} else {
			die('`'.$controller.'` class does not exists or you might didn\'t specify the route for your module!');
		}
	}

	public static function redirect($location = '') {
		if(substr($location, 0, strlen(PROOT)) === PROOT) {
			$location = substr($location, strlen(PROOT), strlen($location));
		}
		if(!headers_sent()) {
			header('Location: '.PROOT.$location);
			exit();
		} else {
			echo '<script type="text/javascript">';
			echo 'window.location.href="'.PROOT.$location.'";';
			echo '</script>';
			echo '<noscript>';
			echo '<meta http-equiv="refresh" content="0;url='.$location.'" />';
			echo '</noscript>';
			exit();
		}
	}

	public static function url($location = '') {
		return PROOT.$location;
	}


}