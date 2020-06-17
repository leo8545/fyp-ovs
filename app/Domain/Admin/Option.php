<?php
namespace OVS\Domain\Admin;

class Option {

	private $option_id;
	private $option_name;
	private $option_value;
	
	public function __construct( string $option_name, string $option_value ) {
		$this->option_name = $option_name;
		$this->option_value = $option_value;
	}
	
	public function get_id() : int {
		return $this->option_id;
	}
	
	public function set_id( int $id ) {
		$this->id = $id;
	}
	
	public function get_option_name() : string {
		return $this->option_name;
	}
	
	public function set_option_name( string $option_name ) {
		$this->option_name = $option_name;
	}
	
	public function get_option_value() : string {
		return $this->option_value;
	}
	
	public function set_option_value( string $option_value ) {
		$this->option_value = $option_value;
	}

}