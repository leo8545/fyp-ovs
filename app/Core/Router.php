<?php

namespace OVS\Core;

use OVS\Controllers\ErrorController;
use OVS\Utils\DependencyInjector;

/**
 * Base routing class
 * 
 * Routes a request to its controller
 * 
 * @package OVS
 * @author Sharjeel Ahmad
 * @version 1.0.0
 * @access public
 */
class Router {

	/**
	 * Instance of DependecyInjector
	 * 
	 * @var DependencyInjector
	 * @access private 
	 */
	private $di;

	/**
	 * Stores routes from routes.json 
	 *
	 * @var array
	 * @access private
	 */
	private $route_map;

	/**
	 * Patterns of regular expressions 
	 *
	 * @var array
	 * @access private
	 */
	private static $regex_patters = [
		"number" => "\d+",
		"string" => "\w"
	];

	/**
	 * Reads from routes.json and stores it as $route_map
	 * 
	 * @param DependencyInjector $di
	 */
	public function __construct( DependencyInjector $di ) {
		$this->di = $di;
		$json = file_get_contents( __DIR__ . "/../config/routes.json" );
		$this->route_map = json_decode( $json, true );
	}

	/**
	 * Redirect
	 * 
	 * @param string $to Path to redirect to, without slash
	 * @access public
	 */

	static public function redirect(string $to) {
		header("location: /$to");
	}

	/**
	 * Matches route with the path of the given request, executes the controller related to that route
	 * 
	 * @param Request $request
	 * 
	 * @return string Controller's method result
	 */
	public function route( Request $request ) {
		
		$path = $request->get_path();
		foreach( $this->route_map as $route => $info ) {
			$regex_route = $this->get_regex_route( $route, $info );

			if( preg_match( "@^$regex_route$@", $path ) ) {
				return $this->execute_controller( $route, $path, $info, $request );
			}
		}

		$error_controller = new ErrorController( $this->di, $request );
		return $error_controller->not_found();
	}

	/**
	 * Gets the regex version of provided route
	 * 
	 * E.g. The route "vehicle/:id" will be converted to "vehicle/\d+"
	 * 
	 * @param string $route
	 * @param array $info
	 * 
	 * @return string Regex route
	 */
	public function get_regex_route( string $route, array $info ) : string {
		if( isset( $info["params"] ) ) {
			foreach( $info["params"] as $name => $type ) {
				$route = str_replace( ":" . $name, self::$regex_patters[$type], $route );
			}
		}
		
		return $route;
	}

	/**
	 * Executes the controller's method
	 * 
	 * @param string $route 
	 * @param string $path
	 * @param array $info
	 * @param Request $request
	 * 
	 * @return string
	 */

	public function execute_controller( string $route, string $path, array $info, Request $request ) {
		$controller_name = "\OVS\Controllers\\" . $info["controller"] . "Controller";
		$controller = new $controller_name( $this->di, $request );
		
		$params = $this->extract_params( $route, $path );
		return call_user_func_array( [$controller, $info["method"]], $params );

	}

	/**
	 * Extracts arguments of the URL
	 * 
	 * E.g. if we have route as "vehicle/:id" and path as "vehicle/1" then result will be ["id" => 1]
	 * 
	 * @param string $route
	 * @param string $path
	 * 
	 * @return array Associative array of route's parameters
	 */
	public function extract_params(string $route, string $path) : array {
		$params = [];

		$path_parts = explode( "/", $path );
		$route_parts = explode( "/", $route );

		foreach( $route_parts as $key => $route_part ) {
			if( strpos( $route_part, ":" ) === 0 ) {
				$name = substr( $route_part, 1 );
				// $params[$name] = $path_parts[$key + 1];
				$params[$name] = $path_parts[array_key_last($path_parts)];
			}
		}
		return $params;
	}

	/**
	 * Getter for routes map
	 *
	 * @return array
	 */
	public function get_route_map() : array {
		return $this->route_map;
	}

}