<?php

namespace OVS\Controllers;

use OVS\Models\VehicleModel;

class DealerController extends UserController
{
	public function index(int $id)
	{
		$dealer = $this->get($id);
		$vehicle_model = new VehicleModel($this->db);
		$_vehicles = $vehicle_model->get_vehicle_by("meta.vehicle_dealer", "dealer-".$id, false);
		$vehicles = [];
		if(is_array($_vehicles) && count($_vehicles) > 0) {
			foreach($_vehicles as $vehicle) {
				$vehicles[] = $vehicle_model->get_vehicle_by("id", (int)$vehicle['vehicle_id']);
			}
		}
		return $this->render("dealer/index.twig", [
			"dealer" => $dealer,
			"vehicles" => $vehicles
		]);
	}
}