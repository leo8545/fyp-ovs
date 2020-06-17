<?php

namespace OVS\Domain;

class Order
{
	private $id;
	private $booked_by;
	private $vehicle_id;
	private $order_price;
	private $status;
	private $booked_at;

	public function __construct($booked_by, $vehicle_id, $order_price, $status, $booked_at)
	{
		$this->booked_by = $booked_by;
		$this->vehicle_id = $vehicle_id;
		$this->order_price = $order_price;
		$this->status = $status;
		$this->booked_at = $booked_at;
	}

	public function get_id()
	{
		return $this->id;
	}

	public function set_id(int $id)
	{
		$this->id = $id;
	}

	public function get_booked_by()
	{
		return $this->booked_by;
	}

	public function set_booked_by(string $booked_by)
	{
		$this->booked_by = $booked_by;
	}

	public function get_vehicle_id()
	{
		return $this->vehicle_id;
	}

	public function set_vehicle_id(int $vehicle_id)
	{
		$this->vehicle_id = $vehicle_id;
	}

	public function get_order_price()
	{
		return $this->order_price;
	}

	public function set_order_price(string $order_price)
	{
		$this->order_price = $order_price;
	}

	public function get_status()
	{
		return $this->status;
	}

	public function set_status(string $status)
	{
		$this->status = $status;
	}

	public function get_booked_at()
	{
		return $this->booked_at;
	}

	public function set_booked_at(string $booked_at)
	{
		$this->booked_at = $booked_at;
	}
}