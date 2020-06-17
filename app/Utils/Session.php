<?php

namespace OVS\Utils;

class Session {

	static public function init() {
		return session_start();
	}

	static public function get( string $key ) {
		if( !self::started() ) self::init();
		if( isset($_SESSION[$key]) ) {
			return $_SESSION[$key];
		}
		return false;
	}

	static public function set( $key, string $value = "" ) {
		if( !self::started() ) self::init();

		if(is_array($key)) {
			foreach( $key as $session_item_key => $session_item_value ) {
				if( !isset($_SESSION[$session_item_key]) ) {
					$_SESSION[$session_item_key] = $session_item_value;
				}
			}
		} else if (is_string($key)) {
			if(!isset($_SESSION[$key])) {
				$_SESSION[$key] = $value;
			}
		}
	}

	static public function remove( $key ) {
		if( !self::started() ) self::init();

		if( is_array($key) ) {
			foreach( $key as $session_item_key ) {
				if( isset($_SESSION[$session_item_key]) )
					unset( $_SESSION[$session_item_key] );
			}
			return true;
		} else if ( is_string($key) && isset( $_SESSION[$key] ) ) {
			unset( $_SESSION[$key] );
			return true;
		}

		return false;
	}

	static public function status() {
		return session_status();
	}

	static function started() {
		return self::status() == PHP_SESSION_NONE ? false : true;
	}

	static public function all() {
		if( !self::started() ) self::init();
		return $_SESSION;
	}

	static public function kill() {
		session_destroy();
	}

}