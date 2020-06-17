<?php

namespace OVS\Models;

class OrdersModel extends AbstractModel {

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
	protected $fillable = ["id", "booked_by"];

}