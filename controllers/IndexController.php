<?php
class IndexController extends Controller
{
	public function _index() {
		$this->showView('index', []);
	}
}