<?php

namespace OVS\Domain;

class Admin extends User {
	public function __construct( string $name, string $email ) {
		parent::__construct( $name, $email, "admin");
	}
}