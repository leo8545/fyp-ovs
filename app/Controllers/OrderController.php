<?php 

namespace OVS\Controllers;

use Exception;
use OVS\Domain\Order;
use OVS\Models\OrdersModel;
use OVS\Models\VehicleModel;
use OVS\Utils\Session;

class OrderController extends AbstractController
{
	public function addToCart()
	{
		$vehicle_id = (int) $_POST['vehicle_id'];
		$vehicle_model = new VehicleModel($this->db);
		$result = [];
		try {
			$vehicle = $vehicle_model->get_vehicle_by("id", $vehicle_id);
			$order_model = new OrdersModel($this->db);
			if($order_model->exists($vehicle_id) === true) {
				$result = ['error' => 'This vehicle already been booked by you!'];
			} else {
				// Add entry to orders table with status pending
				$this->createOrder($_POST);

			}
		} catch(\Exception $ex) {
			$result = ['error' => $ex->getMessage()];
		}
		echo json_encode($result);
	}

	public function createOrder($data)
	{
		$order = new Order(
			Session::get("logged_in_username"),
			(int) $data['vehicle_id'],
			$data['order_price'],
			'pending',
			date("Y-m-d H:i:s")
		);
		$r = null;
		$order_model = new OrdersModel($this->db);
		try {
			$r = $order_model->create($order);
		} catch(Exception $ex) {
			$r = $ex->getMessage();
		}
		return $r;
	}

	public function cart()
	{
		$orders = [];
		$order_model = new OrdersModel($this->db);
		$orders = $order_model->_get([
			"booked_by" =>  Session::get("logged_in_username"),
			"status" => "pending"
		]);
		$totalPrice = 0;
		foreach($orders as $index => $order) {
			$vehicle_model = new VehicleModel($this->db);
			$orders['details'][] = $order;
			$orders['details'][$index]['vehicle'] = $vehicle_model->get_vehicle_by("id", $order['vehicle_id']);
			$totalPrice += (int) $order['order_price'];
		}
		$orders['total_price'] = $totalPrice;
		echo '<pre>';
		print_r($orders);
		echo '</pre>';
		return $this->render("cart.twig", ["orders" => $orders]);
	}

	public function checkout()
	{
		$data = $_POST;
		$result = [];
		$isUpdated = false;
		// verify payment
		$order_ids = explode(",", $_POST['orderIds']);
		foreach( $order_ids as $order_id ) {
			$order_id = (int) trim($order_id);
			$order_model = new OrdersModel($this->db);
			try {
				$order = $order_model->get("id", $order_id);
				$isUpdated = $order_model->update($order_id, [
					'status' => 'paid',
					'payment' => serialize($data)
				]);
			} catch(Exception $ex) {
				$result['error'] = "Error getting order!";
			}
		}
		if($isUpdated) {
			$result['success'] = "Paid";
		} else {
			$result['error'] = "Not paid";
		}
		echo json_encode($result);
	}
}