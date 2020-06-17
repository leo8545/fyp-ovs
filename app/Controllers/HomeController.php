<?php

namespace OVS\Controllers;

use OVS\Models\VehicleModel;

class HomeController extends AbstractController {

	public function index() {
		$vehicle_model = new VehicleModel($this->db);
		$vehicles = $vehicle_model->get_vehicles();
		$props = [ "vehicles" => $vehicles ];
		return $this->render("home.twig", $props);
	}

	public function __call($name, $args){
		return $this->render( "pages/$name.twig" );
	}

}