<?php
class Controller
{	
	protected $app;

	public function __construct($app)
	{
		$this->app = $app;
	}
	
	protected function showView($view, $params)
	{
		$viewPath = 'views/' . $view . '.php';
		if (file_exists($viewPath)) {
			extract($params);
			include($viewPath);
		}
	}
}