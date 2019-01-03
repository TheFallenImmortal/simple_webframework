<?php
class TestController extends Controller
{
	public function index($param) {
		$this->showView('test', ['param' => $param]);
	}
}