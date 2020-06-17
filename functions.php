<?php
/**
 * This file contains the helper functions used all across the project.
 * 
 * @package OnlineVehicleShowroom
 * @since 1.0.0
 * @author Sharjeel Ahmad Butt <isharjeelahmad@gmail.com>
 */

use OVS\Utils\Session;

/**
 * Sets the default timezone
 */
date_default_timezone_set("Asia/Karachi");

/**
 * Check if a user is logged in
 *
 * @return boolean
 */
function is_user_logged_in() : bool {
	return Session::get("logged_in") ? true : false;
}

/**
 * Check if a logged in user is an admin
 * 
 * @return boolean
 */
function is_admin() : bool {
	return Session::get("logged_in_as") === "admin";
}

/**
 * Check if a user role is a valid one
 *
 * @param string $role User role to check.
 * @return boolean
 */
function is_valid_user_role( string $role ) {
	$allowed_roles = ["admin", "customer", "dealer"];
	if(in_array($role, $allowed_roles)) return true;
	return false;
}

/**
 * Check if two values are equal or not
 *
 * @param string $checked First value
 * @param string $current Second value
 * @return boolean
 */
function checked( $checked, $current ) {
	return (( isset($checked) && $checked === $current ) ? true : false);
}

/**
 * Retrives home url
 *
 * @return string
 */
function home_url() {
	return filter_var("http://" . $_SERVER["SERVER_NAME"], FILTER_SANITIZE_URL);
}

/**
 * Retrives current url if no argument is provided, otherwise url based on arguments provided
 *
 * @param string $request_uri
 * @return string
 */
function url( $request_uri = "" ) {
	$request_uri = empty($request_uri) ? $_SERVER["REQUEST_URI"] : $request_uri;
	return filter_var(home_url() . $request_uri, FILTER_SANITIZE_URL);
}

/**
 * Retrives url to public/assets directory
 *
 * @param string $location The folder/file to access inside public/assets
 * @return string
 */
function assets_url( $location = "" ) {
	return url("/public/assets/$location");
}

/**
 * Retrives url to public/addons directory
 *
 * @param string $location The folder/file to access inside public/addons
 * @return string
 */
function addons_url( $location = "" ) {
	return url("/public/addons/$location");
}

/**
 * Retrives url to uploads directory
 *
 * @param string $location The folder/file to access inside uploads
 * @return string
 */
function uploads_url( $location = "" ) {
	return url("/uploads/$location");
}

/**
 * Show page header
 *
 * @return boolean
 */
function show_page_header() {
	// urls of pages where page header should be hidden
	$hidden = [
		home_url() . '/'
	];
	if( in_array(url(), $hidden) ) {
		return false;
	}
	return true;
}