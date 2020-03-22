<?php

namespace OVS\Domain;

/**
 * Base class for all users
 * 
 * @package OVS
 * @author Sharjeel Ahmad
 * @version 1.0.0
 * @access public
 */
class User {

	private $id;
	private $name;
	private $email;
	private $password;
	private $role;

	public function __construct( string $name, string $email, string $role ) {
		$this->name = $name;
		$this->email = $email;
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
		$this->password = password_hash($password, CRYPT_BLOWFISH);
	}
	
	public function get_role() : string {
		return $this->role;
	}

	public function set_role( string $role ) {
		$this->role = $role;
	}
}