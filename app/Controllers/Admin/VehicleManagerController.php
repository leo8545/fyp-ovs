<?php

namespace OVS\Controllers\Admin;

use OVS\Controllers\AbstractController;
use OVS\Controllers\UserController;
use OVS\Controllers\VehicleController;
use OVS\Core\Router;
use OVS\Models\UserModel;

class VehicleManagerController extends AbstractController {
	public function get_vehicles() {
		$post = isset($_POST) ? $_POST : "";
		$model = "all";
		$manufacturer = "all";
		$vehicle_controller = new VehicleController($this->di, $this->request);
		// Filters
		if( $post ) {

			// 1 0
			if( @$post["filter_model"] && $post["filter_manufacturer"] === "all" ) {
				$model = $post["filter_model"];

				if( $model !== "all" ) {
					$result = $vehicle_controller->filter_vehicle([
						"vehicle_model" => $model,
					]);
				} else {
					$result = $vehicle_controller->get_vehicles();
				}

			}

			// 0 1
			if( $post["filter_model"] === "all" && @$post["filter_manufacturer"] ) {
				$manufacturer = $post["filter_manufacturer"];

				if( $manufacturer !== "all" ) {
					$result = $vehicle_controller->filter_vehicle([
						"vehicle_manufacturer" => $manufacturer
					]);
				} else {
					$result = $vehicle_controller->get_vehicles();
				}

			}

			// 1 1
			if( @$post["filter_model"] && $post["filter_model"] !== "all" &&
				@$post["filter_manufacturer"] && $post["filter_manufacturer"] !== "all"
			) {
				$manufacturer = $post["filter_manufacturer"];
				$model = $post["filter_model"];

				$result = $vehicle_controller->filter_vehicle([
					"vehicle_model" => $model,
					"vehicle_manufacturer" => $manufacturer
				]);
			}

		} else {
			// 0 : 0
			$result = $vehicle_controller->get_vehicles();
		}
		$models = $vehicle_controller->get_vehicle_models()["data"];
		$manufacturers = $vehicle_controller->get_vehicle_manufacturers()["data"];
		$data = $result["data"];
		$pages = $result["pages"];
		$errors = $result["errors"];
		$props = [
			"vehicles" => $data,
			"pages" => $pages,
			"errors" => $errors, 
			"filters" => $post,
			"models" => $models, 
			"manufacturers" => $manufacturers
		];
		return $this->render("admin/vehicles/vehicles.twig", $props);
	}

	public function add_vehicle() {
		$errors = [];
		$post = isset($_POST) ? $_POST : "";
		if( $post ) {
			$vehicle_controller = new VehicleController($this->di, $this->request);
			$errors = $vehicle_controller->add($post);
		}
		$props = ["vehicle" => $post, "errors" => $errors];
		return $this->render("admin/vehicles/vehicle.add.twig", $props);
	}

	public function edit_vehicle( int $vehicle_id ) {
		$vehicle_controller = new VehicleController($this->di, $this->request);
		$result = $vehicle_controller->edit($vehicle_id);
		$user_model = new UserModel($this->db);
		$_dealers = $user_model->get("role", "dealer");
		$dealers = ["" => "Select one..."];
		foreach($_dealers as $dealer) {
			$dealers["dealer-".$dealer['id']] = $dealer['username'];
		}
		if( isset($result["errors"]["exception"]["NotFoundException"]) ) {
			Router::redirect("admin/vehicles");
		}
		$props = [
			"vehicle" => $result["data"],
			"vehicle_id" => $vehicle_id,
			"meta" => $result["vehicle_meta"],
			"dealers" => $dealers,
			"errors" => $result["errors"]
		];
		return $this->render("admin/vehicles/vehicle.edit.twig", $props);
	}

	public function get_vehicle_models() {
		$errors = [];
		$data = [];
		$vehicle_controller = new VehicleController($this->di, $this->request);
		$result = $vehicle_controller->get_vehicle_models();
		$data = $result["data"];
		$errors = $result["errors"];
		$props = ["models" => $data, "errors" => $errors];
		return $this->render("admin/vehicles/vehicle.model.twig", $props);
	}

	public function delete_vehicle( int $vehicle_id ) {
		$base = new VehicleController($this->di, $this->request);
		$errors = $base->delete($vehicle_id);
		Router::redirect("admin/vehicles/all");
	}
}
