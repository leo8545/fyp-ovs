<?php

namespace OVS\Core;

class Request {

	const GET = "GET";
	const POST = "POST";

	public function __construct() {
		$this->domain = $_SERVER["HTTP_HOST"];
		$this->path = $_SERVER["REQUEST_URI"];
		$this->method = $_SERVER["REQUEST_METHOD"];
	}

	public function get_url() : string {
		return $this->domain . $this->path;
	}

	public function get_domain() : string {
		return $this->domain;
	}

	public function get_path() : string {
		return $this->path;
	}

	public function get_method() : string {
		return $this->method;
	}

	public function is_post() : bool {
		return $this->get_method() === self::POST;
	}

	public function is_get() : bool {
		return $this->get_method() === self::GET;
	}

}