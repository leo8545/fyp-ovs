<?php

namespace OVS\Domain;

class Vehicle {

	private $id;
	private $number;
	private $model;
	private $manufacturer;

	public function __construct( $number, $model, $manufacturer ) {
		$this->number = $number;
		$this->model = $model;
		$this->manufacturer = $manufacturer;
	}

	public function get_id() : int {
		return $this->id;
	}

	public function set_id( int $id ) {
		$this->id = $id;
	}
	
	public function get_number() : string {
		return $this->number;
	}
	
	public function set_number( string $number ) {
		$this->number = $number;
	}

	public function get_model() : string {
		return $this->model;
	}

	public function set_model( string $model ) {
		$this->model = $model;
	}

	public function get_manufacturer() : string {
		return $this->manufacturer;
	}

	public function set_manufacturer( string $manufacturer ) {
		$this->manufacturer = $manufacturer;
	}
}