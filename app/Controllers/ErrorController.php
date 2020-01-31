<?php

namespace OVS\Controllers;

class ErrorController extends AbstractController {
	public function not_found() {
		$properties = ["msg" => "Page Not Found"];
		return $this->render("error.twig", $properties);
	}
}