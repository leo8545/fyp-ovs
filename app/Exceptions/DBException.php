<?php
namespace OVS\Exceptions;

use Exception;

class DBException extends Exception {
	public function __construct($message = null) {
		$message = $message ?: "Not found";
		parent::__construct($message);
	}
}