<?php

namespace OVS\Domain;

class Customer extends User {
	public function __construct(string $name, string $email) {
		parent::__construct($name, $email, "customer");
	}
}