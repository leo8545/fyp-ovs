<?php

namespace OVS\Domain;

class User {

	private $id;
	private $name;
	private $email;
	private $password;
	private $role;

	public function __construct( string $name, string $email, string $password, string $role ) {
		$this->name = $name;
		$this->email = $email;
		$this->password = $password;
		$this->role = $role;
	}

	public function get_id() : int {
		return $this->id;
	}

	public function set_id( int $id ) {
		$this->id = $id;
	}

	public function get_name() : string  {
		return $this->name;
	}

	public function set_name( string $name ) {
		$this->name = $name;
	}

	public function get_email() : string {
		return $this->email;
	}

	public function set_email( string $email ) {
		$this->email = $email;
	}

	public function get_password() : string {
		return $this->password;
	}

	public function set_password( string $password ) {
		$this->password = $password;
	}
	
	public function get_role() : string {
		return $this->role;
	}

	public function set_role( string $role ) {
		$this->role = $role;
	}
}