<?php

namespace OVS\Controllers;

use Exception;
use OVS\Domain\Vehicle;
use OVS\Models\VehicleModel;

class VehicleController extends AbstractController {

	public function get_vehicle( int $vehicle_id ) {
		$vehicle_model = new VehicleModel( $this->db );

		try {
			$vehicle = $vehicle_model->get( $vehicle_id );
		} catch( Exception $ex ) {
			$props = ["msg" => "Vehicle with id: \"$vehicle_id\" does not exists"];
			return $this->render("error.twig", $props);
		}
		
		$props = ["vehicle" => $vehicle];
		return $this->render("vehicle.twig", $props);
	}

	public function get_vehicles() {
		$vehicle_model = new VehicleModel($this->db);
		$vehicles = $vehicle_model->get_all();
		return $this->render("vehicle", $vehicles);
	}

	public function create(string $name, string $price, string $year) {
		$vehicle = new Vehicle();
		$vehicle->set_name($name);
		$vehicle->set_price($price);
		$vehicle->set_year($year);
		$vehicle_model = new VehicleModel($this->db);
		echo "c";

		try {
			$vehicle_model->create($vehicle);
		} catch( Exception $ex ) {
			$props = ["msg" => "Error inserting vehicle!! $ex->getMessage()"];
			return $this->render("error.twig", $props);
		}
	}
	public function add_vehicle() {
		return $this->render("vehicle.add.twig");
	}

}