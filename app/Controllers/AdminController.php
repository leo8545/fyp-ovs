<?php

namespace OVS\Controllers;

class AdminController extends AbstractController {
	public function add_vehicle() {
		return $this->render("admin/vehicle.add.twig");
	}
}