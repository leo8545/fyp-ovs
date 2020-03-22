<?php
/**
 * Globaly accessible functions
 */

use OVS\Utils\Session;

date_default_timezone_set("Asia/Karachi");

function is_user_logged_in() : bool {
	return Session::get("logged_in") ? true : false;
}

function is_admin() : bool {
	return Session::get("logged_in_as") === "admin" ? true : false;
}

function is_valid_user_role( string $role ) {
	$allowed_roles = ["admin", "customer", "dealer"];
	if(in_array($role, $allowed_roles)) return true;
	return false;
}

function checked( $checked, $current ) {
	return (( isset($checked) && $checked === $current ) ? true : false);
}