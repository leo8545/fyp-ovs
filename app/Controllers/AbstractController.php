<?php

namespace OVS\Controllers;

use OVS\Core\Request;
use OVS\Utils\DependencyInjector;

abstract class AbstractController {

	protected $request;
	protected $di;
	protected $db;
	protected $view;
	public $obj;

	public function __construct(DependencyInjector $di, Request $request) {
		$this->di = $di;
		$this->db = $this->di->get("PDO");
		$this->view = $this->di->get('Twig_Environment');
		$this->request = $request;
	}

	public function render(string $template, ?array $props = []) {
		// echo isset($props) ? $this->view->render($template, $props) : $this->view->render($template);
		echo $this->view->render($template, $props);
	}

}