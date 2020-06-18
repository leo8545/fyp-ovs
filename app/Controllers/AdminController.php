<?php

namespace OVS\Controllers;

use Exception;
use OVS\Core\Request;
use OVS\Core\Router;
use OVS\Domain\Admin\Option;
use OVS\Domain\User;
use OVS\Models\AdminModel;
use OVS\Models\OrdersModel;
use OVS\Models\UserModel;
use OVS\Models\VehicleModel;
use OVS\Utils\Session;
use OVS\Utils\Validate;

class AdminController extends AbstractController {
	public function add_vehicle() {
		return $this->render("admin/vehicle.add.twig");
	}

	/**
	 * Admin dashboard
	 *
	 * @return void
	 */
	public function dashboard() {
		return $this->render("admin/dashboard.twig");
	}

	/**
	 * Admin settings
	 *
	 * @param string $option
	 * @return void
	 */
	public function settings($setting_page = "") {
		$template = "admin/settings";
		$post_obj = isset($_POST) ? $_POST : "";
		$admin_model = new AdminModel($this->db);
		$options = [];
		if($post_obj) {
			$options = $post_obj["options"];
			foreach( $options as $option_name => $option_value ) {
				$opt = new Option( $option_name, $option_value );
				$admin_model->set_option($opt);
			}
		}

		switch( $setting_page ) {
			case "menu":
				$template .= ".menu";
			break;
		}

		if( empty($options) ) {
			$options = $admin_model->get_all_options_alt();
		}

		$props = ["post_obj" => $post_obj, "options" => $options];
		return $this->render("$template.twig", $props);
	}

	public function appearance() {
		return $this->render("admin/settings.menu.twig");
	}

	public function manage_orders()
	{
		$order_model = new OrdersModel($this->db);
		$orders = $order_model->get();
		foreach($orders as $index => $order) {
			$vehicle_model = new VehicleModel($this->db);
			$orders[$index]['vehicle'] = $vehicle_model->get_vehicle_by('id', $order['vehicle_id']);
		}
		return $this->render("admin/orders.twig", ["orders" => $orders]);
	}

	public function edit_order(int $order_id)
	{
		$order_model = new OrdersModel($this->db);
		$order = $order_model->get("id", $order_id)[0];
		$vehicle_model = new VehicleModel($this->db);
		$order["vehicle"] = $vehicle_model->get_vehicle_by('id', $order['vehicle_id']);
		$order['payment'] = unserialize($order['payment']);
		unset($order['payment']['orderIds']);
		$isUpdated = false;
		if(@$_POST) {
			$order_status = $_POST['order_status'];
			$isUpdated = $order_model->update($order_id, [
				"status" => $order_status
			]);
		}
		return $this->render("admin/orders/edit.twig", ['order' => $order, "isUpdated" => $isUpdated]);
	}

	public function delete_order(int $order_id)
	{
		$order_model = new OrdersModel($this->db);
		$isDeleted = $order_model->delete($order_id);
		Router::redirect("admin/orders");
	}

}