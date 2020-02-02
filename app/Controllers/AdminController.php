<?php

namespace OVS\Controllers;

use OVS\Core\Request;

class AdminController extends AbstractController {
	public function add_vehicle() {
		return $this->render("admin/vehicle.add.twig");
	}

	/**
	 * Admin dashboard
	 *
	 * @return void
	 */
	public function panel() {
		return $this->render("admin/dashboard.twig");
	}

	/**
	 * Admin settings
	 *
	 * @param string $option
	 * @return void
	 */
	public function settings($option = "") {
		$request = new Request();
		$path = $request->get_path();
		$template = "admin/settings";

		switch( $option ) {
			case "menu":
				$template .= ".menu";
			break;
		}

		return $this->render("$template.twig");
	}
}