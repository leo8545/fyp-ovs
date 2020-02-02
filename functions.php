<?php

use OVS\Utils\Session;

function is_user_logged_in() : bool {
	return Session::get("logged_in") ? true : false;
}

function is_admin() : bool {
	return Session::get("logged_in_as") === "admin" ? true : false;
}