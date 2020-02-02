<?php

namespace OVS\Domain;

class Admin extends User {
	public function __construct( string $name, string $email, string $password ) {
		parent::__construct( $name, $email, $password, "admin");
	}
}