<?php
include 'Utility.php';

class Application
{	
	private $running = false;
	private $modules = array();
	private $indexController = 'index';
	
	public function __construct()
	{
		$config = include 'config/config.php';
		if (isset($config['indexController'])) {
			$this->indexController = $config['indexController'];
		}
		if (isset($config['services'])) {
			foreach ($config['services'] as $moduleType => $moduleName) {
				$pos = strrpos($moduleName, '/');
				if ($pos) {
					$moduleClassName = ucfirst(substr($moduleName, $pos+1)) . 'Service';
					$modulePath = 'services/' . substr($moduleName, 0, $pos+1) . $moduleClassName . '.php';
				} else {
					$moduleClassName = ucfirst($moduleName) . 'Service';
					$modulePath = 'services/' . $moduleClassName . '.php';
				}
				if (is_int($moduleType)) {
					$moduleType = $moduleName;
				}
				$moduleConfigPath = 'config/' . $moduleType . '.php';
				if (!file_exists($modulePath)) {
					$modulePath = 'core/' . $modulePath;
				}
				
				include $modulePath;
				$moduleConfig = include $moduleConfigPath;
				$reflect = new ReflectionClass($moduleClassName);
				$module = $reflect->newInstanceArgs($moduleConfig);
				
				$pos = strrpos($moduleType, '/');
				if ($pos) {
					$moduleType = substr($moduleType, $pos+1);
				}
				$modules[$moduleTypeName] = $module;
			}
		}		
	}
	
	function __get($property) {
		if (array_key_exists ($property, $modules)) {
			return $modules[$property];
		}
	}
	
	function __isset($property) {
		return array_key_exists($property, $modules);
	}
	
	private function defaultRouteController($url)
	{
		$components = explode('/', $url);
		array_shift($components);
		
		$components_before = $components;
		$controllerPath = 'controllers';
		$controllerName = '';
		do {
			if ($controllerName) {
				$controllerPath .= '/' . $controllerName;
			}
			$controllerName = dashesToCamelCase(array_shift($components));
			
			$controllerPathPhp = $controllerPath . '/' . ucfirst($controllerName) . 'Controller.php';
			$exists = file_exists($controllerPathPhp);
		} while (!$exists && !empty($components));
		
		if (!$exists) {
			$controllerName = $this->indexController;
			$controllerPathPhp = 'controllers/' . ucfirst($this->indexController) . 'Controller.php';
			$exists = file_exists($controllerPathPhp);
		} else if ($controllerName == ucfirst($this->indexController)) {
			$exists = false;
		}
		$controllerName = ucfirst($controllerName) . 'Controller';
		
		if ($exists) {
			include 'Controller.php';
			include $controllerPathPhp;
			$controller = new $controllerName($this);
			$funcName = array_shift($components);
			if ($funcName == '_index') {
				$funcName = NULL;
			} else if ($funcName === NULL) {
				$funcName = '_index';
			} else if (!method_exists($controller, $funcName)) {
				array_unshift($components, $funcName);
				if (method_exists($controller, '_index')) {
					$funcName = '_index';
				} else {
					$funcName = NULL;
				}
			}
			if ($funcName) {
				call_user_func_array(array($controller, $funcName), $components);
			}
			
		}
	}
	
	public function run()
	{
		if ($this->running) {
			return;
		}
		$running = true;
		
		$url = explode('?', $_SERVER['REQUEST_URI'])[0];
		
		$this->defaultRouteController($url);
	}
}