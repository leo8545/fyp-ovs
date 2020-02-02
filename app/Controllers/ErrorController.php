<?php

namespace OVS\Controllers;

class ErrorController extends AbstractController {
	public function not_found() {
		$properties = ["msg" => "Page Not Found"];
		return $this->render("error.twig", $properties);
	}

	public function no_access() {
		$props = ["msg" => "You are not allowed to access this page"];
		return $this->render("error.twig", $props);
	}
}