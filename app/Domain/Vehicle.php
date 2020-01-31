<?php

namespace OVS\Domain;

class Vehicle {

	private $id;
	private $name;
	private $price;
	private $year;
	private $description;

	public function get_id() : int {
		return $this->id;
	}
	
	public function get_name() : string {
		return $this->name;
	}
	
	public function set_name( string $name ) {
		$this->name = $name;
	}

	public function get_price() : string {
		return (double) $this->price;
	}

	public function set_price( string $price ) {
		$this->price = $price;
	}

	public function get_description() : string {
		return $this->description;
	}

	public function set_description( string $description ) {
		$this->description = $description;
	}

	public function get_year() : string {
		return $this->year;
	}

	public function set_year( string $year ) {
		$this->year = $year;
	}
}