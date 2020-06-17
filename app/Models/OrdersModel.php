<?php

namespace OVS\Models;

use OVS\Utils\QueryBuilder;
use OVS\Utils\Session;

class OrdersModel extends AbstractModel 
{
	/**
	 * Table name
	 *
	 * @var string
	 * @access protected
	 */
	protected $tableName = "orders";

	/**
	 * Columns name
	 *
	 * @var array
	 * @access protected
	 */
	protected $fillable = ["id", "booked_by", "vehicle_id", "order_price", "status", "booked_at"];

	/**
	 * Checks if an order already exists for the customer against the given vehicle
	 *
	 * @param integer $vehicle_id
	 * @return bool
	 */
	public function exists($vehicle_id)
	{
		$res = $this->_get([
			"booked_by" =>  Session::get("logged_in_username"), 
			"vehicle_id" => $vehicle_id, 
			"status" => "pending"
		]);
		return (count($res) > 0 ? true : false);
	}

	public function update(int $order_id, array $data)
	{
		$query = "UPDATE $this->tableName SET ";
		$qb = new QueryBuilder;
		$update_set = $qb->getUpdateSet(array_keys($data), array_values($data));
		$query .= $update_set . " WHERE $this->primaryKey=$order_id";
		$stmt = $this->db->prepare($query);
		if(!$stmt->execute()) {
			return false;
		}
		return true;
	}

}