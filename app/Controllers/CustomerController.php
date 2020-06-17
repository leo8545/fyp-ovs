<?php

namespace OVS\Controllers;

use OVS\Core\Router;
use OVS\Domain\Vehicle;
use OVS\Models\OrdersModel;
use OVS\Models\UserModel;
use OVS\Models\VehicleModel;
use OVS\Utils\Session;

class CustomerController extends UserController {
	public function register() {
		$errors = [];
		$post = isset($_POST) ? $_POST : "";
		if( $post ) {
			$post["role"] = "customer";
			$errors = $this->add($post);
		}
		$props = ["user" => $post, "errors" => $errors];
		return $this->render("register.user.twig", $props);
	}

	/**
	 * Redirects the customers to /customer/dashboard
	 *
	 * @return view customer.dashboard
	 */
	public function dashboard() {
		if( Session::get("logged_in_as") !== "customer" ) {
			return Router::redirect("login");
		}
		$order_model = new OrdersModel($this->db);
		$username = Session::get("logged_in_username");
		if( @$_POST ) {
			$order_id = (int) $_POST["order_id"];
			$order_model->delete($order_id);
		}
		// $orders = $order_model->get_order_by("user", $username);
		$orders = $order_model->get("booked_by", $username);
		$res = [];
		$vehicle_model = new VehicleModel($this->db);
		foreach( $orders as $order) {
			$res[$order["id"]] = $order;
			$res[$order["id"]]["vehicle"] = $vehicle_model->get_vehicle_by("id", $order["vehicle_id"]);
			$res[$order["id"]]["vehicle"]["meta"] = $vehicle_model->get_vehicle_meta($order["vehicle_id"]);
		}
		$props = ["orders" => $res];
		return $this->render("customer/dashboard.twig", $props);
	}
}