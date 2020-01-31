<?php

namespace OVS\Controllers;

class HomeController extends AbstractController {

	public function get_home() {
		echo $this->render("home.twig");
	}

}